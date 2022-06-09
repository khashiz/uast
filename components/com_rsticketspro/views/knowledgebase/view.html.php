<?php
/**
 * @package    RSTickets! Pro
 *
 * @copyright  (c) 2010 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RsticketsproViewKnowledgebase extends JViewLegacy
{
	protected $hot_hits = 0;
	
	public function display($tpl = null)
	{
		$mainframe		= JFactory::getApplication();
		$this->params	= $mainframe->getParams('com_rsticketspro');
		$layout			= $this->getLayout();
		
		if ($layout == 'results')
		{
			$this->items		= $this->get('results');
			$this->pagination	= $this->get('resultspagination');
			$this->word			= $this->get('resultsword');
		}
		else
		{
			$this->categories		= $this->get('categories');
			$this->items			= $this->get('content');
			$this->pagination		= $this->get('contentpagination');
			$this->sortColumn		= $this->get('sortcolumn');
			$this->sortOrder		= $this->get('sortorder');
			$this->filter_word		= $this->get('filterword');
			$this->category			= $this->get('category');
			$this->cid				= $mainframe->input->getInt('cid',0);
			$this->is_filter_active = (strlen($this->filter_word) > 0);
		}

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
		
		// Add meta information from category, if one has been requested
		if (!empty($this->category))
		{
			if (!empty($this->category->meta_description))
			{
				$this->document->setMetaData('description', $this->category->meta_description);
			}
			if (!empty($this->category->meta_keywords))
			{
				$this->document->setMetaData('keywords', $this->category->meta_keywords);
			}
		}
		
		// Pathway
		if ($path = $this->get('path'))
		{
			$pathway = JFactory::getApplication()->getPathway();

			foreach ($path as $item)
			{
				$pathway->addItem($item->name, $item->link);
			}
		}
	}
	
	public function isHot($hits)
	{
		if (empty($this->hot_hits))
		{
			$this->hot_hits = RSTicketsProHelper::getConfig('kb_hot_hits');
		}
		
		return $hits >= $this->hot_hits;
	}
}