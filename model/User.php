<?php

/**
 * Class User
 * 
 * User class.
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class User
{
	public $id;				// Int
	public $firstname;		// String
	public $lastname;		// String
	public $username;		// String
	public $password;		// String
	public $level;			// Int
	public $created;		// String
	public $activated;		// String
	public $email;			// String
	public $state;			// Int
	
	function __construct()
	{
		$this->id				= 0;
		$this->firstname		= "";
		$this->lastname			= "";
		$this->username			= "";
		$this->password			= "";
		$this->level			= 0;
		$this->created			= "";
		$this->activated		= "";
		$this->email			= "";
		$this->state			= 0;
	}
	
	public function getId()
	{
		return $this->id;
	}
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getFirstname()
	{
		return $this->firstname;
	}
	public function setFirstname($firstname)
	{
		$this->firstname = $firstname;
	}
	
	public function getLastname()
	{
		return $this->lastname;
	}
	public function setLastname($lastname)
	{
		$this->lastname = $lastname;
	}
	
	public function getUsername()
	{
		return $this->username;
	}
	public function setUsername($username)
	{
		$this->username = $username;
	}
	
	public function getPassword()
	{
		return $this->password;
	}
	public function setPassword($password)
	{
		$this->password = $password;
	}
	
	public function getLevel()
	{
		return $this->level;
	}
	public function setLevel($level)
	{
		$this->level = $level;
	}
	
	public function getCreated()
	{
		return $this->created;
	}
	public function setCreated($created)
	{
		$this->created = $created;
	}
	
	public function getActivated()
	{
		return $this->activated;
	}
	public function setActivated($activated)
	{
		$this->activated = $activated;
	}
	
	public function getEmail()
	{
		return $this->email;
	}
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	public function getState()
	{
		return $this->state;
	}
	public function setState($state)
	{
		$this->state = $state;
	}
}
?>
