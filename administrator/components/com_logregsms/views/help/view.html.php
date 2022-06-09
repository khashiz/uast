<?php
/**
 * @package    logregsms
 * @subpackage F:
 * @author     Mohammad Miri {@link joominamarket.com}
 * @author     Created on 21-Oct-2016
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');


/**
 * HTML View class for the logregsms Component.
 *
 * @package    logregsms
 * @subpackage Views
 */
class logregsmsViewHelp extends JViewLegacy
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var JPagination
     */
    protected $pagination;
    /**
     * logregsmsList view display method
     *
     * @param null $tpl
     *
     * @return void
     */
    public function display($tpl = null)
    {
		
			$this->params = LRSHelper::getParams();
      
        parent::display($tpl);
        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
      JToolBarHelper::title(JText::_('راهنما') , 'help');
	    JToolbarHelper::preferences('com_logregsms');
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
       JFactory::getDocument()->setTitle('راهنما');
    }
}
