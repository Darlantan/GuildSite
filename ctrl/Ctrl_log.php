<?php

/**
 * Class Ctrl_log
 * 
 * This class handles all log events.
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class Ctrl_log
{
	/**
	 * Function createLogEvent
	 * 
	 * Creates a log event
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $user_id
	 * @param int $log_type
	 * @param string $log_info
	 */
	public static function createLogEvent($user_id, $log_type, $log_info = "")
	{
		Ctrl_guildsite::connectHostDb();
		if(empty($user_id)) {
			// If no user id is set, log the event with "NULL" user id.
			$user_id = "NULL";
		}
		$log_id = Sql_log::insertLogEvent($user_id, $log_type, $log_info);
	}
}
?>
