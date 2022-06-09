<?php
/**
 * @package    logregsms
 * @subpackage C:
 * @author     Mohammad Hosein Mir {@link https://joomina.ir}
 * @author     Created on 22-Feb-2019
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
class logregsmsViewSmsArchive extends JViewLegacy
{
    /**
     * @var
     */
    protected $item;

    /**
     * @var JForm
     */
    protected $form;

    /**
     * @var string
     */
    protected $script;
    /**
     * logregsmss view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        $this->script = $this->get('Script');

        $errors = $this->get('Errors');

        if(count($errors))
        {
            JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');

            return;
        }

        $this->addToolBar();

        parent::display($tpl);

        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);

        JToolBarHelper::title($isNew
                ? JText::_('جدید')
                : JText::_('ویرایش')
            , 'smsarchive');

        JToolBarHelper::save('smsarchive.save');

        JToolBarHelper::cancel('smsarchive.cancel'
            , $isNew
                ? 'JTOOLBAR_CANCEL'
                : 'JTOOLBAR_CLOSE');

        JFactory::getDocument()->addStyleDeclaration(
            '.icon-48-smsarchive {background-image: url('
                .'components/com_logregsms/assets/images/com_logregsms-48.png);}'
        );
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $isNew = ($this->item->id < 1);

        $document = JFactory::getDocument();

        $document->setTitle($isNew
            ? JText::_('ایجاد')
            : JText::_('ویرایش'));

        $document->addScript(JURI::root(true).$this->script);

        $document->addScript(JURI::root(true)
            .'/administrator/components/com_logregsms/views/smsarchive/submitbutton.js');

        JText::script('خطای غیر قابل منتظره');
    }
}
