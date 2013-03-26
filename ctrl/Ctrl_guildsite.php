<?php

/**
 * class Ctrl_guildsite
 * 
 * Control class for GuildSite common functionality.
 * 
 * @copyright Iiro Vaahtojärvi
 * @author Iiro Vaahtojärvi
 */
class Ctrl_guildsite
{
	/**
	 * Function runGuildSite
	 * 
	 * Main function of the application, all requests go through this function.
	 * Handles view selection, login/logout actions and some user authority levels.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param array $get
	 * @param array $post
	 * @param array $files
	 * @param int $ip
	 */
	public static function runGuildSite(&$get, &$post, &$files, $ip)
	{
		// Set variables
		$pid			= Bank::PAGE_ID_FRONTPAGE;
		$aid			= null;
		$login_error	= "";
		
		// Check for session
		session_name(Bank::GUILDSITE_NAME);
		session_start();
//		if(empty(session_id())) {
//			// If no session is started, start one
//			session_name(Bank::GUILDSITE_NAME);
//			session_start();
//		}
//		else if(session_name() != Bank::GUILDSITE_NAME) {
//			// If a session exists and it is from another instance of GS, close it and start a new one
//			session_write_close();
//			session_name(Bank::GUILDSITE_NAME);
//			session_start();
//		}
		
		// Log the http POST request parameters to error log (My server: /var/log/php_errors.log)
		if(sizeof($post) > 0) {
			error_log(Bank::GUILDSITE_NAME.": ".print_r($post, true));
		}
		
		// Login handling
		if(isset($post[Bank::SUBMIT_LOGIN])) {
			// Connect to database
			self::connectHostDb();
			
			// Take username and password from post data
			$username	= trim($post[Bank::INPUT_USER_USERNAME]);
			$password	= trim($post[Bank::INPUT_USER_PASSWORD]);
			
			// Crypt the password (like it is in the db)
			$password	= self::createPassword($password);
			
			$user		= Ctrl_user::lookForUser($username, $password);
			$user_id	= $user->getId();
			
			if(empty($user_id) === false) {
				// If user is found, create session variables to identify
				$_SESSION["user_id"]	= $user_id;
				$_SESSION["username"]	= $user->getUsername();
				$_SESSION["session_id"]	= session_id();
				$_SESSION["user_level"]	= $user->getLevel();
				
				// Create log event from logging in
				switch($user->getLevel()) {
					case Bank::RIGHT_LEVEL_USER :
						$log_type	= Bank::LOG_TYPE_USER_LOGIN;
						$log_info	= Bank::LOG_INFO_USER_LOGIN;
						break;
					case Bank::RIGHT_LEVEL_MEMBER :
						$log_type	= Bank::LOG_TYPE_MEMBER_LOGIN;
						$log_info	= Bank::LOG_INFO_MEMBER_LOGIN;
						break;
					case Bank::RIGHT_LEVEL_MODERATOR :
						$log_type	= Bank::LOG_TYPE_MODERATOR_LOGIN;
						$log_info	= Bank::LOG_INFO_MODERATOR_LOGIN;
						break;
					case Bank::RIGHT_LEVEL_ADMIN :
						$log_type	= Bank::LOG_TYPE_ADMIN_LOGIN;
						$log_info	= Bank::LOG_INFO_ADMIN_LOGIN;
						break;
					case Bank::RIGHT_LEVEL_SUPERADMIN :
						$log_type	= Bank::LOG_TYPE_SUPERADMIN_LOGIN;
						$log_info	= Bank::LOG_INFO_SUPERADMIN_LOGIN;
						break;
				}
				Ctrl_log::createLogEvent($user_id, $log_type, $log_info.$ip);
			} else {
				// No user was found, give error and log event
				$login_error	= Bank::ERROR_LOGIN_FAILED;
				$log_type		= Bank::LOG_TYPE_LOGIN_INVALID;
				$log_info		= Bank::LOG_INFO_LOGIN_INVALID;
				Ctrl_log::createLogEvent(null, $log_type, $log_info."[".$post[Bank::INPUT_USER_USERNAME]."]/[".$post[Bank::INPUT_USER_PASSWORD]."] IP:".$ip);
			}
		}
		
		// Check for requested page ID, set $pid to the one from the request
		if(isset($get["pid"])) {
			$pid = $get["pid"];
		}
		
		// If logout page is requested, log out
		if($pid == Bank::PAGE_ID_LOGOUT) {
			self::logOut();
		}
		
		// Connect to database
		self::connectHostDb();
		
		// Validate data in parser, build an array of the data.
		// TODO: Find out on what level is this necessary and implement it later. Might not be needed.
		// $parser = self::buildParser($post, $get, $files);
		
		// Build view with the set parameters.
		self::buildView($pid, $post, $get, $files, $login_error);
	}
	
