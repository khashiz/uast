<?php
/**
 * @package    Joomla SMS LogRegSms
 * @author     JoominaMarket {@link https://www.joominamarket.com}
 * @author     Created on 11 October 2018
 * @license    GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Plugin class for redirect handling.
 *
 * @since  1.6
 */
class PlgSystemChangeUsernameLogReg extends JPlugin
{
	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	public function onChangeUsernameLogReg($id , $name, $username, $email, $mobile, $referer){
		$session = JFactory::getSession();
        $app = JFactory::getApplication();
        $option = $app->input->get('option', '');
        $view = $app->input->get('view', '');  
        
        if($app->isSite()){

            $valmob_Itemid = $this->getOneMenu('validation_mobile');
            $userProf_Itemid = $this->getOneMenu('profile', 'com_users');
            if ($id && $name && $username && $email && $mobile){ 
                
		        $password = rand(111111,999999); 
                jimport( 'joomla.user.helper' );
                $hashPassword = JUserHelper::hashPassword($password);  
                    
                $updateNulls = true;
                $object = new stdClass();
                $object->id = $id;
                $object->name = $name; 
                $object->username = $mobile; 
                $object->password = $hashPassword; 
                $result = JFactory::getDbo()->updateObject('#__users', $object, 'id', $updateNulls);
                if(!$result){
                    $app->enqueueMessage('مشکلی در بروزرسانی نام کاربری رخ داد.', 'error');
                    $app->redirect(JRoute::_('index.php?option=com_logregsms&view=validation_mobile&Itemid='.$valmob_Itemid));
                }
                
                $helper = new LRSHelper();
                $params = $helper::getParams();
                $menuitem = $params->get('after_login', '');
                 
                 
                // login
                $login = $helper::Login($mobile, $password, true); 
                
                if($login['result'] == true) {
                    $session->clear('smsregMobile');
                    $session->clear('smsregAllowReg');

                    $msg = 'ثبت نام شما با موفقیت انجام شد. شما هم اکنون با نام کاربری '.$mobile.' وارد حساب کاربری خود شده اید.';
                    if($menuitem == "last" && $referer) {
                        $app->enqueueMessage($msg, 'message');
                        $helper::$_app->redirect($referer);
                    }elseif(is_numeric($menuitem)) {
                        $app->enqueueMessage($msg, 'message');
                        $helper::$_app->redirect(JRoute::_('index.php?Itemid='.$menuitem));
                    } else {
                        $app->enqueueMessage($msg, 'message');
                        $helper::$_app->redirect(JRoute::_('index.php?option=com_users&view=profile&Itemid='.$userProf_Itemid));
                    }
                    exit;
                }
                
                
            }
            else{
                $app->enqueueMessage('مشکلی در تغییر نام کاربری رخ داد.', 'error');
                $app->redirect(JRoute::_('index.php?option=com_logregsms&view=validation_mobile&Itemid='.$valmob_Itemid));
            }
		}
		
	}
	
    public function getOneMenu($view = "", $option = 'com_logregsms') {
		$app      = JFactory::getApplication();
		$menus    = $app->getMenu('site');	
		$component  = JComponentHelper::getComponent($option);
		$attributes = array('component_id');
		$values     = array($component->id);	
		$items = $menus->getItems($attributes, $values);
		
		// if this category has no menu 
		foreach( $items as $item){
			if(isset($item->query['view']) && $item->query['view'] == $view)
				return $item->id;
		}

		foreach( $items as $item){
			if(isset($item->query['view']) && $item->query['view'] == 'validation_mobile')
				return $item->id;
		}
		
		$default = $menus->getDefault();
		return !empty($default->id) ? $default->id : null;	
	}// function
}
