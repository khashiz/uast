<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('user');

class JFormFieldRSCustomer extends JFormFieldUser
{
	public $type = 'RSCustomer';
	
	protected function getGroups()
	{
		return null;
	}

	protected function getInput()
	{
		$this->readonly = false;
		return str_replace('?option=com_users', '?option=com_rsticketspro', parent::getInput());
	}
}