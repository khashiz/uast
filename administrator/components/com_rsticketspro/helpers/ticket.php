<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSTicketsProTicketHelper
{
	protected $data = array();
	protected $attachments = array();
	protected $error;
	public $message_id = 0;
	public $ticket_id = 0;

	public static function convert($ticket, $ticketMessages, $params)
	{
		$body          = RSTicketsProHelper::getConfig('kb_template_body');
		$ticketBody    = RSTicketsProHelper::getConfig('kb_template_ticket_body');
		$useEditor     = RSTicketsProHelper::getConfig('allow_rich_editor');
		$dateFormat    = RSTicketsProHelper::getConfig('date_format');
		$showEmailLink = RSTicketsProHelper::getConfig('show_email_link');
		$userInfo      = RSTicketsProHelper::getConfig('show_user_info');

		$table = JTable::getInstance('Kbcontent', 'RsticketsproTable');
		$db    = JFactory::getDbo();

		// Parse ticket message template
		$messages = array();
		foreach ($ticketMessages as $message)
		{
			// get user
			$user = JFactory::getUser($message->user_id);

			// no editor - transform newlines into <br />
			if (!$useEditor)
			{
				$message->message = nl2br($message->message);
			}

			// replacements
			$replacements = array(
				'{message_user}' => $showEmailLink ? '<a href="mailto:' . htmlentities($user->email, ENT_COMPAT, 'utf-8') . '">' . htmlentities($user->{$userInfo}, ENT_COMPAT, 'utf-8') . '</a>' : htmlentities($user->{$userInfo}, ENT_COMPAT, 'utf-8'),
				'{message_user_name}' => htmlentities($user->name, ENT_COMPAT, 'utf-8'),
				'{message_user_username}' => htmlentities($user->username, ENT_COMPAT, 'utf-8'),
				'{message_user_email}' => htmlentities($user->email, ENT_COMPAT, 'utf-8'),
				'{message_date}' => JHtml::_('date', $message->date, $dateFormat),
				'{message_text}' => $message->message
			);

			$messages[] = str_replace(array_keys($replacements), array_values($replacements), $ticketBody);
		}

		// Parse template body
		$replacements = array(
			'{ticket_subject}'    => $ticket->subject,
			'{ticket_department}' => $ticket->department->name,
			'{ticket_date}'       => JHtml::_('date', $ticket->date, $dateFormat),
			'{ticket_messages}'   => implode("\n", $messages)
		);

		return $table->save(array(
			'name'           => $params->name,
			'text'           => str_replace(array_keys($replacements), array_values($replacements), $body),
			'category_id'    => $params->category_id,
			'published'      => $params->publish_article,
			'private'        => $params->private,
			'from_ticket_id' => $ticket->id,
			'ordering'       => $table->getNextOrder($db->qn('category_id') . '=' . $db->q($params->category_id))
		));
	}

	protected static function generateNumber($max = 10)
	{
		$key = '';
		for ($i = 0; $i < $max; $i++)
		{
			$w1 = rand(0, 1);
			$w2 = 1 - $w1;
			$key .= chr($w1 * rand(65, 90) + $w2 * rand(48, 57));
		}

		return $key;
	}

	public static function generateCode($department_id)
	{
		$code = '';
		$db   = JFactory::getDbo();

		$department = RSTicketsProHelper::getDepartment($department_id);

		if ($department->generation_rule == RST_DEPARTMENT_RULE_RANDOM)
		{
			// trick to enter the loop below
			$found = true;
			while ($found)
			{
				// add the department prefix
				$code = $department->prefix . '-' . strtoupper(self::generateNumber(4));

				$query = $db->getQuery(true);
				$query->select($db->qn('id'))
					->from($db->qn('#__rsticketspro_tickets'))
					->where($db->qn('code') . '=' . $db->q($code));
				$db->setQuery($query);
				$found = $db->loadResult();
			}
		}
		elseif ($department->generation_rule == RST_DEPARTMENT_RULE_SEQUENTIAL)
		{
			// add the department prefix
			$code = $department->prefix . '-' . str_pad($department->next_number, 10, 0, STR_PAD_LEFT);

			$department->save(array(
				'id'          => $department->id,
				'next_number' => $department->next_number + 1
			));
		}

		return $code;
	}

	public function bind($data)
	{
		$this->data = $data;
	}

	public function setError($error)
	{
		$this->error = $error;
	}

	public function getError()
	{
		return $this->error;
	}

	public function saveTicket()
	{
		$db = JFactory::getDbo();

		// trigger event before saving and adding user_error
		RSTicketsProHelper::trigger('onBeforeStoreTicket', array($this->data));

		// create user
		if (empty($this->data['customer_id']))
		{
			if (RSTicketsProHelper::getConfig('allow_password_change') && !empty($this->data['password']))
			{
				$password = $this->data['password'];
			}
			else
			{
				$password = JUserHelper::genRandomPassword(8);
			}

			if ($user_id = $this->createUser($password))
			{
				$this->data['customer_id'] = $user_id;
			}
			else
			{
				return false;
			}
		}

		// assign staff member based on department settings
		// unassigned
		$this->data['staff_id'] = 0;

		$department = RSTicketsProHelper::getDepartment($this->data['department_id']);
		// auto-assign to staff member with the least assigned tickets
		if ($department->assignment_type == RST_ASSIGNMENT_AUTO)
		{
			// select staff members that belong to this department
			$query = $db->getQuery(true);
			$query->select($db->qn('user_id'))
				->from($db->qn('#__rsticketspro_staff_to_department'))
				->where($db->qn('department_id') . '=' . $db->q($department->id));
			$db->setQuery($query);
			if ($staff_ids = $db->loadColumn())
			{
				// select groups that can answer tickets
				$query = $db->getQuery(true);
				$query->select($db->qn('id'))
					->from($db->qn('#__rsticketspro_groups'))
					->where($db->qn('answer_ticket') . '=' . $db->q(1));
				$db->setQuery($query);
				if ($group_ids = $db->loadColumn())
				{
                    $priority_ids = array(0, $this->data['priority_id']);

                    $group_ids = array_map('intval', $group_ids);
                    $staff_ids = array_map('intval', $staff_ids);
                    $priority_ids = array_map('intval', $priority_ids);

					$query        = $db->getQuery(true);
					$query->select($db->qn('user_id'))
						->from($db->qn('#__rsticketspro_staff'))
						->where($db->qn('group_id') . ' IN (' . implode(',', $group_ids) . ')')
						->where($db->qn('user_id') . ' IN (' . implode(',', $staff_ids) . ')')
						->where($db->qn('priority_id') . ' IN (' . implode(',', $priority_ids) . ')')
						->where($db->qn('exclude_auto_assign') . ' = ' . $db->q(0));

					$db->setQuery($query);

					if ($staff_ids = $db->loadColumn())
					{
                        $staff_ids = array_map('intval', $staff_ids);

						$query = $db->getQuery(true);
						$query->select($db->qn('staff_id'))
							->select('COUNT(' . $db->qn('id') . ') AS tickets')
							->from($db->qn('#__rsticketspro_tickets'))
							->where($db->qn('status_id') . '!=' . $db->q(RST_STATUS_CLOSED))
							->where($db->qn('staff_id') . ' IN (' . implode(',', $staff_ids) . ')')
							->group($db->qn('staff_id'))
							->order($db->qn('tickets') . ' ' . $db->escape('asc'));
						$db->setQuery($query);
						$stats = $db->loadObjectList('staff_id');

						// must make sure we cover all staff members, even those who don't have tickets yet
						foreach ($staff_ids as $staff)
						{
							if (!isset($stats[$staff]))
							{
								// found a staff member who has 0 tickets - assign
								$staff_id = $staff;
								break;
							}
						}

						// no staff member assigned so far - must grab from query the first result
						if (empty($staff_id) && $stats)
						{
							if ($tmp = reset($stats))
							{
								$staff_id = $tmp->staff_id;
							}
						}

						// get a random staff id from all the members
						if (empty($staff_id))
						{
							$staff_id = $staff_ids[mt_rand(0, count($staff_ids) - 1)];
						}
					}
				}
			}

			if (!empty($staff_id))
			{
				$this->data['staff_id'] = $staff_id;
			}
		}

		// generate code based on department
		$this->data['code'] = RSTicketsProTicketHelper::generateCode($this->data['department_id']);

		// add ticket
		$ticket = JTable::getInstance('Tickets', 'RsticketsproTable');
		if (!$ticket->save($this->data))
		{
			$this->setError($ticket->getError());

			return false;
		}

		$this->ticket_id = $ticket->id;

		// populate new data
		$this->data['ticket_id'] = $ticket->id;
		$this->data['user_id']   = $ticket->customer_id;

		// trigger event after saving ticket
		RSTicketsProHelper::trigger('onAfterStoreTicket', array($this->data, $ticket));

		// let's add custom fields
		$custom_fields_email = '';
		$custom_fields_replacements = array();
		if (!empty($this->data['fields']))
		{
			foreach ($this->data['fields'] as $custom_field_id => $value)
			{
				$table = JTable::getInstance('Customfieldsvalues', 'RsticketsproTable');
				$table->save(array(
					'custom_field_id' => $custom_field_id,
					'ticket_id'       => $ticket->id,
					'value'           => $value
				));

				// append fields to email text
				$field = JTable::getInstance('Customfields', 'RsticketsproTable');
				$field->load($custom_field_id);

				$label = JText::_($field->label);
				$val   = is_array($value) ? implode(', ', $value) : $value;

				$custom_fields_email .= "<p>$label: $val</p>";
				$custom_fields_replacements['{field-' . $field->name . '}'] = $val;
			}
		}

		// let's save the message
		if (!$this->saveMessage(false))
		{
			return false;
		}

		// get email sending settings
		if ($department->email_use_global)
		{
			// are we using global ?
			if (RSTicketsProHelper::getConfig('email_use_global'))
			{
				$config   = JFactory::getConfig();
				$from     = $config->get('mailfrom');
				$fromname = $config->get('fromname');
			}
			else
			{
				$from     = RSTicketsProHelper::getConfig('email_address');
				$fromname = RSTicketsProHelper::getConfig('email_address_fullname');
			}
		}
		else
		{
			$from     = $department->email_address;
			$fromname = $department->email_address_fullname;
		}

		$priority = JTable::getInstance('Priorities', 'RsticketsproTable');
		$priority->load($ticket->priority_id);

		$status = JTable::getInstance('Statuses', 'RsticketsproTable');
		$status->load($ticket->status_id);

		// start sending emails

		// send email to the customer with a copy of his own ticket
		if ($department->customer_send_copy_email)
		{
			if ($email = RSTicketsProHelper::getEmail('add_ticket_customer'))
			{
				$customer = JFactory::getUser($ticket->customer_id);

				$replacements = array(
					'{live_site}'         => JUri::root(),
					'{ticket}'            => RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&id=' . $ticket->id . ':' . JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('customer_itemid')),
					'{customer_name}'     => $customer->name,
					'{customer_username}' => $customer->username,
					'{customer_email}'    => $customer->email,
					'{code}'              => $ticket->code,
					'{subject}'           => $ticket->subject,
					'{priority}'          => JText::_($priority->name),
					'{status}'            => JText::_($status->name),
					'{message}'           => $this->data['message'],
					'{custom_fields}'     => $custom_fields_email,
					'{department_id}'     => $department->id,
					'{department_name}'   => JText::_($department->name)
				);
				$replacements = array_merge($replacements, $custom_fields_replacements);

				$email_subject = '[' . $ticket->code . '] ' . $ticket->subject;
				$email_message = str_replace(array_keys($replacements), array_values($replacements), $email->message);
				$email_message = RSTicketsProHelper::getReplyAbove() . $email_message;

				/**
				 * Grab ticket attachments
				 */
				$files = $this->getTicketAttachments($ticket->id);

				/**
				 * Set this as default to null so we can overwrite
				 * only when it's necessary -> download_type == 'attachment'
				 */
				$attachments = null;

				/**
				 * Check if the setting is activated and if there are files
				 *
				 */
				if ($department->customer_attach_email && !empty($files))
				{
					/**
					 * In case the download type is set to link
					 * we add a list of files to the end of
					 * the email message
					 */
					if ($department->download_type == 'link')
					{
						if ($files) {
							$email_message .= '<ul>';
							foreach ($files as $file) {
								$url = RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&task=ticket.downloadfile&id=' . $file->id . '&access_code=' . md5($ticket->id . '|' . $file->id . '|' . $file->filename));
								$email_message .= '<li><a href="' . $url . '">' . $file->filename . '</a></li>';
							}
							$email_message .= '</ul>';
						}
					}
					/**
					 * if the download_type == 'attachment'
					 * we need to populate $attachments var
					 * with the files
					 */
					else
					{
						$attachments = $this->attachments;
					}
				}

				RSTicketsProHelper::sendMail($from, $fromname, $customer->email, $email_subject, $email_message, 1, $attachments, $department->cc, $department->bcc);
			}
		}

		// send email to the staff member that gets assigned this ticket
		if ($department->staff_send_email && $this->data['staff_id'])
		{
			if ($email = RSTicketsProHelper::getEmail('add_ticket_staff'))
			{
				$customer = JFactory::getUser($this->data['customer_id']);
				$staff    = JFactory::getUser($this->data['staff_id']);

				$replacements = array(
					'{live_site}'         => JUri::root(),
					'{ticket}'            => RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&id=' . $ticket->id . ':' . JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('customer_itemid')),
					'{customer_name}'     => $customer->name,
					'{customer_username}' => $customer->username,
					'{customer_email}'    => $customer->email,
					'{staff_name}'        => $staff->name,
					'{staff_username}'    => $staff->username,
					'{staff_email}'       => $staff->email,
					'{code}'              => $ticket->code,
					'{subject}'           => $ticket->subject,
					'{priority}'          => JText::_($priority->name),
					'{status}'            => JText::_($status->name),
					'{message}'           => $this->data['message'],
					'{custom_fields}'     => $custom_fields_email,
					'{department_id}'     => $department->id,
					'{department_name}'   => JText::_($department->name)
				);
				$replacements = array_merge($replacements, $custom_fields_replacements);

				$email_subject = '[' . $ticket->code . '] ' . $ticket->subject;
				$email_message = str_replace(array_keys($replacements), array_values($replacements), $email->message);
				$email_message = RSTicketsProHelper::getReplyAbove() . $email_message;

				/**
				 * Grab ticket attachments
				 */
				$files = $this->getTicketAttachments($ticket->id);

				/**
				 * Set this as default to null so we can overwrite
				 * only when it's necessary -> download_type == 'attachment'
				 */
				$attachments = null;

				/**
				 * Check if the setting is activated and if there are files
				 *
				 */
				if ($department->staff_attach_email && !empty($files))
				{
					/**
					 * In case the download type is set to link
					 * we add a list of files to the end of
					 * the email message
					 */
					if ($department->download_type == 'link')
					{
						$email_message .= '<ul>';
						foreach ($files as $file)
						{
							$url = RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&task=ticket.downloadfile&id=' . $file->id . '&access_code=' . md5($ticket->id.'|'.$file->id.'|'.$file->filename));
							$email_message .= '<li><a href="' . $url . '">' . $file->filename . '</a></li>';
						}
						$email_message .= '</ul>';
					}
					/**
					 * if the download_type == 'attachment'
					 * we need to populate $attachments var
					 * with the files
					 */
					else
					{
						$attachments = $this->attachments;
					}
				}

				RSTicketsProHelper::sendMail($from, $fromname, $staff->email, $email_subject, $email_message, 1, $attachments, $department->cc, $department->bcc);
			}
		}

		// notify the email addresses configured in the department
		if ($department->notify_new_tickets_to)
		{
			if ($email = RSTicketsProHelper::getEmail('add_ticket_notify'))
			{
				$customer = JFactory::getUser($this->data['customer_id']);
				$staff    = JFactory::getUser($this->data['staff_id']);

				$replacements = array(
					'{live_site}'         => JUri::root(),
					'{ticket}'            => RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&id=' . $ticket->id . ':' . JFilterOutput::stringURLSafe($ticket->subject), true, RSTicketsProHelper::getConfig('customer_itemid')),
					'{customer_name}'     => $customer->name,
					'{customer_username}' => $customer->username,
					'{customer_email}'    => $customer->email,
					'{staff_name}'        => $staff->name,
					'{staff_username}'    => $staff->username,
					'{staff_email}'       => $staff->email,
					'{code}'              => $ticket->code,
					'{subject}'           => $ticket->subject,
					'{priority}'          => JText::_($priority->name),
					'{status}'            => JText::_($status->name),
					'{message}'           => $this->data['message'],
					'{custom_fields}'     => $custom_fields_email,
					'{department_id}'     => $department->id,
					'{department_name}'   => JText::_($department->name)
				);
				$replacements = array_merge($replacements, $custom_fields_replacements);

				$email_subject = '[' . $ticket->code . '] ' . $ticket->subject;
				$email_message = str_replace(array_keys($replacements), array_values($replacements), $email->message);

				$notify_new_tickets_to = str_replace(array("\r\n", "\r"), "\n", $department->notify_new_tickets_to);
				$notify_new_tickets_to = explode("\n", $notify_new_tickets_to);
				foreach ($notify_new_tickets_to as $notify_email)
				{
					$notify_email = trim($notify_email);
					/**
					 * Grab ticket attachments
					 */
					$files = $this->getTicketAttachments($ticket->id);

					/**
					 * Set this as default to null so we can overwrite
					 * only when it's necessary -> download_type == 'attachment'
					 */
					$attachments = null;

					/**
					 * Check if the setting is activated and if there are files
					 *
					 */
					if ($department->staff_attach_email && !empty($files))
					{
						/**
						 * In case the download type is set to link
						 * we add a list of files to the end of
						 * the email message
						 */
						if ($department->download_type == 'link')
						{
							$email_message .= '<ul>';
							foreach ($files as $file)
							{
								$url = RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&task=ticket.downloadfile&id=' . $file->id . '&access_code=' . md5($ticket->id.'|'.$file->id.'|'.$file->filename));
								$email_message .= '<li><a href="' . $url . '">' . $file->filename . '</a></li>';
							}
							$email_message .= '</ul>';
						}
						/**
						 * if the download_type == 'attachment'
						 * we need to populate $attachments var
						 * with the files
						 */
						else
						{
							$attachments = $this->attachments;
						}
					}

					RSTicketsProHelper::sendMail($from, $fromname, $notify_email, $email_subject, $email_message, 1, $attachments, $department->cc, $department->bcc);
				}
			}
		}

		return true;
	}

	protected function getUserByEmail($email)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->qn('#__users'))
			->where($db->qn('email') . ' LIKE ' . $db->q($email));
		$db->setQuery($query);

		return $db->loadObject();
	}

	protected function createUser($password)
	{
		if ($customer = $this->getUserByEmail($this->data['email']))
		{
			return $customer->id;
		}
		else
		{
			$db   = JFactory::getDbo();
			$lang = JFactory::getLanguage();
			$lang->load('com_users', JPATH_ADMINISTRATOR, null, true);

			if ((bool) RSTicketsProHelper::getConfig('emails_as_usernames'))
			{
				$username = $this->data['email'];
			}
			else
			{
				@list($username, $domain) = explode('@', $this->data['email']);

				if (preg_match("#[<>\"'%;()&]#i", $username) || strlen(utf8_decode($username)) < 2)
				{
					$username = JFilterOutput::stringURLSafe($this->data['name']);
					if (strlen($username) < 2)
					{
						$username = str_pad($username, 2, mt_rand(0, 9));
					}
				}

				$found = true;
				while ($found)
				{
					$query = $db->getQuery(true);
					$query->select($db->qn('id'))
						->from($db->qn('#__users'))
						->where($db->qn('username') . ' LIKE ' . $db->q($username));
					$db->setQuery($query);
					$found = $db->loadResult();

					if ($found)
					{
						$username .= mt_rand(0, 9);
					}
				}
			}
			// create user object
			$user = new JUser();

			// Bind the data array to the user object
			$data = array(
				'name'     => trim($this->data['name']) ? JComponentHelper::filterText($this->data['name']) : $this->data['email'],
				'email'    => $this->data['email'],
				'username' => $username,
				'password' => $password
			);
			$data['password2'] = $data['password'];
			if (!$user->bind($data))
			{
				$this->setError($user->getError());

				return false;
			}

			$user->set('groups', array(RSTicketsProHelper::getConfig('user_type')));

			$date = JFactory::getDate();
			$user->set('registerDate', $date->toSql());

			$user->set('block', 0);

			// If there was an error with registration, set the message
			if (!$user->save())
			{
				$this->setError($user->getError());

				return false;
			}

			$this->sendUserEmail($user, $password);

			return $user->id;
		}
	}

	protected function sendUserEmail($user, $password)
	{
		$email = RSTicketsProHelper::getEmail('new_user_email');
		if (!$email)
		{
			return false;
		}
		// disallow control chars in the email
		$password = preg_replace('/[\x00-\x1F\x7F]/', '', $password);

		$lang = JFactory::getLanguage();
		$lang->load('com_rsticketspro', JPATH_SITE);

		// get email sending settings
		// are we using global ?
		if (RSTicketsProHelper::getConfig('email_use_global'))
		{
			$config   = JFactory::getConfig();
			$from     = $config->get('mailfrom');
			$fromname = $config->get('fromname');
		}
		else
		{
			$from     = RSTicketsProHelper::getConfig('email_address');
			$fromname = RSTicketsProHelper::getConfig('email_address_fullname');
		}

		$replacements = array(
			'{live_site}' => JUri::root(),
			'{username}'  => $user->username,
			'{password}'  => $password,
			'{email}'     => $user->email
		);

		// assemble the email data
		try
		{
			return JFactory::getMailer()->sendMail($from, $fromname, $user->email, $email->subject, str_replace(array_keys($replacements), array_values($replacements), $email->message), true);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			return false;
		}
	}

	public function saveMessage($sendMessageEmails = true)
	{
		$isStaff = RSTicketsProHelper::isStaff($this->data['user_id']);

		// skip the email for the customer (as this is intended as his own reply)
		$skip_customer_email = false;
		if ($isStaff && !empty($this->data['reply_as_customer'])) {
			$this->data['submitted_by_staff'] = $this->data['user_id'];
			$this->data['user_id'] = $this->data['customer_id'];
			$skip_customer_email = true;
		}
		// let's see if we need to add a signature
		if ($isStaff && RSTicketsProHelper::getConfig('show_signature') && !empty($this->data['use_signature']) && empty($this->data['reply_as_customer']))
		{
			$signature = RSTicketsProHelper::getSignature($this->data['user_id']);
			if (strlen($signature))
			{
				$this->data['message'] .= "\r\n" . $signature;
			}
		}

		// let's set the message type
		$this->data['html'] = (int) RSTicketsProHelper::getConfig('allow_rich_editor');

		if ($sendMessageEmails)
		{ // This is true only for replies
			RSTicketsProHelper::trigger('onBeforeStoreTicketReply', array($this->data));
		}

		$message = JTable::getInstance('Ticketmessages', 'RsticketsproTable');
		if (!$message->save($this->data))
		{
			$this->setError($message->getError());

			return false;
		}

		if ($sendMessageEmails)
		{ // This is true only for replies
			RSTicketsProHelper::trigger('onAfterStoreTicketReply', array($this->data, $message));
		}

		$this->message_id = $message->id;

		// let's add the files
		$hasFiles = null;
		if (!empty($this->data['files']))
		{
			foreach ($this->data['files'] as $file)
			{
				if ($file['src'] == 'upload')
				{
					$filename = $file['name'];
				}
				elseif ($file['src'] == 'cron')
				{
					$filename = $file['filename'];
				}

				$new_file = JTable::getInstance('Ticketfiles', 'RsticketsproTable');
				$new_file->save(array(
					'ticket_id'         => $this->data['ticket_id'],
					'ticket_message_id' => $message->id,
					'filename'          => $filename
				));

				$hash = md5($new_file->id . ' ' . $message->id);

				if ($file['src'] == 'upload')
				{
					$success = JFile::upload($file['tmp_name'], RST_UPLOAD_FOLDER . '/' . $hash, false, true);
				}
				elseif ($file['src'] == 'cron')
				{
					$success = JFile::write(RST_UPLOAD_FOLDER . '/' . $hash, $file['contents']);
				}

				// store attachment
				if ($success)
				{
					$this->attachments[] = (object) array(
						'path'     => RST_UPLOAD_FOLDER . '/' . $hash,
						'filename' => $filename
					);
				}
			}

			$hasFiles = 1;
		}

		$original = JTable::getInstance('Tickets', 'RsticketsproTable');
		$original->load($this->data['ticket_id']);

		// $isStaff is defined at the start of this function.
		$isCustomer = $original->customer_id == $this->data['user_id'] || !$isStaff;
		// if a customer replied, we don't need to autoclose anymore
		$autocloseSent = $isCustomer ? 0 : null;
		// assign the ticket if the department's assignment type is static and the ticket isn't already assigned
		// if submitted on behalf of another user, don't assign it (this only happens when $sendMessageEmails is set to false - this means it is the first message of the ticket)
		$staffId = $sendMessageEmails && $isStaff && $original->department->assignment_type == RST_ASSIGNMENT_STATIC && !$original->staff_id ? $this->data['user_id'] : null;
		// update the status
		// if customer replied => open
		// if staff replied => on-hold
		// else don't modify the status
		$statusId = $isCustomer ? RST_STATUS_OPEN : ($isStaff ? RST_STATUS_ON_HOLD : null);

		$object = (object) array(
			'id'                  => $this->data['ticket_id'],
			'last_reply'          => $this->data['date'],
			'last_reply_customer' => (int) !$isStaff,
			'autoclose_sent'      => $autocloseSent,
			'staff_id'            => $staffId,
			'has_files'           => $hasFiles,
			'replies'             => $original->replies + 1,
			'status_id'           => $statusId
		);
		JFactory::getDbo()->updateObject('#__rsticketspro_tickets', $object, array('id'));

		// Reload all ticket fields
		$ticket = JTable::getInstance('Tickets', 'RsticketsproTable');
		$ticket->load($ticket->id);

		$department =& $original->department;
		// get email sending settings
		if ($department->email_use_global)
		{
			// are we using global ?
			if (RSTicketsProHelper::getConfig('email_use_global'))
			{
				$config   = JFactory::getConfig();
				$from     = $config->get('mailfrom');
				$fromname = $config->get('fromname');
			}
			else
			{
				$from     = RSTicketsProHelper::getConfig('email_address');
				$fromname = RSTicketsProHelper::getConfig('email_address_fullname');
			}
		}
		else
		{
			$from     = $department->email_address;
			$fromname = $department->email_address_fullname;
		}

		$priority = JTable::getInstance('Priorities', 'RsticketsproTable');
		$priority->load($original->priority_id);

		$status = JTable::getInstance('Statuses', 'RsticketsproTable');
		$status->load($original->status_id);

		// send email to the staff member with the customer's reply
		if ($sendMessageEmails)
		{
			if (!$isStaff && $department->staff_send_email && $original->staff_id)
			{
				if ($email = RSTicketsProHelper::getEmail('add_ticket_reply_staff'))
				{
					$actor = JFactory::getUser($this->data['user_id']);
					$customer = &$original->customer;
					$staff = &$original->staff;
					$ticket_id = $original->id;

					$replacements = array(
						'{live_site}' => JUri::root(),
						'{ticket}' => RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&id=' . $ticket_id . ':' . JFilterOutput::stringURLSafe($original->subject), true, RSTicketsProHelper::getConfig('staff_itemid')),
						'{customer_name}' => $customer->name,
						'{customer_username}' => $customer->username,
						'{customer_email}' => $customer->email,
						'{staff_name}' => $staff->name,
						'{staff_username}' => $staff->username,
						'{staff_email}' => $staff->email,
						'{user_name}' => $actor->name,
						'{user_username}' => $actor->username,
						'{user_email}' => $actor->email,
						'{code}' => $original->code,
						'{subject}' => $original->subject,
						'{priority}' => JText::_($priority->name),
						'{status}' => JText::_($status->name),
						'{message}' => $this->data['message'],
						'{department_id}' => $original->department_id,
						'{department_name}' => JText::_($department->name)
					);

					$email_subject = '[' . $original->code . '] ' . $original->subject;
					$email_message = str_replace(array_keys($replacements), array_values($replacements), $email->message);
					$email_message = RSTicketsProHelper::getReplyAbove() . $email_message;

					$attachments = null;
					/**
					 * Check if the setting is activated and if there are files
					 *
					 */
					if ($department->staff_attach_email && $hasFiles) {
						/**
						 * In case the download type is set to link
						 * we add a list of files to the end of
						 * the email message
						 */
						if ($department->download_type == 'link') {
							$files = $this->getTicketMessageAttachments($this->message_id);
							$email_message .= '<ul>';
							foreach ($files as $file) {
								$url = RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&task=ticket.downloadfile&id=' . $file->id . '&access_code=' . md5($ticket->id . '|' . $file->id . '|' . $file->filename));
								$email_message .= '<li><a href="' . $url . '">' . $file->filename . '</a></li>';
							}
							$email_message .= '</ul>';
						} /**
						 * if the download_type == 'attachment'
						 * we need to populate $attachments var
						 * with the files
						 */
						else {
							$attachments = $this->attachments;
						}
					}

					RSTicketsProHelper::sendMail($from, $fromname, $staff->email, $email_subject, $email_message, 1, $attachments, $department->cc, $department->bcc);
				}
			}
			elseif ($isStaff && $department->customer_send_email && !$skip_customer_email)
			{
				if ($email = RSTicketsProHelper::getEmail('add_ticket_reply_customer'))
				{
					$actor = JFactory::getUser($this->data['user_id']);
					$customer = &$original->customer;
					$staff = $original->staff->id ? $original->staff : $ticket->staff;
					$ticket_id = $original->id;

					$replacements = array(
						'{live_site}' => JUri::root(),
						'{ticket}' => RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&id=' . $ticket_id . ':' . JFilterOutput::stringURLSafe($original->subject), true, RSTicketsProHelper::getConfig('customer_itemid')),
						'{customer_name}' => $customer->name,
						'{customer_username}' => $customer->username,
						'{customer_email}' => $customer->email,
						'{staff_name}' => $staff->name,
						'{staff_username}' => $staff->username,
						'{staff_email}' => $staff->email,
						'{user_name}' => $actor->name,
						'{user_username}' => $actor->username,
						'{user_email}' => $actor->email,
						'{code}' => $original->code,
						'{subject}' => $original->subject,
						'{priority}' => JText::_($priority->name),
						'{status}' => JText::_($status->name),
						'{message}' => $this->data['message'],
						'{department_id}' => $original->department_id,
						'{department_name}' => JText::_($department->name)
					);

					$email_subject = '[' . $original->code . '] ' . $original->subject;
					$email_message = str_replace(array_keys($replacements), array_values($replacements), $email->message);
					$email_message = RSTicketsProHelper::getReplyAbove() . $email_message;

					$attachments = null;
					/**
					 * Check if the setting is activated and if there are files
					 *
					 */
					if ($department->customer_attach_email && $hasFiles) {
						/**
						 * In case the download type is set to link
						 * we add a list of files to the end of
						 * the email message
						 */
						if ($department->download_type == 'link') {
							$files = $this->getTicketMessageAttachments($this->message_id);
							$email_message .= '<ul>';
							foreach ($files as $file) {
								$url = RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&task=ticket.downloadfile&id=' . $file->id . '&access_code=' . md5($ticket->id . '|' . $file->id . '|' . $file->filename));
								$email_message .= '<li><a href="' . $url . '">' . $file->filename . '</a></li>';
							}
							$email_message .= '</ul>';
						} /**
						 * if the download_type == 'attachment'
						 * we need to populate $attachments var
						 * with the files
						 */
						else {
							$attachments = $this->attachments;
						}
					}

					RSTicketsProHelper::sendMail($from, $fromname, $customer->email, $email_subject, $email_message, 1, $attachments, $department->cc, $department->bcc);
				}
			}
		}

		// this works only for customers
		// check if notification email address is not empty
		if (!$isStaff && ($to = RSTicketsProHelper::getConfig('notice_email_address')))
		{
			$to = explode(',', $to);
			if ($original->staff_id)
			{
				$to[] = $original->staff->email;
			}
			// take care of duplicates
			$to = array_unique($to);

			// check if number of max replies is reached
			$maxReplies     = (int) RSTicketsProHelper::getConfig('notice_max_replies_nr');
			$currentReplies = RSTicketsProHelper::getConsecutiveReplies($original->id);
			if ($maxReplies && $currentReplies == $maxReplies && !$original->staff_id)
			{
				if ($email = RSTicketsProHelper::getEmail('notification_max_replies_nr'))
				{
					$actor = JFactory::getUser($this->data['user_id']);
					$customer = JFactory::getUser($this->data['user_id']);
					$ticket_id = $original->id;

					$replacements = array(
						'{live_site}' => JUri::root(),
						'{ticket}' => RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&id=' . $ticket_id . ':' . JFilterOutput::stringURLSafe($original->subject), true, RSTicketsProHelper::getConfig('staff_itemid')),
						'{customer_name}' => $customer->name,
						'{customer_username}' => $customer->username,
						'{customer_email}' => $customer->email,
						'{user_name}' => $actor->name,
						'{user_username}' => $actor->username,
						'{user_email}' => $actor->email,
						'{code}' => $original->code,
						'{subject}' => $original->subject,
						'{priority}' => JText::_($priority->name),
						'{status}' => JText::_($status->name),
						'{message}' => $this->data['message'],
						'{replies}' => $currentReplies,
						'{department_id}' => $original->department_id,
						'{department_name}' => JText::_($department->name)
					);

					$email_subject = str_replace(array_keys($replacements), array_values($replacements), $email->subject);
					$email_message = str_replace(array_keys($replacements), array_values($replacements), $email->message);

					RSTicketsProHelper::sendMail($from, $fromname, $to, $email_subject, $email_message, 1, null, $department->cc, $department->bcc);
				}
			}

			// check if number of max replies with no staff response is reached
			$maxReplies = (int) RSTicketsProHelper::getConfig('notice_replies_with_no_response_nr');
			if ($maxReplies && $currentReplies == $maxReplies && $original->staff_id)
			{
				if ($email = RSTicketsProHelper::getEmail('notification_replies_with_no_response_nr'))
				{
					$actor = JFactory::getUser($this->data['user_id']);
					$customer = JFactory::getUser($this->data['user_id']);
					$staff = &$original->staff;
					$ticket_id = $original->id;

					$replacements = array(
						'{live_site}' => JUri::root(),
						'{ticket}' => RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&id=' . $ticket_id . ':' . JFilterOutput::stringURLSafe($original->subject), true, RSTicketsProHelper::getConfig('staff_itemid')),
						'{customer_name}' => $customer->name,
						'{customer_username}' => $customer->username,
						'{customer_email}' => $customer->email,
						'{staff_name}' => $staff->name,
						'{staff_username}' => $staff->username,
						'{staff_email}' => $staff->email,
						'{user_name}' => $actor->name,
						'{user_username}' => $actor->username,
						'{user_email}' => $actor->email,
						'{code}' => $original->code,
						'{subject}' => $original->subject,
						'{priority}' => JText::_($priority->name),
						'{status}' => JText::_($status->name),
						'{message}' => $this->data['message'],
						'{replies}' => $currentReplies,
						'{department_id}' => $original->department_id,
						'{department_name}' => JText::_($department->name)
					);

					$email_subject = str_replace(array_keys($replacements), array_values($replacements), $email->subject);
					$email_message = str_replace(array_keys($replacements), array_values($replacements), $email->message);

					RSTicketsProHelper::sendMail($from, $fromname, $to, $email_subject, $email_message, 1, null, $department->cc, $department->bcc);
				}
			}

			// check if it has restricted words
			if ($keywords = RSTicketsProHelper::getConfig('notice_not_allowed_keywords'))
			{
				$keywords  = explode(',', $keywords);
				$email     = RSTicketsProHelper::getEmail('notification_not_allowed_keywords');

				if ($email)
				{
					$actor = JFactory::getUser($this->data['user_id']);
					$customer = JFactory::getUser($this->data['user_id']);
					$staff = $original->staff->id ? $original->staff : $ticket->staff;
					$ticket_id = $original->id;

					$quotedWords = array();
					foreach ($keywords as $word) {
						$word = trim($word);
						if (strlen($word)) {
							$quotedWords[] = preg_quote($word);
						}
					}
					$pattern = '#\b(' . implode('|', $quotedWords) . ')\b#i';
					if (preg_match($pattern, $this->data['message'])) {
						$replacements = array(
							'{live_site}' => JUri::root(),
							'{ticket}' => RSTicketsProHelper::mailRoute('index.php?option=com_rsticketspro&view=ticket&id=' . $ticket_id . ':' . JFilterOutput::stringURLSafe($original->subject), true, RSTicketsProHelper::getConfig('staff_itemid')),
							'{customer_name}' => $customer->name,
							'{customer_username}' => $customer->username,
							'{customer_email}' => $customer->email,
							'{staff_name}' => $staff->name,
							'{staff_username}' => $staff->username,
							'{staff_email}' => $staff->email,
							'{user_name}' => $actor->name,
							'{user_username}' => $actor->username,
							'{user_email}' => $actor->email,
							'{code}' => $original->code,
							'{subject}' => $original->subject,
							'{priority}' => JText::_($priority->name),
							'{status}' => JText::_($status->name),
							'{message}' => preg_replace($pattern, '<b style="color: red">$1</b>', $this->data['message']),
							'{replies}' => $currentReplies,
							'{department_id}' => $original->department_id,
							'{department_name}' => JText::_($department->name)
						);

						$email_subject = str_replace(array_keys($replacements), array_values($replacements), $email->subject);
						$email_message = str_replace(array_keys($replacements), array_values($replacements), $email->message);

						RSTicketsProHelper::sendMail($from, $fromname, $to, $email_subject, $email_message, 1, null, $department->cc, $department->bcc);
					}
				}
			}
		}

		return true;
	}

	public function getMessageId()
	{
		return $this->message_id;
	}

	public function getTicketId()
	{
		return $this->ticket_id;
	}

	public function getTicketAttachments($ticketid){
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->qn('#__rsticketspro_ticket_files'))
			->where($db->qn('ticket_id') . ' = ' . $db->q($ticketid));
		$db->setQuery($query);
		return $db->loadObjectList('id');
	}
	
	public function getTicketMessageAttachments($message_id){
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->qn('#__rsticketspro_ticket_files'))
			->where($db->qn('ticket_message_id') . ' = ' . $db->q($message_id));
		$db->setQuery($query);
		return $db->loadObjectList('id');
	}

	public static function getTicketTimeState($ticketid) {
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('start'))
			->select($db->qn('end'))
			->from($db->qn('#__rsticketspro_timespent'))
			->where($db->qn('ticket_id') . ' = ' . $db->q($ticketid))
			->order($db->qn('id') . ' DESC');

		$db->setQuery($query, 0, 1);
		$result = $db->loadObject();

		if (empty($result)) {
			return false;
		}

		$time_data = new stdClass();
		$time_data->state = false;

		// return true (started) only when the start is set and the end is not
		if ($result->start != '0000-00-00 00:00:00' && $result->end == '0000-00-00 00:00:00') {
			$time_data->state =  true;
		}

		$time_data->start = $result->start;
		$time_data->end = $result->end;

		return $time_data;
	}
}