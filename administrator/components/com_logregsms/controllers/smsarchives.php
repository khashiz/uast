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


//-- Import the Class JControllerAdmin
jimport('joomla.application.component.controlleradmin');

/**
 * logregsms Controller.
 */
class logregsmsControllerSmsArchives extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 */
	public function getModel(
		$name = 'SmsArchive',
		$prefix = 'logregsmsModel',
		$config = array('ignore_request' => true)
	) {
		$doSomething = 'here';

		return parent::getModel($name, $prefix, $config);
	}
	public function delete()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;

		$cid = $jinput->get('cid', array(), 'ARRAY');
		$cids = implode(',', $cid);

		$db = JFactory::getdbo();
		$db->setQuery("DELETE FROM `#__logregsms_smsarchives` WHERE id IN(" . $cids . "); ");
		$db->execute();

		$app->enqueueMessage('موارد حذف شدند', 'message');
		$app->redirect("index.php?option=com_logregsms&view=smsarchives");
	}
}
