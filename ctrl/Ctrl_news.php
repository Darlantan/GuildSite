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
	 * Function addNews
	 * 
	 * Function adds a new news article and parses it.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param $post array
	 * @return $result
	 */
	
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
