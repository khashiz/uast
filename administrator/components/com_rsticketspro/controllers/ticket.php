<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RsticketsproControllerTicket extends JControllerLegacy
{
	protected $option = 'com_rsticketspro';
	protected $context = 'ticket';

    public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('reopen', 'changeTicketStatus');
		$this->registerTask('close', 'changeTicketStatus');
	}

	protected function getLoginLink()
	{
		$link = base64_encode((string) JUri::getInstance());

		return RSTicketsProHelper::route('index.php?option=com_users&view=login&return=' . $link, false);
	}

	protected function getListingLink()
	{
		return RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=tickets', false);
	}

	public function flag()
	{
		$app           = JFactory::getApplication();
		$cid           = $app->input->getInt('cid');
		$flagged       = $app->input->getInt('flagged');
		$model         = $this->getModel('ticket');

		// logged in?
		if ($model->isGuest())
		{
			return $this->setRedirect($this->getLoginLink());
		}
		// only staff members can call this
		if (!$model->isStaff())
		{
			$app->close();
		}

		// check permissions for the ticket
		if (!$model->hasPermission($cid))
		{
            $app->enqueueMessage($model->getError(), 'warning');

			return $this->setRedirect($this->getListingLink());
		}

		@ob_end_clean();
		$model->setFlag($cid, $flagged);

		echo '1';

		$app->close();
	}

	public function rate()
	{
		$app         = JFactory::getApplication();
		$cid         = $app->input->getInt('cid');
		$rating      = $app->input->getInt('rating');
		$access_code = $app->input->get('access_code');

		$model = $this->getModel('ticket');

		if (strlen($access_code))
		{
			$ticket   = $model->getTicket($cid);
			$customer = JFactory::getUser($ticket->customer_id);
			
			if ((int) $ticket->feedback != 0)
			{
				$app->redirect(JUri::root(), JText::_('RST_EMAIL_ALREADY_RATED'));
			}

			if ($access_code !== md5($ticket->id . ' | ' . $customer->email))
			{
				throw new Exception(JText::_('RST_EMAIL_ACCESS_CODE_INCORRECT'), 403);
			}

			$model->setRating($cid, $rating);

			$app->redirect(JUri::root(), JText::_('RST_FEEDBACK_RECEIVED_FROM_EMAIL'));
		}
		else
		{
			// logged in?JText::_('RST_YOU_HAVE_TO_BE_LOGGED_IN')
			if ($model->isGuest())
			{
				throw new Exception(JText::_('RST_YOU_HAVE_TO_BE_LOGGED_IN'), 403);
			}
			// no point in trying to rate when config doesn't allow it
			if (!RSTicketsProHelper::getConfig('show_ticket_voting'))
			{
				$app->close();
			}
			// only customers can call this
			if ($model->isStaff())
			{
				$app->close();
			}
			// check permissions for the ticket
			if (!$model->hasPermission($cid))
			{
				throw new Exception($model->getError(), 403);
			}

			@ob_end_clean();
			$model->setRating($cid, $rating);
			echo '1';

			$app->close();
		}

	}

	public function delete()
	{
		$app = JFactory::getApplication();
		$cid = $app->input->getInt('cid');

		$model = $this->getModel('ticket');
		// logged in?
		if ($model->isGuest())
		{
			return $this->setRedirect($this->getLoginLink());
		}
		// only staff members can call this
		if (!$model->isStaff())
		{
			throw new Exception(JText::_('RST_CANNOT_DELETE_TICKETS'), 403);
		}
		if (!$model->hasPermission($cid))
		{
            $app->enqueueMessage($model->getError(), 'warning');

			return $this->setRedirect($this->getListingLink());
		}
		$permissions = $model->getStaffPermissions();
		if ($permissions->delete_ticket)
		{
			$model->delete($cid);
			$this->setMessage(JText::_('RST_TICKET_DELETED_OK'));
		}
		else
		{
			$this->setMessage(JText::sprintf('RST_TICKET_NOT_DELETED', $cid), 'error');
		}
		$this->setRedirect($this->getListingLink());
	}

	public function notify()
	{
		// this is called only when autoclose is enabled
		if (!RSTicketsProHelper::getConfig('autoclose_enabled'))
		{
			return $this->setRedirect($this->getListingLink());
		}
		$app = JFactory::getApplication();
		$cid = $app->input->getInt('cid');

		$model = $this->getModel('ticket');
		// logged in?
		if ($model->isGuest())
		{
			return $this->setRedirect($this->getLoginLink());
		}
		// only staff members can call this
		if (!$model->isStaff())
		{
			throw new Exception(JText::_('RST_CANNOT_NOTIFY_TICKETS'), 403);
		}
		if (!$model->hasPermission($cid))
		{
            $app->enqueueMessage($model->getError(), 'warning');

			return $this->setRedirect($this->getListingLink());
		}

		$model->notify($cid);
		$this->setMessage(JText::_('RST_TICKET_NOTIFIED_OK'));
		$this->setRedirect($this->getListingLink());
	}

	public function bulkUpdate()
	{
		$app  = JFactory::getApplication();
		$cids = $app->input->get('cid', array(), 'array');

		$model = $this->getModel('ticket');
		// logged in?
		if ($model->isGuest())
		{
			return $this->setRedirect($this->getLoginLink());
		}
		// only staff members can call this
		if (!$model->isStaff())
		{
			throw new Exception(JText::_('RST_CANNOT_UPDATE_TICKETS'), 403);
		}

		$department_id = $app->input->getInt('bulk_department_id', 0);
		$staff_id    = $app->input->getInt('bulk_staff_id', -1);
		$priority_id = $app->input->getInt('bulk_priority_id');
		$status_id   = $app->input->getInt('bulk_status_id');
		$notify      = $app->input->getInt('bulk_notify');
		$delete      = $app->input->getInt('bulk_delete');

		// no point notifying if autoclose is disabled
		if (!RSTicketsProHelper::getConfig('autoclose_enabled'))
		{
			$notify = 0;
		}

		// get staff member permissions
		$permissions = $model->getStaffPermissions();

		foreach ($cids as $cid)
		{
			// first, let's make sure this ticket can be opened by the current user
			if ($model->hasPermission($cid))
			{
				// if we are deleting tickets then it doesn't make any sense to check the other options
				if ($delete)
				{
					// check for delete permission & if ticket has been deleted
					if (!$permissions->delete_ticket || !$model->delete($cid))
					{
						$app->enqueueMessage(JText::sprintf('RST_TICKET_NOT_DELETED', $cid), 'error');
					}
				}
				else
				{
					$data = array();

					// can assign?
					if ($permissions->assign_tickets && $staff_id > -1)
					{
						$data['staff_id'] = $staff_id;
					}

					// can update ticket information?
					if ($permissions->update_ticket)
					{
						if ($priority_id)
						{
							$data['priority_id'] = $priority_id;
						}
						if ($status_id)
						{
							$data['status_id'] = $status_id;
						}
					}

					if ($permissions->move_ticket)
					{
						$data['department_id'] = $department_id;
					}

					if ($data)
					{
						$model->updateInfo($cid, $data);
					}

					// let's see if we need to notify as well
					if ($notify)
					{
						$model->notify($cid);
					}
				}
			}
		}

		if ($delete)
		{
			$this->setMessage(JText::_('RST_TICKETS_DELETED_OK'));
		}
		else
		{
			$this->setMessage(JText::_('RST_TICKETS_UPDATED_OK'));
			if ($notify)
			{
				$this->setMessage(JText::_('RST_TICKET_NOTIFIED_OK'));
			}
		}

		$this->setRedirect($this->getListingLink());
	}

	// used to update custom fields
	public function updateFields()
	{
		$app   = JFactory::getApplication();
		$cid   = $app->input->getInt('cid');
		$data  = $app->input->get('rst_custom_fields', array(), 'array');
		$model = $this->getModel('ticket');
		$url   = JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($cid), false);

		// logged in?
		if ($model->isGuest())
		{
			return $this->setRedirect($this->getLoginLink());
		}
		// only staff members can call this
		if (!$model->isStaff())
		{
			throw new Exception(JText::_('RST_CANNOT_UPDATE_TICKET'), 403);
		}
		if (!$model->hasPermission($cid))
		{
            $app->enqueueMessage($model->getError(), 'warning');

			return $this->setRedirect($this->getListingLink());
		}
		$permissions = $model->getStaffPermissions();
		if (!$permissions->update_ticket_custom_fields)
		{
            $app->enqueueMessage(JText::_('RST_CANNOT_UPDATE_TICKET'), 'warning');

			return $this->setRedirect($url);
		}

		$model->updateFields($cid, $data);

		$this->setMessage(JText::_('RST_TICKET_UPDATED_OK'));
		$this->setRedirect($url);
	}

	// used to update ticket information
	public function updateInfo()
	{
		$app   = JFactory::getApplication();
		$cid   = $app->input->getInt('cid');
		$data  = $app->input->get('ticket', array(), 'array');
		$model = $this->getModel('ticket');
		$url   = JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($cid), false);

		// logged in?
		if ($model->isGuest())
		{
			return $this->setRedirect($this->getLoginLink());
		}
		// only staff members can call this
		if (!$model->isStaff())
		{
			throw new Exception(JText::_('RST_CANNOT_UPDATE_TICKET'), 403);
		}
		if (!$model->hasPermission($cid))
		{
            $app->enqueueMessage($model->getError(), 'warning');

			return $this->setRedirect($this->getListingLink());
		}

		// get permissions
		$permissions = $model->getStaffPermissions();

		// check permissions to update the ticket information
		if (!$permissions->update_ticket)
		{
			unset($data['subject']);
			unset($data['priority_id']);
		}

		// check permissions to move to another department
		if (!$permissions->move_ticket)
		{
			unset($data['department_id']);
		}

		// check permissions to change ticket status
		if (!$permissions->change_ticket_status)
		{
			unset($data['status_id']);
		}

		// check permissions to assign tickets
		if (!$permissions->assign_tickets)
		{
			unset($data['staff_id']);
		}

		if (!RSTicketsProHelper::getConfig('show_alternative_email'))
		{
			unset($data['alternative_email']);
		}

		// check permissions to change customer
		// no permissions at all
		if (!$permissions->add_ticket_customers && !$permissions->add_ticket_staff && !$permissions->add_ticket)
		{
			unset($data['customer_id']);
			unset($data['alternative_email']);
		}
		else
		{
			$user     = JFactory::getUser();
			$customer = JFactory::getUser($data['customer_id']);
			$is_staff = RSTicketsProHelper::isStaff($customer->get('id'));

			// cannot change to himself...
			if ($customer->id == $user->id && !$permissions->add_ticket)
			{
				unset($data['customer_id']);
			}

			// cannot change to another staff member
			if ($customer->id != $user->id && $is_staff && !$permissions->add_ticket_staff)
			{
				unset($data['customer_id']);
			}

			// cannot change to another customer
			if ($customer->id != $user->id && !$is_staff && !$permissions->add_ticket_customers)
			{
				unset($data['customer_id']);
			}
		}

		$model->updateInfo($cid, $data);

		$this->setMessage(JText::_('RST_TICKET_UPDATED_OK'));
		$this->setRedirect($url);
	}

	public function toggleTime()
	{
		$app         = JFactory::getApplication();
		$ticket_id   = $app->input->getInt('id');
		$state  	 = $app->input->getInt('tstate', 1);
		$model       = $this->getModel('ticket');
		$ticket_data = $model->getTicket($ticket_id);

		try
		{
			// logged in?
			if ($model->isGuest())
			{
				return $this->setRedirect($this->getLoginLink());
			}

			// not enabled
			if (!RSTicketsProHelper::getConfig('enable_time_spent'))
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}

			if (RSTicketsProHelper::getConfig('time_spent_type') !== 'tracking')
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}

			// only staff members can call this
			if (!$model->isStaff())
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}
			if (!$model->hasPermission($ticket_id))
			{
				$app->enqueueMessage($model->getError(), 'warning');

				return $this->setRedirect($this->getListingLink());
			}

			if (!empty($ticket_data) && $ticket_data->status_id != RST_STATUS_CLOSED)
			{
				if ($model->toggleTime($ticket_id, $state))
				{
					$app->enqueueMessage(JText::_('COM_RSTICKETSPRO_TIME_TOGGLE'.($state ? '_STARTED' : '_STOPPED')));
				}
				else
				{
					$app->enqueueMessage($model->getError(), 'warning');
				}
			}

			$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($ticket_id), false));
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($ticket_id), false));
		}
	}

	public function clearTimeTrackingEntry()
	{
		$app         = JFactory::getApplication();
		$ticket_id   = $app->input->getInt('ticket_id');
		$entry_id   = $app->input->getInt('entry');
		$model       = $this->getModel('ticket');

		try
		{
			// logged in?
			if ($model->isGuest())
			{
				return $this->setRedirect($this->getLoginLink());
			}
			// not enabled
			if (!RSTicketsProHelper::getConfig('enable_time_spent'))
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}
			if (RSTicketsProHelper::getConfig('time_spent_type') !== 'tracking')
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}
			// only staff members can call this
			if (!$model->isStaff())
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}

			// check to see if this ticket has this time spent entry
			if (!$model->checkIfExistsTimeSpentEntry($ticket_id, $entry_id))
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}

			// only allowed staff members can delete the time tracking history
			if (!$model->canDeleteTimeTracking() && !$model->canDeleteOwnTimeTracking($ticket_id, $entry_id)) {
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}


			if (!$model->hasPermission($ticket_id))
			{
				$app->enqueueMessage($model->getError(), 'warning');

				return $this->setRedirect($this->getListingLink());
			}

			// clear the own history entry
			$model->clearTimeTracking($ticket_id, $entry_id);
			$app->enqueueMessage(JText::_('COM_RSTICKETSPRO_TIME_TRACKING_HISTORY_CLEAR_OWN_SUCCESS'));

			$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($ticket_id), false));
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($ticket_id), false));
		}
	}

	public function clearTimeTracking()
	{
		$app         = JFactory::getApplication();
		$ticket_id   = $app->input->getInt('id');
		$model       = $this->getModel('ticket');

		try
		{
			// logged in?
			if ($model->isGuest())
			{
				return $this->setRedirect($this->getLoginLink());
			}
			// not enabled
			if (!RSTicketsProHelper::getConfig('enable_time_spent'))
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}
			if (RSTicketsProHelper::getConfig('time_spent_type') !== 'tracking')
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}
			// only staff members can call this
			if (!$model->isStaff())
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}

			// only allowed staff members can delete the time tracking history
			if (!$model->canDeleteTimeTracking()) {
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}

			if (!$model->hasPermission($ticket_id))
			{
				$app->enqueueMessage($model->getError(), 'warning');

				return $this->setRedirect($this->getListingLink());
			}

			// clear the history
			$model->clearTimeTracking($ticket_id);
			$app->enqueueMessage(JText::_('COM_RSTICKETSPRO_TIME_TRACKING_HISTORY_CLEAR_SUCCESS'));

			$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($ticket_id), false));
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($ticket_id), false));
		}
	}

	public function saveTimeSpent()
	{
		$app        = JFactory::getApplication();
		$cid        = $app->input->getInt('cid');
		$data       = $app->input->get('ticket', array(), 'array');
		$time_spent = $data['time_spent'];
		$model      = $this->getModel('ticket');

		try
		{
			// logged in?
			if ($model->isGuest())
			{
				return $this->setRedirect($this->getLoginLink());
			}
			// not enabled
			if (!RSTicketsProHelper::getConfig('enable_time_spent'))
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}
			if (RSTicketsProHelper::getConfig('time_spent_type') !== 'input')
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}
			// only staff members can call this
			if (!$model->isStaff())
			{
				throw new Exception(JText::_('RST_CANNOT_UPDATE_TIME_SPENT'), 403);
			}
			if (!$model->hasPermission($cid))
			{
				$app->enqueueMessage($model->getError(), 'warning');

				return $this->setRedirect($this->getListingLink());
			}

			$table = $model->getTable();

			$table->save(array(
				'id'         => $cid,
				'time_spent' => $time_spent
			));

			$this->setMessage(JText::_('RST_TIME_SPENT_UPDATED_OK'));
			$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($cid), false));
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($cid), false));
		}
	}

	public function cancel()
	{
		$this->setRedirect($this->getListingLink());
	}

	public function changeTicketStatus()
	{
		$app   = JFactory::getApplication();
		$model = $this->getModel('ticket');
		$id    = $app->input->getInt('id');
		$task  = $app->input->get('task');

		$permissions = $model->getStaffPermissions();

		if ($task == 'reopen')
		{
			$canChangeStatus = ($model->isStaff() && $permissions->change_ticket_status) || (!$model->isStaff() && RSTicketsProHelper::getConfig('allow_ticket_reopening'));
			$status_id       = RST_STATUS_OPEN;
			$successMsg      = JText::_('RST_TICKET_REOPENED_OK');
			$errorMsg        = JText::_('RST_CANNOT_REOPEN_TICKET');
		}
		elseif ($task == 'close')
		{
			$canChangeStatus = ($model->isStaff() && $permissions->change_ticket_status) || (!$model->isStaff() && RSTicketsProHelper::getConfig('allow_ticket_closing'));
			$status_id       = RST_STATUS_CLOSED;
			$successMsg      = JText::_('RST_TICKET_CLOSED_OK');
			$errorMsg        = JText::_('RST_CANNOT_CLOSE_TICKET');
		}

		if ($model->hasPermission($id) && $canChangeStatus)
		{
			$model->updateInfo($id, array(
				'status_id' => $status_id
			));

			// if the ticket is closed and by any chan
			if ($status_id == RST_STATUS_CLOSED) {
				$model->toggleTime($id, 0);
			}

			$this->setMessage($successMsg);
			$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($id), false));
		}
		else
		{
			throw new Exception($errorMsg, 403);
		}
	}

	public function reply()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app     = JFactory::getApplication();
		$input   = $app->input;
		$data    = $input->get('ticket', array(), 'array');
		$id      = $input->getInt('id');
		$files   = $input->files->get('ticket', null, 'raw');
		$model   = $this->getModel('ticket');
		$ticket  = $model->getTicket($id);
		$context = "$this->option.edit.$this->context";

		if ($ticket->status_id == RST_STATUS_CLOSED)
		{
            $app->enqueueMessage(JText::_('RST_TICKET_REPLIES_CLOSED_ERROR'), 'warning');

			return $this->setRedirect($this->getListingLink());
		}

		if (!$model->hasPermission($id))
		{
            $app->enqueueMessage($model->getError(), 'warning');

			return $this->setRedirect($this->getListingLink());
		}
		// overwrite some options
		$data['id']        = null;
		$data['user_id']   = JFactory::getUser()->id;
		$data['date']      = JFactory::getDate()->toSql();
		$data['ticket_id'] = $id;
		if ($app->isClient('administrator'))
        {
            $data['consent'] = array(1);
        }
		if (!$model->reply($id, $data, is_array($files) && isset($files['files']) ? $files['files'] : array()))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $data);

			$this->setMessage($model->getError(), 'error');
		}
		else
		{
			// Clear the data in the session
			$app->setUserState($context . '.data', null);

			$this->setMessage(JText::_('RST_TICKET_SUBMIT_REPLY_OK', 'info'));
		}

		$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=ticket&id=' . RSTicketsProHelper::sef($id), false));
	}

	public function downloadFile()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$id    = $input->getInt('id');

		$model = $this->getModel('ticket');
		$file  = JTable::getInstance('Ticketfiles', 'RsticketsproTable');

		// check if file exists
		if (!$file->load($id) || !$file->id)
		{
			throw new Exception(JText::_('RST_CANNOT_DOWNLOAD_FILE_NOT_EXIST'), 500);
		}

		// check if ticket can be opened by the user
		$ticket = $model->getTicket($file->ticket_id);
		if (!$ticket || !$ticket->id)
		{
			throw new Exception(JText::_('RST_CANNOT_DOWNLOAD_FILE'), 403);
		}

		if ($access_code = JFactory::getApplication()->input->get('access_code', ''))
		{
			if (!$model->hasDownloadPermission($access_code, $file->id, $ticket->id))
			{
				throw new Exception(JText::_('RST_CANNOT_DOWNLOAD_FILE'), 403);
			}
		}
		else
		{
			if (!$model->hasPermission($file->ticket_id))
			{
				throw new Exception(JText::_('RST_CANNOT_DOWNLOAD_FILE'), 403);
			}
		}

		$path = $file->getRealPath();
		if (!file_exists($path))
		{
			throw new Exception(JText::_('RST_CANNOT_DOWNLOAD_FILE_NOT_EXIST'), 500);
		}

		// increment downloads
		$file->hit();

		@ob_end_clean();

		header("Cache-Control: public, must-revalidate");
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		header("Expires: 0");
		header("Content-Description: File Transfer");
		header("Expires: Sat, 01 Jan 2000 01:00:00 GMT");
		header("Content-Type: application/octet-stream; charset=utf-8");
		header("Content-Length: " . (string) filesize($path));
		header('Content-Disposition: attachment; filename="' . $file->filename . '"');
		header("Content-Transfer-Encoding: binary\n");
		@readfile($path);

		$app->close();
	}
}