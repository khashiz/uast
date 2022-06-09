<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RsticketsproModelKbarticle extends JModelAdmin
{
	public function getTable($type = 'Kbcontent', $prefix = 'RsticketsproTable', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_rsticketspro.kbarticle', 'kbarticle', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}

		return $form;
	}
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_rsticketspro.edit.kbarticle.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
	
	public function getTicket()
	{
		$item = $this->getItem();
		if ($item->from_ticket_id)
		{
			$table = JTable::getInstance('Tickets', 'RsticketsproTable');
			if ($table->load($item->from_ticket_id))
			{
				return $table;
			}
			else
			{
				return false;
			}
		}

		return false;
	}
	
	protected function getReorderConditions($table)
	{
		return array(
			'category_id = '.(int) $table->category_id
		);
	}

	protected function canDelete($record)
	{
		return JFactory::getUser()->authorise('kbarticle.delete', 'com_rsticketspro');
	}

	protected function canEditState($record)
	{
		return JFactory::getUser()->authorise('kbarticle.edit.state', 'com_rsticketspro');
	}
}