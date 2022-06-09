<?php

/**
 * @package    Joomla SMS Registration
 * @author     Joominamarket {@link https://www.joominamarket.com}
 * @author     Created on 11 October 2018
 * @license    GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

class LRSSendSms
{		
	public static function SendSms($username, $password, $smsline, $reseller, $text, $to, $code) {
		
		$config = JComponentHelper::getParams('com_logregsms');
		$shareservice = intval($config->get('shareservice', 0)); 
		$shareservice_sms_text = $config->get('shareservice_sms_text', '');
		
		LRSHelper::loadNuSoap();
		
		if($shareservice == 0) {
			$client = new nusoap_client('http://'.$reseller.'/post/send.asmx?wsdl',true);
			$err = $client->getError();
			if ($err) 
			{
				 echo 'Constructor error' . $err;
			}

			$parameters['username'] = $username; 
			$parameters['password'] = $password; 
			$parameters['to'] = $to;
			$parameters['from'] = $smsline; 
			$parameters['text'] = $text;
			$parameters['isflash'] =false;

			$res = $client->call('SendSimpleSMS2', $parameters);  
		} else {
			if(constant("SHARECONS") === "1") {
				$client = new SoapClient('http://'.$reseller.'/post/send.asmx?wsdl', array('encoding'=>'UTF-8'));
				$parameters['username'] = $username;
				$parameters['password'] = $password;
				$parameters['to'] = $to;
				$parameters['text'] = array($code);
				$parameters['bodyId'] = $shareservice_sms_text;
				$res =  $client->SendByBaseNumber($parameters);
			}
		}
		
		return $res;
		//return array('SendSimpleSMS2Result'=> '1555');
	}// function
		
}// class
?>