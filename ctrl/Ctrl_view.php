<?php

/**
 * Class Ctrl_view
 * 
 * Control class for view actions.
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class Ctrl_view
{
	/**
	 * Function replaceContent
	 * 
	 * Function serves as a place to hold a switch case to replace mainly user related content tags.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param $tag string
	 * @param $user object
	 * @return $replace_with string
	 */
	public static function replaceContent($tag,$user)
	{
		$replace_with = "";
		switch($tag) {
			case Bank::TAG_USER_ID:
				$replace_with = $user->getId();
				break;
			case Bank::TAG_FIRSTNAME:
				$replace_with = $user->getFirstname();
				break;
			case Bank::TAG_LASTNAME:
				$replace_with = $user->getLastname();
				break;
			case Bank::TAG_EMAIL:
				$replace_with = $user->getEmail();
				break;
			case Bank::TAG_USERNAME:
				$replace_with = $user->getUsername();
				break;
		}
		
		return $replace_with;
	}
	
	/**
	 * Function buildUserList
	 * 
	 * Function fetches views from the database based on all users listed in the database and returns a string to be placed on the view.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @return string
	 */
	public static function buildUserList()
	{
		$str = "";
		$tag_mark = Bank::PARAM_TAG_MARK;
		
		$users = Sql_user::selectAllUsers();
		
		$user_count = count($users);
		
		$user_list_view = new View();
		$tmp = Sql_view::selectHelperViewByParams(Bank::VIEW_ID_USER_LIST_CONTENT);
		$user_list_view->setViewStr($tmp[0]["gs_view_helper_str"]);
		unset($tmp);
		
		foreach($users as $key => $value) {
			$user_id = $value["gs_user_id"];
			$edituser = Ctrl_user::getUserById($user_id);
			$str .= $user_list_view->getViewStr();
			
			$tmp_str = $str;
			while(self::findTags($tmp_str, $tag) !== false){
				$replace_with = self::replaceContent($tag, $edituser);
				
				$to_replace = $tag_mark.$tag.$tag_mark;
				
				$str = str_replace($to_replace, $replace_with, $str);
			}
		}
		
		return $str;
		
	}
	
	/**
	 * Function findTags
	 * 
	 * Helper function to find and separate tags from code. Tries to find '##SOMETHING##'.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string &$str
	 * @param string &$key
	 */
	public static function findTags(&$str, &$tag)
	{
		$tag_mark		= Bank::PARAM_TAG_MARK;
		$tag_mark_len	= strlen($tag_mark);
		$tag_start		= strpos($str, $tag_mark);
		$temp			= strstr($str, $tag_mark);
		$temp			= substr($temp, $tag_mark_len, strlen($temp));
		$tag_end		= strpos($temp, $tag_mark);
		$tag			= substr($temp, 0, $tag_end);
		
		$str			= substr($str, $tag_start + $tag_end + (2 * $tag_mark_len), strlen($str));
		
		if(strlen($tag) == 0) {
			return false;
		}
	}
}
?>
