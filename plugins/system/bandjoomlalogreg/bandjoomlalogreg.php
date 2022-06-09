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
class PlgSystemBandJoomlaLogReg extends JPlugin
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

	public function onBeforeRender(){
		if (!JComponentHelper::isEnabled('com_logregsms', true)){ 
			JFactory::getApplication()->enqueueMessage('کامپوننت لاگین و ثبت نام پیامکی جوملا یافت نشد.', 'error');
		}
		else{
			$app = JFactory::getApplication();
			$option = $app->input->get('option', '');
			$view = $app->input->get('view', '');  
			if($app->isClient('site')){
				if($option == 'com_users' && ($view == 'registration' || $view == 'login')){
					$valmob_Itemid = $this->getOneMenu('validation_mobile');
					$app->redirect(JRoute::_('index.php?option=com_logregsms&view=validation_mobile&Itemid='.$valmob_Itemid));
				}
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
