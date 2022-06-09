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
 * logregsms Model.
 *
 * @package    logregsms
 * @subpackage Models
 */
class logregsmsModellogregsms extends JModelLegacy
{
    /**
     * Gets the Data.
     *
     * @return string The greeting to be displayed to the user
     */
    public function getData()
    {
        $id = JFactory::getApplication()->input->getInt('id');
        $db = JFactory::getDBO();

        $query = 'SELECT a.*, cc.title AS category '
        . ' FROM #__logregsms AS a '
        . ' LEFT JOIN #__categories AS cc ON cc.id = a.catid '
        . ' WHERE a.id = '.$id;

        $db->setQuery($query);
        $data = $db->loadObject();

        return $data;
    }
}
