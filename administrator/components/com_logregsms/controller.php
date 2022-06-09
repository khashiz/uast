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
 * logregsms default Controller.
 *
 * @package    logregsms
 * @subpackage Controllers
 */
class logregsmsController extends JControllerLegacy
{
    /**
     * Method to display the view.
     *
     * @param bool $cachable
     * @param bool $urlparams
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = false)
    {
	    $input = JFactory::getApplication()->input;

        //-- Setting the default view
        $input->set('view', $input->get('view', 'smsarchives'));

        parent::display($cachable, $urlparams);
    }
}
