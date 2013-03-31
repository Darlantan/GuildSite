<?php

/**
 * Class Ctrl_news
 * 
 * Control class for news article handling.
 *
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class Ctrl_news
{
	/**
	 * Function modifyNewsData
	 * 
	 * Function adds a new news article. Handles validation too.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param $post array
	 * @param $user_id int
	 * @return $result
	 */
	public static function modifyNewsData($post, $user_id)
	{
		$user = Ctrl_user::getUserById($user_id);
		
		if($post[Bank::INPUT_NEWS_ID]) {
			// If ID is sent, modify existing news article
			$editnews = self::getNewsById($post[Bank::INPUT_NEWS_ID]);
		} else {
			// If ID is not sent, news article is new.
			$editnews = new News();
			
			$title = $post[Bank::INPUT_NEWS_TITLE];
			$str = $post[Bank::INPUT_NEWS_STR];
			$errors = array();
			
			self::parseNewsItem($title, $str, $errors);
			
			if(!empty($errors)) {
				// If errors found, return them.
				return $errors;
			}
			
			$editnews->setTitle($title);
			$editnews->setStr($str);
			$editnews->setAuthor($user->getId());
			$editnews->setDate(date("Y-m-d H:i:s"));
			
			return Sql_news::addNews($editnews, $user->getId());
		}
		
	}
	
	/**
	 * Function parseNewsItem
	 * 
	 * Function parses submitted news article, title and body.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param $title str
	 * @param $str str
	 * @param $errors array
	 */
	private static function parseNewsItem(&$title, &$str, &$errors)
	{
		// Escape the string for mysql.
		$title = mysql_real_escape_string($title);
		$str = mysql_real_escape_string($str);
	}
	
	/**
	 * Function getNewsById
	 * 
	 * Function fetches a news article by article id.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param $id int
	 * $return $article object
	 */
	public static function getNewsById($id)
	{
		$article = new News();
		
		$result = Sql_news::selectNewsById($id);
		
		// Set author as a user object within the news object
		$author = Ctrl_user::getUserById($result[0]["gs_user_id"]);
		
		$article->setId($id);
		$article->setTitle($result[0]["gs_news_title"]);
		$article->setStr($result[0]["gs_news_str"]);
		$article->setDate($result[0]["gs_news_date"]);
		$article->setEdited($result[0]["gs_news_date_edited"]);
		$article->setAuthor($author);
		
		return $article;
	}
}

?>
