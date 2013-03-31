<?php

/**
 * Class Sql_news
 * 
 * Contains functions for news article management.
 *
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class Sql_news extends Sql
{
	/**
	 * Function selectAllNews
	 * 
	 * Function selects all ids of news from the database and returns an array.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @return $result array
	 */
	public static function selectAllNews()
	{
		$query = "SELECT gs_news_id FROM gs_news";
		return parent::select($query);
	}
	
	/**
	 * Function selectNewsById
	 * 
	 * Function selects a news article based on the article ID given.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param $id int
	 * @return $result array
	 */
	public static function selectNewsById($id)
	{
		$query = "SELECT * FROM gs_news WHERE gs_news_id = ".$id;
		return parent::select($query);
	}
	
	/**
	 * Function addNews
	 * 
	 * Function inserts a new news article. Returns new article id.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param $news object
	 * @param $user_id int
	 */
	public static function addNews($news, $user_id)
	{
		$query = "INSERT INTO gs_news(gs_news_title, gs_news_str, gs_news_date, gs_user_id) VALUES ('".$news->getTitle()."', '".$news->getStr()."', '".$news->getDate()."', ".$user_id.")";
		return parent::insertWithId($query);
	}
}

?>
