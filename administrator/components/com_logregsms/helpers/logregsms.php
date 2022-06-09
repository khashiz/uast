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
 * logregsms Helper.
 *
 * @package    logregsms
 * @subpackage Helpers
 */
class LRSHelper
{

	public static $_db = NULL;

	public static $_app = NULL;

	public static $_JApplication = NULL;

	public static $_doc = NULL;

	public static $_user = NULL;

	function __construct()
	{

		// get database Instance
		if (empty(self::$_db)) {
			self::$_db 		= JFactory::getDbo();
		}

		// get document
		if (empty(self::$_doc)) {
			self::$_doc 		= JFactory::getDocument();
		}

		// get document
		if (empty(self::$_app)) {
			self::$_app 		= JFactory::getApplication();
		}

		// get document
		if (empty(self::$_user)) {
			self::$_user 		= self::User();
		}
	} // __construct
	/**
	 * Configure the Linkbar.
	 *
	 * @param string $viewName The name of the active view.
	 */
	public static function addToolbar($viewName = 'smsarchives')
	{

		$jinput = JFactory::getApplication()->input;
		// get form values
		$view = $jinput->get('view', '0', 'STRING');

		JHtmlSidebar::addEntry(
			JText::_('لیست پیامک های ارسال شده'),
			'index.php?option=com_logregsms&view=smsarchives',
			$view == 'smsarchives'
		);

		JHtmlSidebar::addEntry(
			JText::_('راهنما'),
			'index.php?option=com_logregsms&view=help',
			$view == 'help'
		);
	}

	public static function addFilter($text, $key, $options, $noDefault = false)
	{
		JHtmlSidebar::addFilter($text, $key, $options, $noDefault);
	}
	
	public static function getActions()
	{
		$user = self::User();
		$result = new JObject;

		$assetName = 'com_logregsms';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	} // function

	public static function render()
	{
		return JHtmlSidebar::render();
	}
	// load nusoap client
	// place on classes folder in administrator
	public static function loadNuSoap()
	{
		if (!class_exists('nusoap_client')) {
			require_once(JPATH_ADMINISTRATOR . '/components/com_logregsms/classes/nusoap.php');
		}
	}

	/* 
	 * Login to site
	*/
	public function Login($username, $password, $remember_me)
	{

		$result = array('result' => false, 'msg' => '');

		// get params
		$params = self::getParams();

		// Application
		$app = JFactory::getApplication('site');

		// convert $remember_me to boolean
		if (!is_bool($remember_me)) {
			if (intval($remember_me) === 1) {
				$remember_me = true;
			} else {
				$remember_me = false;
			}
		}

		// get user from #__users
		$user = self::FindUserName($username);
		if (empty($user)) {
			$result['msg'] = 'متاسفانه نام کاربری درج شده در سامانه یافت نشد!';
			return $result;
		}

		if ($user->activation === "0") {
			$user->activation = "";
		}

		if ($user->block == "1" || !empty($user->activation)) {
			$result['msg'] = 'حساب کاربری شما مسدود می باشد. لطفا با پشتیبانی تماس بگیرید';
			return $result;
		}

		// Credentials = Username & Password
		$credentials = array();
		$credentials['username'] = self::CleanUTF8_ToEn($username);
		$credentials['password'] = self::CleanUTF8_ToEn($password);

		// Options
		$options = array('remember' => $remember_me);

		// Do login
		$successful = $app->login($credentials, $options);
		if (!$successful) {
			$result['msg'] = 'کلمه عبور اشتباه می باشد، لطفا مجدد با دقت بیشتری وارد کنید.';
			return $result;
		}

		$result['msg'] = 'با نام کاربری ' . $username . ' با موفقیت وارد حساب کاربری شده اید.';
		$result['result'] = true;

		return $result;
	} // function

	/*
	 * Prepare text for send email OR sms
	*/
	public static function Prepare($text, $data)
	{
		if (is_object($data)) {
			$data = (array)$data;
		}

		foreach ($data as $key => $replace) {
			$search = "{" . $key . "}";
			if (strpos($text, $search) !== false) {
				$text = str_replace($search, $replace, $text);
			}
		}

		return $text;
	} // function

	/*
	 * Delete Row(s)
	 * By condition
	*/
	public static function Delete($conditions, $dbName)
	{
		$db = self::$_db;
		$query = $db->getQuery(true);

		$query->delete($db->quoteName($dbName));
		$query->where($conditions);

		$db->setQuery($query);
		$result = $db->execute();

		return $result;
	} // function

