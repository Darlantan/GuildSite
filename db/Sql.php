<?php

/**
 * Class Sql
 * 
 * Control class for basic SQL functionality. Database operations are handled through this class.
 * 
 * Most errors will be logged with error_log. On my server that means /var/log/php_errors.log
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class Sql
{
	/**
	 * Function connectDb
	 * 
	 * Connects the database. Returns mysql link id if successful.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $host
	 * @param string $database
	 * @param string $user
	 * @param string $password
	 * @return resource
	 */
	public function connectDb($host, $database, $user, $password)
	{
		// Connect
		$link = mysql_connect($host, $user, $password);
		if($link !== false) {
			// If connection was successful, select database
			$ok = mysql_select_db($database);
			if($ok === false) {
				// If database couldn't be selected, log error, return false.
				error_log(mysql_error());
				return $ok;
			}
		} else {
			// If connection couldn't be made, give error
			error_log(mysql_error());
		}
		return $link;
	}
	
	/**
	 * Function select
	 * 
	 * Run SELECT query, returns either array or NULL if no set is returned.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $query
	 * @return array
	 */
	public static function select($query)
	{
		$sql = mysql_query($query);
		if($sql !== false && $sql !== true) {
			// If query returns result set
			if(mysql_num_rows($sql) > 0) {
				// If result has any rows
				$result = array();
				
				// Get data from the result and push it into an associative array
				while($row = mysql_fetch_array($sql, MYSQL_ASSOC)) {
					$result[] = $row;
				}
				mysql_free_result($sql);
				$sql = false;
				return $result;
			} else {
				// No rows found, return NULL
				mysql_free_result($sql);
				return NULL;
			}
		} else {
			// Error occurred
			error_log(mysql_error());
		}
	}
	
	/**
	 * Function insert
	 * 
	 * Run INSERT query, return true if successful, false if not.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $query
	 * @return boolean
	 */
	public static function insert($query)
	{
		$sql = mysql_query($query);
		if($sql !== false) {
			return $sql;
		} else {
			// Error occurred
			error_log(mysql_error());
		}
	}
	
	/**
	 * Function insertWithId
	 * 
	 * Run INSERT query, returns ID generated for auto increment column. Returns 0 if nothing is inserted or last inserted id can not be defined.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $query
	 * @return int
	 */
	public static function insertWithId($query)
	{
		$sql = mysql_query($query);
		if($sql !== false) {
			return mysql_insert_id(); // Note: This is based on the last performed query, needs to be run right after the query itself to avoid wrong id's.
		} else {
			// Error occurred
			error_log(mysql_error());
		}
	}
	
	/**
	 * Function delete
	 * 
	 * Run DELETE query, returns true if successful.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $query
	 * @return boolean
	 */
	public static function delete($query)
	{
		$sql = mysql_query($query);
		if($sql !== false) {
			return $sql;
		} else {
			// Error occurred
			error_log(mysql_error());
		}
	}
	
	/**
	 * Function update
	 * 
	 * Run UPDATE query, returns true if successful.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $query
	 * @return boolean
	 */
	public static function update($query)
	{
		$sql = mysql_query($query);
		if($sql !== false) {
			return $sql;
		} else {
			// Error occurred
			error_log(mysql_error());
		}
	}
	
	/**
	 * Function fetchError
	 * 
	 * Function returns error message string from database.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $error_id
	 * @return string
	 */
	public static function fetchError($error_id)
	{
		$query = "SELECT ".Bank::DB_TABLE_ERROR."_str FROM ".Bank::DB_TABLE_ERROR." WHERE ".Bank::DB_TABLE_ERROR."_id = ".$error_id;
		return self::select($query);
	}
	
}
?>
