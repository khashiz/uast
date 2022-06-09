<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_logreg
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

 
$doc = JFactory::getDocument();
$app = JFactory::getApplication();
$base = JUri::base(true); 
$user     = JFactory::getUser();
$layout = $params->get('layout', 'default');

$doc->addStyleSheet(JURI::root(true) . '/components/com_logregsms/assets/css/logregsms.css');
$doc->addScript(JURI::root(true) . '/components/com_logregsms/assets/js/validation.js');
$moduleclass_sfx = htmlspecialchars($params->get('mod_static_moduleclass_sfx'));
 
$option = $app->input->getString('option', '');
$view = $app->input->getString('view', '');
if($option != "com_logregsms") {
  $doc->addScript(JURI::root(true) . '/components/com_logregsms/assets/js/remodal.js');
  $doc->addStyleSheet(JURI::root(true) . '/components/com_logregsms/assets/css/remodal-default-theme.css');
  $doc->addStyleSheet(JURI::root(true) . '/components/com_logregsms/assets/css/remodal.css');

  require_once(JPATH_SITE.'/components/com_logregsms/message_popup.php');
}

// Logged users must load the logout sublayout
if (!$user->guest)
{
	$layout .= '_logout';
}

require JModuleHelper::getLayoutPath('mod_logreg', $layout);