	public static function RegisterUser($name, $email, $username, $mobile)
	{

		// check username and email if exists
		$check = self::getUser(0, $username);
		if (!empty($check)) {
			self::$_app->enqueueMessage("نام کاربری قبلا انتخاب شده است. لطفا یک نام کاربری دیگری انتخاب کنید.", 'error');
			return null;
		}

		$check = null;
		$check = self::getUser(0, null, $email);
		if (!empty($check)) {
			self::$_app->enqueueMessage("ایمیل انتخاب شده از قبل درون سامانه استفاده شده است. لطفا یک آدرس ایمیل دیگری را وارد کنید.", 'error');
			return null;
		}

		$juser = new JUser;
		$data = array();

		$data['name'] = self::CleanUTF8_ToEn($name);
		$data['email'] = JStringPunycode::emailToPunycode(self::CleanUTF8_ToEn($email));
		$data['password'] = self::CleanUTF8_ToEn($mobile);
		$data['username'] = self::CleanUTF8_ToEn($username);
		$data['activation'] = '';
		$data['block'] = 0;
		$data['sendEmail'] = 0;

		// Bind the data.
		if (!$juser->bind($data)) {
			self::$_app->enqueueMessage($juser->getError(), 'error');
			return null;
		}

		if (!$juser->save()) {
			self::$_app->enqueueMessage($juser->getError(), 'error');
			return null;
		}

		// set mobile into #__fields_values
		// get mobile field id
		$field = self::getJoomlaField('mobile', 'com_users.user');

		// save mobile
		self::SetFieldValue($field->id, $juser->id, $mobile);

		// insert user group map
		$usergroupmap = array('user_id' => $juser->id, 'group_id' => "2");
		$insertUserGroupMap = self::Insert($usergroupmap, '#__user_usergroup_map');

		return $juser;
	} // function

	public static function getJoomlaField($name = "", $context = "")
	{

		// Create a new query object.
		$db = self::$_db;
		$query = $db->getQuery(true);

		// Order it by the ordering field.
		$query->select('a.*');
		$query->from($db->quoteName('#__fields', 'a'));

		if (!empty($name)) {
			$query->where($db->quoteName('a.name') . ' = ' . $db->quote($name));
		}

		if (!empty($context)) {
			$query->where($db->quoteName('a.context') . ' = ' . $db->quote($context));
		}

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		if (!empty($name) && !empty($context)) {
			$results = $db->loadObject();
		} else {
			$results = $db->loadObjectList();
		}

		return $results;
	} // function

	public static function SetFieldValue($field_id, $item_id, $value)
	{

		// include and get fields models
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_fields/models', 'FieldsModel');
		$fieldModel = JModelLegacy::getInstance('Field', 'FieldsModel', array('ignore_request' => true));

		// set value
		$fieldModel->setFieldValue($field_id, $item_id, $value);
	} // function