	/**
	 * Function buildView
	 * 
	 * Builds and prints the created view.
	 * Be careful not to add any hard coding when editing this function!
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $pid
	 * @param array $post
	 * @param array $get
	 * @param array $files
	 * @param string $login_error
	 */
	public static function buildView($pid, $post, $get, $files, $login_error)
	{
		$str			= "";
		$layout_str		= "";
		$result			= null;
		
		$user_level		= Bank::RIGHT_LEVEL_NONE;
		$user_id		= 0;
		$action			= self::findAction($post);
		
		// If user is logged in, get user id and right level from session.
		if(isset($_SESSION["user_id"])) {
			$user_id	= $_SESSION["user_id"];
			$user_level	= $_SESSION["user_level"];
		}
		
		// Call for function that handles actually doing anything
		self::runEdit($post, $result, $user_id);
		
		// Fetch layout string based on user level
		$layout	= Sql_view::selectLayout($user_level);
		$layout_str = $layout[0]["gs_layout_str"];
		
		// Fetch view from db using pid from get params or based on action
		if(empty($action)) {
			$view = self::lookForViewToShow($pid, $user_id);
		} else {
			switch($action){
				case Bank::SUBMIT_LOGIN :
					if($user_id != 0) {
						// If user ID holds something else than 0, find the default page for the user's level.
						$pid = self::getDefaultPidByLevel($user_id);
					}
					$view = self::lookForViewToShow($pid, $user_id);
					break;
				case Bank::SUBMIT_USER_JOIN :
					if(is_int($result)) {
						// If result is integer after user has joined it means that user joined successfully -> $result is the new user's id. Move user to thank you page.
						$pid = Bank::PAGE_ID_JOINED_USER;
						$user_id = $result;
					}
					$view = self::lookForViewToShow($pid, $user_id);
					break;
				case Bank::SUBMIT_USER_EDIT :
					if(is_int($result)) {
						// If result is integer after user has been modified, modification was successful
						$pid = Bank::PAGE_ID_MODIFIED_USER;
						$user_id = $result;
					}
					$view = self::lookForViewToShow($pid, $user_id);
					break;
				case Bank::SUBMIT_EDIT_USER :
					$pid = Bank::PAGE_ID_ADMIN_EDIT_USER;
					$view = self::lookForViewToShow($pid, $user_id);
					break;
			}
		}
		
		// If the login failed, put that in result.
		if(empty($login_error) === false) {
			$result = array();
			$result["login_error"] = $login_error;
		}
		
		// Build the string to be printed.
		$str = self::buildPage($view, $pid, $user_id, $layout_str, $result, $post, $get);
		
		// Print the finalised string to browser.
		print $str;
		
	}
	
