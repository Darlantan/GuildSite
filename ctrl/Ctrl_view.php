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
	 * @param $edituser object
	 * @param $article object
	 * @return $replace_with string
	 */
	public static function replaceContent($tag, $user = false, $edituser = false, $article= false)
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
			case Bank::TAG_EDIT_USER_ID:
				$replace_with = $edituser->getId();
				break;
			case Bank::TAG_EDIT_FIRSTNAME:
				$replace_with = $edituser->getFirstname();
				break;
			case Bank::TAG_EDIT_LASTNAME:
				$replace_with = $edituser->getLastname();
				break;
			case Bank::TAG_EDIT_EMAIL:
				$replace_with = $edituser->getEmail();
				break;
			case Bank::TAG_EDIT_USERNAME:
				$replace_with = $edituser->getUsername();
				break;
			case Bank::TAG_NEWS_ID:
				$replace_with = $article->getId();
				break;
			case Bank::TAG_NEWS_TITLE:
				$replace_with = $article->getTitle();
				break;
			case Bank::TAG_NEWS_STR:
				$replace_with = $article->getStr();
				break;
			case Bank::TAG_NEWS_DATE:
				$replace_with = $article->getDate();
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
		
		$user_list_view = self::fetchHelperView(Bank::VIEW_ID_USER_LIST_CONTENT);
		
		foreach($users as $key => $value) {
			$edituser = Ctrl_user::getUserById($value["gs_user_id"]);
			$str .= $user_list_view->getViewStr();
			
			$tmp_str = $str;
			while(self::findTags($tmp_str, $tag) !== false){
				$replace_with = self::replaceContent($tag, $edituser, false, false);
				
				$to_replace = $tag_mark.$tag.$tag_mark;
				
				$str = str_replace($to_replace, $replace_with, $str);
			}
		}
		
		return $str;
		
	}
	
	/**
	 * Function buildNewsList
	 * 
	 * Function builds a list of news based on the views and news articles in database. Returns string to be placed in view.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @return $str string
	 */
	public static function buildNewsList()
	{
		$str = "";
		$tag_mark = Bank::PARAM_TAG_MARK;
		$news = Sql_news::selectAllNews();
		
		$news_list_view = self::fetchHelperView(Bank::VIEW_ID_NEWS_LIST_CONTENT);
		
		foreach($news as $key -> $value) {
			$article = Ctrl_news::getNewsById($value["gs_news_id"]);
			$str .= $news_list_view->getViewStr();
			
			$tmp_str = $str;
			while(self::findTags($tmp_str, $tag) !== false){
				$replace_with = self::replaceContent($tag, $article->getAuthor(), false, $article);
				
				$to_replace = $tag_mark.$tag.$tag_mark;
				
				$str = str_replace($to_replace, $replace_with, $str);
			}
		}
		
		return $str;
	}
	
	/**
	 * Function fetchHelperView
	 * 
	 * Function returns a view object from helper views.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param $id int
	 * @return $view object
	 */
	public static function fetchHelperView($id)
	{
		// Get the database row into an array
		$result = Sql_view::selectHelperViewByParams($id);
		
		$view = new View();
		
		$view->setId($result[0]["gs_view_helper_id"]);
		$view->setViewStr($result[0]["gs_view_helper_str"]);
		
		return $view;
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
