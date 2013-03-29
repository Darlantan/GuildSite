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
}

?>
