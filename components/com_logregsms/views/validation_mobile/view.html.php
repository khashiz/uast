<?php

/**
 * @package    smsarchive
 * @subpackage C:
 * @author     Mohammad Hosein Mir {@link https://joomina.ir}
 * @author     Created on 22-Feb-2019
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');


/**
 * HTML View class for the smsarchive Component.
 *
 * @package    smsarchive
 * @subpackage Views
 */
class logregsmsViewValidation_Mobile extends JViewLegacy
{
  /**
   * @var
   */
  protected $data;

  /**
   * smsarchive view display method.
   *
   * @param string $tpl The name of the template file to parse;
   *
   * @return void
   */
  public function display($tpl = null)
  {
    $helper = new LRSHelper();
    $reg_Itemid = $helper::getOneMenu('registration');
		$userProf_Itemid = $helper::getOneMenu('profile', 'com_users');
    $session = JFactory::getSession();
    $smsregAllowReg = $session->get('smsregAllowReg', null);
    if ($smsregAllowReg === "1") {
      $helper::$_app->enqueueMessage('لطفا ثبت نام خود را انجام دهید.', 'notice');
      $helper::$_app->redirect(JRoute::_('index.php?option=com_logregsms&view=registration&Itemid='.$reg_Itemid));
      exit;
    }

    $user = $helper::User();
    if ($user->guest == false) {
      $helper::$_app->enqueueMessage('شما قبلا به سایت وارد شده اید.', 'message');
      $helper::$_app->redirect(JRoute::_('index.php?option=com_users&view=profile&Itemid='.$userProf_Itemid));
      exit;
    }

    $this->params = LRSHelper::getParams();

    parent::display($tpl);
  }
}