	// $data = it is an array LIKE array(id => 1, title => 'mohammad hosein miri', age => 25 ...)
	public static function Insert($data, $table)
	{

		if (empty($data)) {
			return false;
		}

		// Create and populate an object.
		$profile = new stdClass();
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$profile->$key = $value;
			}
		} elseif (is_object($data)) {
			$profile = $data;
		}

		$action = self::$_db->insertObject($table, $profile);
		
		return self::$_db->insertid();
	} // function

	// $data = it is an array LIKE array(id => 1, title => 'mohammad hosein miri', age => 25 ...)
	public static function Update($data, $table, $indexKey = 'id')
	{

		if (empty($data)) {
			return false;
		}

		// Create and populate an object.
		$profile = new stdClass();
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				$profile->$key = $value;
			}
		} elseif (is_object($data)) {
			$profile = $data;
		}

		$action = self::$_db->updateObject($table, $profile, $indexKey);

		return $action;
	} // function

	public static function getParams($component = 'com_logregsms')
	{
		$params = JComponentHelper::getParams($component);
		return $params;
	} // function

	public static function getCurrentUrl()
	{
		$uri = JUri::getInstance();
		$link = $uri->toString();

		return $link;
	} // function

	public static function getOneMenu($view = "", $option = 'com_logregsms') {
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

	public static function getAUTOincrement($table)
	{

		// get joomla config
		$config = &JFactory::getConfig();

		// get database config
		$host = $config->get('host');
		$user = $config->get('user');
		$password = $config->get('password');
		$db = $config->get('db');
		$prefix = $config->get('dbprefix');

		// prepare table full name
		$table = $prefix . $table;

		// connect to db
		$conn = mysqli_connect($host, $user, $password);
		@mysqli_select_db($conn, $db);
		$schema = mysqli_query($conn, "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_NAME = '$table' and TABLE_SCHEMA='$db'");
		$result = mysqli_fetch_assoc($schema);
		$auto_increment_id = $result['AUTO_INCREMENT'];

		return $auto_increment_id;
	} // function

	// Convert Persian numbers to Latin number
	public static function CleanUTF8_ToEn($str)
	{

		if (empty($str)) {
			return $str;
		}

		$ends = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'ی', 'ک', 'ه');
		$ards = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', 'ي', 'ك', 'ة');

		$str = str_replace($ards, $ends, $str);

		return $str;
	} // function

	public static function User($user_id = NULL)
	{
		$user = JFactory::getUser($user_id);
		self::getUserExtraFieldsFromJoomlaFields($user);

		return $user;
	} // function

	/*
		get user from #__users
	*/
	public static function getUser($id = 0, $username = "", $email = "", $block = 0)
	{

		$id = (int)$id;
		$block = (int)$block;

		// Create a new query object.
		$db = self::$_db;
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__users', 'a'));

		// get by block
		if ($block == 2) {
			$query->where('(' . $db->quoteName('a.block') . ' = 1 OR ' . $db->quoteName('a.block') . ' = 0)');
		} else {
			$query->where($db->quoteName('a.block') . ' = ' . $block);
		}

		// get by id
		if ($id > 0) {
			$query->where($db->quoteName('a.id') . ' = ' . $id);
		}

		// get by username
		if (!empty($username)) {
			$query->where($db->quoteName('a.username') . ' = ' . $db->quote($username));
		}

		// get by email
		if (!empty($email)) {
			$query->where($db->quoteName('a.email') . ' = ' . $db->quote($email));
		}

		$db->setQuery($query);
		if ($id > 0 || !empty($username) || !empty($email)) {
			$result = $db->loadObject();
		} else {
			$result = $db->loadObjectList();
		}

		return $result;
	} // function

	public static function getUserExtraFieldsFromJoomlaFields(&$user)
	{

		JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

		// ******
		// JFactory::getUser() must not change to self::getUser()
		// ******
		$fields = FieldsHelper::getFields('com_users.user', $user, true);

		$newFields = array();
		foreach ($fields as $field) {
			$user->{$field->name} = $field->value;
		}

		return true;
	} // function

	public static function Validation($field, $type)
	{
		if (empty($field)) {
			return false;
		}

		$result = array('result' => false, 'msg' => '');

		switch ($type) {
			case "email": // Validate Email
				$regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/';
				if (preg_match($regex, $field)) {
					$result['result'] = true;
				} else {
					$result['result'] = false;
					$result['msg'] = "فرمت آدرس پست الکترونیکی (ایمیل) وارد شده صحیح نمی باشد.";
				}
				break;

			case "mobile": // Validate Mobile number
				$split = str_split($field);
				if ($split[0] !== "0" || $split[1] !== "9") {
					$result['result'] = false;
					$result['msg'] = "شماره تلفن همراه باید با 09 شروع شود.";
					break;
				}

				if (count($split) !== 11) {
					$result['result'] = false;
					$result['msg'] = "شماره تلفن همراه باید 11 رقم باشد.";
					break;
				}

				$result['result'] = true;
				break;
			case "domain":
				if (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $field) && preg_match("/^.{1,253}$/", $field) && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $field)) {
					$result['result'] = true;
					break;
				} else {
					$result['result'] = false;
					$result['msg'] = "فرمت دامنه صحیح نمی باشد.";
					break;
				}
		}

		return $result;
	} // function

	public static function getUserInfo($username = NULL, $email = NULL, $state = NULL)
	{

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select(array('a.*'))
			->from($db->quoteName('#__users', 'a'));

		// filter by username
		if (!empty($username))
			$query->where($db->quoteName('a.username') . ' = "' . $username . '"');

		// filter by email
		if (!empty($email))
			$query->where($db->quoteName('a.email') . ' = "' . $email . '"');

		// filter by state
		if (!empty($state) && $state > 0)
			$query->where($db->quoteName('a.state') . ' = ' . $state);

		$db->setQuery($query);
		$data = $db->loadObject();

		return $data;
	} // function

	public static function getIpAddress()
	{
		// check for shared internet/ISP IP
		if (!empty($_SERVER['HTTP_CLIENT_IP']) && self::validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		// check for IPs passing through proxies
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// check if multiple ips exist in var
			if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
				$iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				foreach ($iplist as $ip) {
					if (self::validate_ip($ip))
						return $ip;
				}
			} else {
				if (self::validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
					return $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
		}
		if (!empty($_SERVER['HTTP_X_FORWARDED']) && self::validate_ip($_SERVER['HTTP_X_FORWARDED']))
			return $_SERVER['HTTP_X_FORWARDED'];
		if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
			return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
		if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && self::validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
			return $_SERVER['HTTP_FORWARDED_FOR'];
		if (!empty($_SERVER['HTTP_FORWARDED']) && self::validate_ip($_SERVER['HTTP_FORWARDED']))
			return $_SERVER['HTTP_FORWARDED'];
		// return unreliable ip since all else failed
		return $_SERVER['REMOTE_ADDR'];
	} // function

	/**
	 * Ensures an ip address is both a valid IP and does not fall within
	 * a private network range.
	 */
	public static function validate_ip($ip)
	{
		if (strtolower($ip) === 'unknown')
			return false;
		// generate ipv4 network address
		$ip = ip2long($ip);
		// if the ip is set and not equivalent to 255.255.255.255
		if ($ip !== false && $ip !== -1) {
			// make sure to get unsigned long representation of ip
			// due to discrepancies between 32 and 64 bit OSes and
			// signed numbers (ints default to signed in PHP)
			$ip = sprintf('%u', $ip);
			// do private network range checking
			if ($ip >= 0 && $ip <= 50331647) return false;
			if ($ip >= 167772160 && $ip <= 184549375) return false;
			if ($ip >= 2130706432 && $ip <= 2147483647) return false;
			if ($ip >= 2851995648 && $ip <= 2852061183) return false;
			if ($ip >= 2886729728 && $ip <= 2887778303) return false;
			if ($ip >= 3221225984 && $ip <= 3221226239) return false;
			if ($ip >= 3232235520 && $ip <= 3232301055) return false;
			if ($ip >= 4294967040) return false;
		}
		return true;
	} // function

	public static function rndNums($count = 5, $rdm = '')
	{
		$a = array(1, 2, 3, 4, 5, 6, 7, 8, 9);
		for ($i = 0; $i < $count; $i++) {
			$number = rand(0, 8);
			$rdm .= $a[$number];
		}
		return $rdm;
	} // function

	/*
		get user by username from #__users if exist
		if not, return null
	*/
	public static function FindUserName($username)
	{

		// Create a new query object.
		$db = self::$_db;

		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__users', 'a'));
		$query->where($db->quoteName('a.username') . ' = ' . $db->quote($username));

		$db->setQuery($query);
		$result = $db->loadObject();

		return $result;
	} // function

	public static function randChar($count = 8)
	{
		//$a = "1234567890abcdefghijklmnopqrstuvwxyz";
		$a = "qwertyuiopasdfghjklzxcvbnm";
		$b = "";
		for ($i = 0; $i < $count; $i++) {
			$start = mt_rand(1, strlen($a) - 1);
			$b .= substr($a, $start, 1);
		}
		return $b;
	}
	public static function getConfirm($id = 0, $code = "", $is_confirmed = 1)
	{

		$id = (int)$id;

		// Create a new query object.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Order it by the ordering field.
		$query->select('a.*');
		$query->from($db->quoteName('#__logregsms_confirm', 'a'));
		$query->where($db->quoteName('a.is_confirmed') . ' = ' . $is_confirmed);

		if ($id > 0) {
			$query->where($db->quoteName('a.id') . ' = ' . $id);
		}

		if (!empty($code)) {
			$query->where($db->quoteName('a.code') . ' = "' . $code . '"');
		}

		$db->setQuery($query);
		if ($id > 0 || !empty($code)) {
			$result = $db->loadObject();
		} else {
			$result = $db->loadObjectList();
		}

		return $result;
	} // function
}
