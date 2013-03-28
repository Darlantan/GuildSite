<?php

/**
 * Class Path
 * 
 * Defines paths for classes. All classes need to be included in this file.
 * 
 * Also handles server paths.
 * 
 * @copyright Iiro Vaahtojärvi
 * @author Iiro Vaahtojärvi
 */

class Path
{
	// General classes
	
	// Ctrl classes
	public static $Ctrl_guildsite   = "ctrl/Ctrl_guildsite.php";
	public static $Ctrl_log         = "ctrl/Ctrl_log.php";
	public static $Ctrl_user		= "ctrl/Ctrl_user.php";
	public static $Ctrl_view		= "ctrl/Ctrl_view.php";
	public static $Ctrl_news		= "ctrl/Ctrl_news.php";
	
	// Bank
	public static $Bank             = "supplement/Bank.php";

	// Models
	public static $User             = "model/User.php";
	//public static $Parser			= "model/Parser.php";
	public static $View				= "model/View.php";
	public static $News				= "model/News.php";

	// DB classes
	public static $Sql              = "db/Sql.php";
	public static $Sql_user         = "db/Sql_user.php";
	public static $Sql_log          = "db/Sql_log.php";
	public static $Sql_view			= "db/Sql_view.php";
	public static $Sql_news			= "db/Sql_news.php";


	// Server path vars
	public static $path             = "";
	public static $server_path      = "";
	public static $server_self      = "";
	public static $request_path     = "";
    
	/**
	 * Function initServerData
	 * 
	 * Set server path variables, get location and manage some basic rights.
	 * 
	 * @author Iiro Vaahtojärvi
	 */
	public static function initServerData()
	{
		// Set document path starting from web server root: "/index.php"
		self::$server_self = $_SERVER["PHP_SELF"];

		// Set document path starting from server root: "/var/www/index.php"
		self::$path = $_SERVER["DOCUMENT_ROOT"];
		self::$path .= $_SERVER["PHP_SELF"];

		// Check for system test page
		if(in_array("Bank", get_declared_classes()) === false) // TODO: check if needed
		{
			require_once("Bank.php");
		}
		$pos = strrpos(self::$path, "www/".Bank::SYSTEM_INDEX_PAGE);
		if($pos === false)
		{
			$pos = strrpos(self::$path, "www/".Bank::SYSTEM_TEST_PAGE);
		}
		if($pos !== false) // Remove www/index... stuff from variable $path if match is found in the string
		{
			self::$path = substr(self::$path, 0, $pos);
		}
	}

	/**
	 * Function getFile
	 * 
	 * Returns the path of the requested class.
	 * 
	 * @author Iiro Vaahtojärvi
	 */
	public static function getFile($class)
	{
		// Double $'s to make the variables variables
		// Read more: http://fi.php.net/manual/en/language.variables.variable.php
		
		if(!isset(self::$$class)) // If requested class is not set in Path.php
		{
			exit("Class ".$class." not set.");
		}
		else if(!file_exists(self::$path.self::$$class)) // If file for requested class isn't found
		{
			//print("Path: ".self::$path."<br />Server_self: ".self::$server_self."<br />");
			exit("Class ".$class." not found.");
		}
		
		return self::$path.self::$$class;
	}
}

// Init Path variables
Path::initServerData();

/**
 * Function __autoload
 * 
 * Read more: http://fi2.php.net/autoload
 * 
 * @author Iiro Vaahtojärvi
 */
function __autoload($class)
{
	$class_path = Path::getFile($class);
	require_once($class_path);
}
?>
