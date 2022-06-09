<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RsticketsproViewDashboard extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->params		= JFactory::getApplication()->getParams('com_rsticketspro');
		$this->user			= JFactory::getUser();
		$this->categories	= $this->get('categories');
		$this->tickets		= $this->get('tickets');
		$this->login_link	= JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode((string) JUri::getInstance()));
		$this->kb_itemid	= (int) $this->params->get('kb_itemid');
		$this->search_link  = RSTicketsProHelper::route('index.php?option=com_rsticketspro&view=knowledgebase' . (empty($this->kb_itemid) ? '&layout=results' : '&Itemid=' . $this->kb_itemid));
		$this->itemid       = JFactory::getApplication()->input->getInt('Itemid',0);

		$this->prepareDocument();

		parent::display($tpl);
	}
	
	protected function prepareDocument()
	{
		// Description
		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		// Keywords
		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		// Robots
		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
	
	public function trim($string, $max = 255, $more='...')
	{
		return RSTicketsProHelper::shorten($string, $max, $more);
	}
}