	/**
	 * Function buildPage
	 * 
	 * Function goes through view and layout strings, replaces all content tags and puts together the final string.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param object $view
	 * @param int $pid
	 * @param int $user_id
	 * @param str $layout_str
	 * @param array $result
	 * @param array $post
	 * @param array $get
	 * @return string $str
	 */
	private static function buildPage($view, $pid, $user_id, $layout_str, $result, $post, $get)
	{
		$user_level		= Bank::RIGHT_LEVEL_NONE;
		$str			= "";
		$result_str		= "";
		$replace_with	= "";
		$menustr		= "";
		$extracontent	= "";
		$error_str		= "";
		$tag_mark		= Bank::PARAM_TAG_MARK;
		$key			= "";
		
		
		// If user id is 0, find the user in question.
		if($user_id != 0) {
			$user = Ctrl_user::getUserById($user_id);
			$user_level = $user->getLevel();
			$user_id	= $user->getId();
		}
		
		$str .= $view->getViewStr();
		
		// Replace possible menu tags from the layout string with user level based menu
		if(strpos($layout_str, Bank::MENU_TAG) !== false) {
			$menustr_array = Sql_view::selectMenuByLevel($user_level);
			// Workaround for Thank you page for joined user to show the correct menu
			if($pid == Bank::PAGE_ID_JOINED_USER) {
				$menustr_array = Sql_view::selectMenuByLevel(Bank::RIGHT_LEVEL_NONE);
			}
			$menustr .= $menustr_array[0]["gs_view_menu_str"];
			$layout_str = str_replace(Bank::MENU_TAG, $menustr, $layout_str);
		}
		
		// Replace possible extra content tags from the layout string with the extra content based on user level
		// Making an exception here on join page and the page after that.
		if(strpos($layout_str, Bank::EXTRA_CONTENT) !== false && $pid != Bank::PAGE_ID_JOIN && $pid != Bank::PAGE_ID_JOINED_USER) {
			$extracontent_array = Sql_view::selectExtraContentByLevel($user_level);
			$extracontent .= $extracontent_array[0]["gs_view_extracontent_str"];
			$layout_str = str_replace(Bank::EXTRA_CONTENT, $extracontent, $layout_str);
		} else {
			$layout_str = str_replace(Bank::EXTRA_CONTENT, "", $layout_str);
		}
		
		// Replace content tag in layout string with the generated string.
		$str = str_replace(Bank::TAG_CONTENT, $str, $layout_str);
		
		// Replace menu content tag in string with the generated menu string.
		$str = str_replace(Bank::MENU_TAG, $menustr, $str);
		
		// Replace possible errors only if error tag is present. If error tag is present but no errors found, replace with empty string.
		if(empty($result) === false && is_array($result) && strpos($str, Bank::FORM_ERRORS) !== false) {
			// Fetch error template
			$errorview = new View();
			$tmp = Sql_view::selectHelperViewByParams(Bank::VIEW_ID_ERRORTEMPLATE);
			$errorview->setViewStr($tmp[0]["gs_view_helper_str"]);
			unset($tmp);
			
			// For each resulted error, get the error string from db
			foreach($result as $key => $value) {
				$error = Sql::fetchError($value);
				$error_str .= $error[0]["gs_error_str"];
			}
			
			// Replace tag in error template with error strings
			$result_str = str_replace(Bank::ERROR_CONTENTS, $error_str, $errorview->getViewStr());
			
			// Replace error tag in actual viewstring with completed error string
			$str = str_replace(Bank::FORM_ERRORS, $result_str, $str);
		} else if(strpos($str, Bank::FORM_ERRORS) !== false) {
			$str = str_replace(Bank::FORM_ERRORS, "", $str);
		}
		
		// If content user list tag is found
		if(strpos($str, Bank::USER_LIST) !== false) {
			// Get strings for wrapper and content
			$user_list_wrapper = new View();
			$tmp = Sql_view::selectHelperViewByParams(Bank::VIEW_ID_USER_LIST_TEMPLATE);
			$user_list_wrapper->setViewStr($tmp[0]["gs_view_helper_str"]);
			$user_list_str = Ctrl_view::buildUserList();
			
			// Replace content tag from wrapper with content string
			$user_list_wrapper = str_replace(Bank::USER_LIST_CONTENT, $user_list_str, $user_list_wrapper->getViewStr());
			
			// Replace wrapper tag from viewstring with wrapper string
			$str = str_replace(Bank::USER_LIST, $user_list_wrapper, $str);
		}
		
		$tmp_str = $str;
		if(isset($post[Bank::SUBMIT_EDIT_USER])) {
			$edituser = Ctrl_user::getUserById($post[Bank::SUBMIT_EDIT_USER]);
		}
		// Find all the rest of the tags and replace them with required content.
		print_r($user);
		print_r($edituser);
		while(Ctrl_view::findTags($tmp_str, $tag) !== false) {
			$replace_with = Ctrl_view::replaceContent($tag, $user, $edituser);
			
			$to_replace = $tag_mark.$key.$tag_mark;
			
			$str = str_replace($to_replace, $replace_with, $str);
		}
		
		return $str;
	}
	
	/**
	 * Function connectHostDb
	 * 
	 * Function creates a database connection.
	 * 
	 * @author Iiro Vaahtojärvi
	 */
	public static function connectHostDb()
	{
		// Connection info from Bank
		$host		= Bank::DB_HOST;
		$database	= Bank::DB_NAME;
		$user		= Bank::DB_USER;
		$password	= Bank::DB_PASS;
		
		$db = new Sql();
		$link = $db->connectDb($host, $database, $user, $password);
		return $link;
	}
	
	/**
	 * Function logOut
	 * 
	 * Remove all session data, redirect user to system index page.
	 * 
	 * @author Iiro Vaahtojärvi
	 */
	public static function logOut()
	{
		session_unset();
		session_destroy();
		$_SESSION = array();
		header("Location: ".Bank::SYSTEM_INDEX_PAGE);
		exit;
	}
	
	/**
	 * Function createPassword
	 * 
	 * Generates a crypted password from given password (when submitting a form) or generates a new one if nothing was given.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $password
	 * @param string &$not_crypted_password
	 * @return string
	 */
	public static function createPassword($password = "", &$not_crypted_password = "")
	{
		if($password == ""){
			// No password given, generate a new one
			for($i = 0; $i < 5; $i++)
			{
				$not_crypted_password .= chr(rand(97,122));
			}
			$not_crypted_password .= chr(rand(48,57));
		} else {
			// Password was given
			$not_crypted_password = $password;
		}
		
		// NOTE: If you change this value, existing users will not be able to log in without a new password generated. Careful when changing the salt here.
		$password = crypt($not_crypted_password, "iCMScrypt");
		
		return $password;
	}
	
