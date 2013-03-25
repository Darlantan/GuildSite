<?php

/**
 * Class Sql_log
 * 
 * Contains functions to insert, update, select log events.
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class Sql_log extends Sql
{
	/**
	 * Function insertLogEvent
	 * 
	 * Function inserts a log event to gs_log.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $user_id
	 * @param int $log_type
	 * @param int $log_info
	 */
	public static function insertLogEvent($user_id, $log_type, $log_info)
	{
		$date = date("Y-m-d H:i:s");
		$query = "INSERT INTO ".Bank::DB_TABLE_LOG."(gs_log_type, gs_user_id, gs_log_date, gs_log_info) VALUES (".$log_type.", ".$user_id.", '".$date."', '".$log_info."')";
		return parent::insertWithId($query);
	}
}
?>
