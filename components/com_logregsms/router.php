<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jshop
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Routing class from com_jshop
 *
 * @package     Joomla.Site
 * @subpackage  com_jshop
 * @since       3.3
 */
class LogregSmsRouter extends JComponentRouterBase
{
	
	public $_db = null;
	
	public $_params = null;
	
	public function __construct() {
		parent::__construct();
		$this->_db = JFactory::getDbo();
	}
	/*
	 * Build the route for the com_content component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */
	public function build(&$query)
	{		
		$segments = array();
		$limit = 0;
		
		// Get a menu item based on Itemid or currently active
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		
		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if(empty($query['Itemid'])){
			$menuItem = $menu->getDefault();
		} else{
			$menuItem = $menu->getItem($query['Itemid']);
		}

		if(isset($query['view'])){
			$view = $query['view'];
		} else{
			return $segments;
		}
		
		/*
		 * validation_mobile
		*/
		if ($view == "validation_mobile"){
			unset($query['view']);
		}
		
	
		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$item = $menu->getActive();
		$vars = array();
		$count = count($segments);

		return $vars;
	}// function

}// class

/**
 * Content router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function JShopBuildRoute(&$query)
{
	//$router = new JShopRouter();
	//return $router->build($query);
}

function JShopParseRoute($segments)
{
	//$router = new JShopRouter();
	//return $router->parse($segments);
}