	/**
	 * Function validateEmail
	 * 
	 * Function checks whether the email is a valid address. Returns true or false.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $email
	 * @return boolean
	 */
	public static function validateEmail($email)
	{
		if(!(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.((aero|biz|com|coop|edu|eu|gov|info|int|mil|mobi|museum|name|net|org|pro)|(ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|fi|fj|fk|fm|fo|fr|ga|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw))$", strtolower($email)))) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Function lookForViewToShow
	 * 
	 * Looks for the view to show. If no view is found, return not found page and log event. If user is unauthorized to view requested page, show unauthorized page instead and log event.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $pid
	 * @param int $user_id
	 * @return View
	 */
	private static function lookForViewToShow($pid, $user_id)
	{
		$user_level = Bank::RIGHT_LEVEL_NONE;
		
		// If user is found.
		if($user_id != 0) {
			$user = Ctrl_user::getUserById($user_id);
			$user_level = $user->getLevel();
		}
		
		$view = new View();
		$result = Sql_view::selectViewByParams($pid);
		if(empty($result)) {
			// Pid not found, logging event and showing 404 page instead.
			$log_type = Bank::LOG_TYPE_PAGE_NOT_FOUND;
			$log_info = Bank::LOG_INFO_PAGE_NOT_FOUND;
			Ctrl_log::createLogEvent($user_id, $log_type, $log_info.$pid);
			$pid = Bank::PAGE_ID_404;
			$result = Sql_view::selectViewByParams($pid);
		} else if($result[0]["gs_view_right_level"] > $user_level) {
			// Page not authorized for current user, log event and show 403 page instead.
			$log_type = Bank::LOG_TYPE_UNAUTHORIZED;
			$log_info = Bank::LOG_INFO_UNAUTHORIZED;
			Ctrl_log::createLogEvent($user_id, $log_type, $log_info.$pid);
			$pid = Bank::PAGE_ID_UNAUTHORIZED;
			$result = Sql_view::selectViewByParams($pid);
		}
		$view->setId($result[0]["gs_view_id"]);
		$view->setPid($result[0]["gs_view_pid"]);
		$view->setViewStr($result[0]["gs_view_str"]);
		$view->setRightLevel($result[0]["gs_view_right_level"]);
		
		return $view;
	}
	
	/**
	 * Function runEdit
	 * 
	 * Helper function: Goes through post and get data, returns a result based on required actions.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param array $post
	 * @param int $user_id
	 * @return array $result
	 */
	public static function runEdit($post, &$result, $user_id)
	{
		$action = self::findAction($post);
		
		// Look for a function to use with the given action. Actions must be unique. $action is the button name used to submit a form.
		switch($action) {
			case Bank::SUBMIT_USER_JOIN:
				$result = Ctrl_user::modifyUserData($post, $user_id);
				break;
			case Bank::SUBMIT_USER_EDIT:
				$result = Ctrl_user::modifyUserData($post, $user_id);
				break;
		}
	}
	
	/**
	 * Function findAction
	 * 
	 * Parses post data for a submitted action.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param array $post
	 * @return string $action
	 */
	public static function findAction($post)
	{
		$action = null;
		// Search for the specific action
		foreach($post as $key => $value) {
			if(strstr($key, "SUBMIT")) {
				$action = $key;
			}
		}
		
		return $action;
	}
	
	/**
	 * Function getDefaultPidByLevel
	 * 
	 * Helper function to determine the default PID for logged in users. Returns int.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $user_id
	 * @return int $pid
	 */
	private static function getDefaultPidByLevel($user_id)
	{
		$user = Ctrl_user::getUserById($user_id);
		
		switch($user->getLevel()){
			case Bank::RIGHT_LEVEL_USER :
				$pid = Bank::PAGE_ID_DEFAULT_USER;
				break;
			case Bank::RIGHT_LEVEL_MEMBER :
				$pid = Bank::PAGE_ID_DEFAULT_MEMBER;
				break;
			case Bank::RIGHT_LEVEL_MODERATOR :
				$pid = Bank::PAGE_ID_DEFAULT_MODERATOR;
				break;
			case Bank::RIGHT_LEVEL_ADMIN :
				$pid = Bank::PAGE_ID_DEFAULT_ADMIN;
				break;
			case Bank::RIGHT_LEVEL_SUPERADMIN :
				$pid = Bank::PAGE_ID_DEFAULT_ADMIN; // NOTE: Superadmin functionalities not implemented. Perhaps in the future, but using admin stuff for now.
				break;
		}
		
		return $pid;
	}
	
	/**
	 * Function buildParser
	 * 
	 * Parses the data received in a request, builds an array of data to use.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param array $post
	 * @param array $get
	 * @param array $files
	 * @return array $parser
	 */
	private static function buildParser($post, $get, $files)
	{
		$declared_classes = get_declared_classes();
		
		
	}
}
?>
