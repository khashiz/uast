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

if( ! JFactory::getUser()->authorise('core.manage', 'com_logregsms'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

JLoader::register('LRSHelper', JPATH_COMPONENT.'/helpers/logregsms.php');

$controller	= JControllerLegacy::getInstance('logregsms');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
