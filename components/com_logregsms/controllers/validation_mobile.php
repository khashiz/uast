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
class LogregsmsControllerValidation_mobile extends JControllerForm
{
	/**
	 * Gets the Data.
	 *
	 * @return string The greeting to be displayed to the user
	 */
	public function step1()
	{
		$config = JComponentHelper::getParams('com_logregsms');
    $shareservice = intval($config->get('shareservice', 0)); 
		$helper = new LRSHelper();
		$mobile = $helper::$_app->input->get('mobilenum', '');
		$referer = $helper::$_app->input->getString('referer', '');
		$valmob_Itemid = $helper::getOneMenu('validation_mobile');
		$valcd_Itemid = $helper::getOneMenu('validation_code');
		$userProf_Itemid = $helper::getOneMenu('profile', 'com_users');

		$user = $helper::User();
		if($user->guest == false) {
			$helper::$_app->enqueueMessage('شما قبلا به سایت وارد شده اید.', 'message');
			$helper::$_app->redirect(JRoute::_('index.php?option=com_users&view=profile&Itemid='.$userProf_Itemid));
			exit;
		}
		
		$validation = LRSHelper::Validation($mobile, 'mobile');
		if($validation['result'] == false) {
			$helper::$_app->enqueueMessage($validation['msg'], 'error');
			$helper::$_app->redirect(JRoute::_('index.php?option=com_logregsms&view=validation_mobile&Itemid='.$valmob_Itemid));
			exit;
		}
		
		$params = $helper::getParams();
		$text = $params->get('smstext', 'کد تاییدیه شما: {code}');
		$username = $params->get('username', '');
		$password = $params->get('password', '');
		$line = $params->get('line', '');
		$reseller = $params->get('reseller', '');
		if(empty($username)) {
			$helper::$_app->enqueueMessage('لطفا از بخش تنظیمات کامپوننت ثبت نام پیامکی ، اطلاعات مربوط به پنل پیامک تان را درج کنید.', 'error');
			$helper::$_app->redirect(JRoute::_('index.php?option=com_logregsms&view=validation_mobile&Itemid='.$valmob_Itemid));
			exit;
		}
		
		// check 0
		//$mobile = ltrim($mobile, '0');

		// create code
		$code = LRSHelper::rndNums(5);
		
		// prepare text
		$data = array('code' => $code);
		$data = LRSHelper::Prepare($text, $data);
				
		// send sms
		$result = LRSSendSms::SendSms ($username, $password, $line, $reseller, $data, $mobile, $code);
		if($shareservice == 0) {
			$sms_result = isset($result['SendSimpleSMS2Result']) ? $result['SendSimpleSMS2Result'] : -1;
		} else {
			if(constant("SHARECONS") === "1") {
				$sms_result = isset($result->SendByBaseNumberResult) ? $result->SendByBaseNumberResult : -1;
			}
		}
		date_default_timezone_set('Iran');
		
		$session = JFactory::getSession();
		$session->set('smsregCode', $code);
		$session->set('smsregMobile', $mobile);
		$session->set('smsregReferer', $referer);
		LRSHelper::Insert(
			array(
				'created_on' => date('Y-m-d'),
				'time' => date('H:i:s'),
				'to' => $mobile,
				'from' => $line,
				'message' => $data,
				'text' => '',
				'result' => $sms_result
			),
			'#__logregsms_smsarchives'
		);
		
        
		LRSHelper::Insert(
			array(
				'created_on' => date('Y-m-d'),
				'mobile' => $mobile,
				'from' => $line,
				'code' => $code,
				'time' => date("Y-m-d H:i:s"),
				'is_confirmed' => -1,
				'state' => 1
			),
			'#__logregsms_confirm'
		);
		
		$helper::$_app->enqueueMessage('کد ورود به سایت به شماره&ensp;'.$mobile.'&ensp;ارسال شد.', 'message');
		$helper::$_app->redirect(JRoute::_('index.php?option=com_logregsms&view=validation_code&Itemid='.$valcd_Itemid));
		exit;
	}
}
