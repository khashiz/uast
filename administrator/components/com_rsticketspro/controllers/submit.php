<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RsticketsproControllerSubmit extends JControllerLegacy
{
	protected $option  = 'com_rsticketspro';
	protected $context = 'submit';
	
	public function showForm()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=submit', false));
	}
	
	public function cancel()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=tickets', false));
	}
	
	public function save()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$app    	= JFactory::getApplication();
		$input  	= $app->input;
		$data    	= $input->get('jform', array(), 'array');
		$fields  	= $input->get('rst_custom_fields', array(), 'array');
		$files	 	= $input->files->get('jform', null, 'raw');
		$model   	= $this->getModel('submit');
		$context 	= "$this->option.edit.$this->context";
		$redirect	= RSTicketsProHelper::getConfig('submit_redirect');
		
		if (!$model->save($data, $fields, is_array($files) && isset($files['files']) ? $files['files'] : array()))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $data);
			$app->setUserState($context . '.fields', $fields);
			
			$this->setMessage($model->getError(), 'error');
		}
		else
		{
			// Clear the data in the session
			$app->setUserState($context . '.data', null);
			$app->setUserState($context . '.fields', null);
			
			$this->setMessage(JText::_('RST_TICKET_SUBMIT_OK'));
			
			if ($app->isClient('site') && !empty($redirect))
			{
				return $this->setRedirect($redirect);
			}
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_rsticketspro&view=submit', false));
	}
}