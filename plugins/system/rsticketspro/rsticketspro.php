<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * RSTickets! Pro System Plugin
 */
class plgSystemRsticketspro extends JPlugin
{
	/**
	 *
	 */
	public function onAfterInitialise()
	{
		/**
		 * No point in running if RSTickets!Pro is not installed
		 */
		if (!$this->canRun())
		{
			return;
		}

		$lang = JFactory::getLanguage();
		$lang->load('com_rsticketspro', JPATH_ADMINISTRATOR, 'en-GB', true);
		$lang->load('com_rsticketspro', JPATH_ADMINISTRATOR, $lang->getDefault(), true);
		$lang->load('com_rsticketspro', JPATH_ADMINISTRATOR, null, true);

		$config = RSTicketsProConfig::getInstance();

		if ($config->get('autoclose_enabled'))
		{
			if ($config->get('autoclose_cron_lastcheck') + $config->get('autoclose_cron_interval') * 60 < JFactory::getDate()->toUnix())
			{
				$this->setAutocronLastCheck('autoclose');

				if ($config->get('autoclose_automatically'))
				{
					$this->autoNotifyTickets($config->get('autoclose_interval'));
				}

				$this->closeNotifiedTickets($config->get('autoclose_interval'));
			}
		}

		if ($config->get('enable_followup'))
		{
			if ($config->get('followup_cron_lastcheck') + $config->get('followup_cron_interval') * 60 < JFactory::getDate()->toUnix())
			{
				$this->setAutocronLastCheck('followup');

				$this->sendFollowUp($config->get('followup_enabled_time'), $config->get('followup_interval'));
			}
		}

	}

	/**
	 * @param $interval
	 */
	protected function closeNotifiedTickets($interval)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$date  = JFactory::getDate()->toUnix() - ($interval * 86400);

		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rsticketspro_tickets'))
			->where($db->qn('status_id') . ' <> ' . $db->q(2))
			->where($db->qn('autoclose_sent') . ' > ' . $db->q(0))
			->where($db->qn('autoclose_sent') . ' < ' . $db->q($date));
		$db->setQuery($query);
		if ($tickets = $db->loadColumn())
		{
			/**
			 * Joomla\Utilities\ArrayHelper::toInteger should be used
			 */
            $tickets = array_map('intval', $tickets);
			foreach ($tickets as $ticket_id)
			{
				RSTicketsProHelper::addHistory($ticket_id, 'autoclose', 0);
				RSTicketsProHelper::saveSystemMessage($ticket_id, array(
					'type' => 'autoclose',
					'days' => $interval
				), false);
			}

			$query->clear()
				->update($db->qn('#__rsticketspro_tickets'))
				->set($db->qn('status_id') . ' = ' . $db->q(2))
				->set($db->qn('closed') . ' = ' . $db->q(JFactory::getDate()->toSql()))
				->where($db->qn('id') . ' IN (' . $this->implodeArray($tickets) . ')');

			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * @param $interval
	 */
	protected function autoNotifyTickets($interval)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->clear()
			->select('id')
			->from($db->qn('#__rsticketspro_tickets'))
			->where($db->qn('status_id') . ' != ' . $db->q(2))
			->where($db->qn('last_reply_customer') . ' = ' . $db->q(0))
			->where($db->qn('autoclose_sent') . ' = ' . $db->q(0))
			->where('DATE_ADD(' . $db->qn('last_reply') . ', INTERVAL ' . (int) $interval . ' DAY) < ' . $db->q(JFactory::getDate()->toSql()));
		$db->setQuery($query, 0, 5);
		$ids = $db->loadColumn();

		if (!empty($ids))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsticketspro/models/ticket.php';
			$model = new RsticketsproModelTicket;
			foreach ($ids as $id)
			{
				$model->notify($id);
			}
		}
	}

	/**
	 * @param $startingTime
	 * @param $interval
	 */
	protected function sendFollowUp($startingTime, $interval)
	{
		$date  = JFactory::getDate()->toSql();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		/**
		 * Get the tickets that are in the interval
		 * defined in the Configuration,
		 * but were closed after the option was enabled
		 */
		$query->clear()
			->select(array('id', 'department_id'))
			->from($db->qn('#__rsticketspro_tickets'))
			->where($db->qn('status_id') . ' = ' . $db->q(2))
			->where('DATE_ADD(' . $db->qn('closed') . ', INTERVAL ' . (int) $interval . ' DAY) < ' . $db->q($date))
			->where($db->qn('closed') . ' > ' . $db->q($startingTime))
			->where($db->qn('followup_sent') .' = ' . $db->q(0))
			->where($db->qn('feedback') . ' = ' . $db->q(0));
		$db->setQuery($query, 0, 5);

		$tickets = $db->loadAssocList('id');

		/**
		 * If there is any match,
		 * we need to send the followup email
		 */
		if (!empty($tickets))
		{
			require_once JPATH_ADMINISTRATOR . '/components/com_rsticketspro/models/ticket.php';
			require_once JPATH_ADMINISTRATOR . '/components/com_rsticketspro/helpers/emails.php';

			$model = new RsticketsproModelTicket;
			$ticketArray = array();

			foreach ($tickets as $ticket)
			{
				// original ticket
				$original = $model->getTicket($ticket['id']);
				RSTicketsProEmailsHelper::sendEmail('feedback_followup_email', array(
					'ticket'        => $original,
					'department_id' => $original->department_id
				));

				$ticketArray[] = $ticket['id'];
			}

			$query->clear()
				->update($db->qn('#__rsticketspro_tickets'))
				->set($db->qn('followup_sent') . ' = ' . $db->q(1))
				->where($db->qn('id') . ' IN (' . $this->implodeArray($ticketArray) . ')');
			$db->setQuery($query);
			$db->execute();
		}
	}

	/**
	 * @param $type
	 */
	protected function setAutocronLastCheck($type)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->clear()
			->update($db->qn('#__rsticketspro_configuration'))
			->set($db->qn('value') . ' = ' . $db->q(JFactory::getDate()->toUnix()))
			->where($db->qn('name') . ' = ' . $db->q($type . '_cron_lastcheck'));

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * @param $array
	 *
	 * @return string
	 */
	protected function implodeArray($array)
	{
		$db = JFactory::getDbo();

		foreach ($array as $i => $value)
		{
			$array[$i] = $db->q($value);
		}

		return implode(',', $array);
	}

    public function onContentPrepareForm(JForm $form, $data)
    {
        $context = $form->getName();
        if ($context !== 'com_users.user')
        {
            return;
        }

        if (!empty($data->id))
        {
            $this->loadLanguage();
            JForm::addFormPath(__DIR__);
            $form->loadFile('rsticketspro_anonymise', false);
        }
    }

	/**
	 * @return bool
	 */
	protected function canRun()
	{
		$helper = JPATH_ADMINISTRATOR . '/components/com_rsticketspro/helpers/rsticketspro.php';

		if (class_exists('RSTicketsProHelper'))
		{
			return true;
		}
		else
		{
			if (file_exists($helper))
			{
				require_once $helper;

				return true;
			}
		}

		return false;
	}
}