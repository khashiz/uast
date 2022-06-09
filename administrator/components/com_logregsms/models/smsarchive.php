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


jimport('joomla.application.component.modeladmin');

/**
 * logregsms Model.
 *
 * @package    logregsms
 * @subpackage Models
 */
class logregsmsModelSmsArchive extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string     $type  The table type to instantiate
     * @param string     $prefix A prefix for the table class name.
     * @param array      $config Configuration array for model.
     *
     * @internal param \The $type table type to instantiate
     * @return JTable A database object
     */
    public function getTable($type = 'SmsArchive', $prefix = 'logregsmsTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return mixed A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_logregsms.smsarchive', 'logregsms'
        , array('control' => 'jform', 'load_data' => $loadData));

        if(empty($form))
        {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return mixed The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()
        ->getUserState('com_logregsms.edit.smsarchive.data');

        if(empty($data))
        {
            $data = $this->getItem();
        }

        return $data;
    }
}
