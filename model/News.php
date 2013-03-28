<?php

/**
 * Class News
 *
 * News class.
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class News
{
	public $id;			// Int
	public $title;		// String
	public $str;		// String
	public $date;		// String
	public $edited;		// String
	public $author;		// Int
	
	function __construct()
	{
		$this->id		= 0;
		$this->title	= "";
		$this->str		= "";
		$this->date		= "";
		$this->edited	= "";
		$this->author	= 0;
	}
	
	public function getId()
	{
		return $this->id;
	}
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getTitle()
	{
		return $this->id;
	}
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getStr()
	{
		return $this->str;
	}
	public function setStr($str)
	{
		$this->str = $str;
	}
	
	public function getDate()
	{
		return $this->date;
	}
	public function setDate($date)
	{
		$this->date = $date;
	}
	
	public function getEdited()
	{
		return $this->edited;
	}
	public function setEdited($edited)
	{
		$this->edited = $edited;
	}
	
	public function getAuthor()
	{
		return $this->author;
	}
	public function setAuthor($author)
	{
		$this->author = $author;
	}
}

?>
