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


//-- Import the class JControllerForm
jimport('joomla.application.component.controllerform');

/**
 * logregsms Controller.
 *
 * @package    logregsms
 * @subpackage Controllers
 */
class logregsmsControllerSmsArchive extends JControllerForm
{
    /**
     * !!!
     * If our controller does not follow the standard pluralisation
     * we have to provide the name here
     *
     * @var string
     */
    protected $view_list = 'smsarchives';
}
