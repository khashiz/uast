<?php
/**
 * @package    logregsms
 * @subpackage C:
 * @author     Mohammad Hosein Miri {@link https://joomina.ir}
 * @author     Created on 22-Feb-2019
 * @license    GNU/GPL
 */
 
//-- No direct access
defined('_JEXEC') || die('=;)');

define("SHARECONS","1");

JLoader::register('LRSHelper', JPATH_ADMINISTRATOR.'/components/com_logregsms/helpers/logregsms.php');
JLoader::register('LRSSendSms', JPATH_ADMINISTRATOR.'/components/com_logregsms/classes/sendsms.php');
$s = new LRSHelper();

JHtml::_('jquery.framework');

$params = LRSHelper::getParams();
if($params->get('load_bootstrap', '0') == "1") {
	$s::$_doc->addStyleSheet(JURI::root(true) . '/components/com_logregsms/assets/css/bootstrap.min.css');
}

/* $s::$_doc->addStyleSheet(JURI::root(true) . '/components/com_logregsms/assets/css/logregsms.css'); */
/* $s::$_doc->addScript(JURI::root(true) . '/components/com_logregsms/assets/js/remodal.js'); */
/* $s::$_doc->addStyleSheet(JURI::root(true) . '/components/com_logregsms/assets/css/remodal-default-theme.css'); */
/* $s::$_doc->addStyleSheet(JURI::root(true) . '/components/com_logregsms/assets/css/remodal.css'); */
$s::$_doc->addScript(JURI::root(true) . '/components/com_logregsms/assets/js/validation.js');

$controller	= JControllerLegacy::getInstance('logregsms');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();

/* require_once(JPATH_SITE.'/components/com_logregsms/message_popup.php'); */
return;