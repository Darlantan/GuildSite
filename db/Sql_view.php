<?php

/**
 * Class Sql_view
 * 
 * Handles sql queries regarding views.
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class Sql_view extends Sql
{	
	/**
	 * Function selectLayout
	 * 
	 * Selects layout string based on user level.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $user_level
	 * @return string
	 */
	public static function selectLayout($user_level)
	{
		$query = "SELECT gs_layout_str FROM gs_layout WHERE gs_layout_right_level = ".$user_level;
		return parent::select($query);
	}
	
	/**
	 * Function selectViewByParams
	 * 
	 * Selects view by pid.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $pid
	 * @return array
	 */
	public static function selectViewByParams($pid)
	{
		$query = "SELECT gs_view_id, gs_view_pid, gs_view_str, gs_view_right_level FROM gs_view WHERE gs_view_pid = ".$pid;
		return parent::select($query);
	}
	
	/**
	 * Function selectHelperViewByParams
	 * 
	 * Selects a helper view based on id.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $vid
	 * @return array
	 */
	public static function selectHelperViewByParams($vid)
	{
		$query = "SELECT gs_view_helper_id, gs_view_helper_str FROM gs_view_helper WHERE gs_view_helper_id = ".$vid;
		return parent::select($query);
	}
	
	/**
	 * Function selectMenuByLevel
	 * 
	 * Selects the appropriate menu from gs_view_menu table.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $user_level
	 * @return string
	 */
	public static function selectMenuByLevel($user_level)
	{
		$query = "SELECT gs_view_menu_str FROM gs_view_menu WHERE gs_view_right_level = ".$user_level;
		return parent::select($query);
	}
	
	/**
	 * Function selectExtraContentByLevel
	 * 
	 * Selects the appropriate extra content from gs_view_extracontent table.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $user_level
	 * @return string
	 */
	public static function selectExtraContentByLevel($user_level)
	{
		$query = "SELECT gs_view_extracontent_str FROM gs_view_extracontent WHERE gs_view_extracontent_right_level = ".$user_level;
		return parent::select($query);
	}
}
?>
