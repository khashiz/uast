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
class logregsmsViewSmsArchives extends JViewLegacy
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
			$this->state = $this->get('State');
      $this->items = $this->get('Items');
			$this->pagination = $this->get('Pagination');
        
      $this->addToolBar();

      //Get companie options
			//JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

			//$car = JFormHelper::loadFieldType('Car', false);
			//$this->carOption = $car->getOptions(); // works only if you set your field getOptions on public!!
        
        $errors = $this->get('Errors');
        if(count($errors))
        {
            JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');
            return;
        }

        parent::display($tpl);
        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
		
      JToolBarHelper::title(JText::_('لیست پیامک های ارسال شده') , 'smsarchives');
		
			$canDo = LRSHelper::getActions();

      /*if ($canDo->get('core.create'))
			{
				JToolbarHelper::addNew('smsarchive.add');
			}

		if ($canDo->get('core.edit') || $canDo->get('core.edit.own')) {
			JToolBarHelper::editList('smsarchive.edit');
		}
		
		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('smsarchives.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('smsarchives.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}*/
		
		if($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'smsarchives.delete');
		}
		
	    JToolbarHelper::preferences('com_logregsms');
		
        JFactory::getDocument()->addStyleDeclaration(
       '.icon-48-logregsms'
       .' {background-image: url(components/com_logregsms/assets/images/com_logregsms-48.png)}');
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        JFactory::getDocument()->setTitle('لیست پیامک های ارسال شده');
    }
}
