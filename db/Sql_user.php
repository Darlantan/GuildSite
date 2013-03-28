<?php

/**
 * Class Sql_user
 * 
 * Contains functions for user handling
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class Sql_user extends Sql
{
	/**
	 * Function selectUserByPass
	 * 
	 * Selects one user with the username and password.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $username
	 * @param string $password
	 * @return array
	 */
	public static function selectUserByPass($username, $password)
	{
		$table = Bank::DB_TABLE_USER;
		$query = "SELECT ".$table."_id, ".$table."_username, ".$table."_level FROM ".$table." WHERE ".$table."_username = '".$username."' AND ".$table."_password = '".$password."' AND ".$table."_state = ".Bank::USER_STATE_ACTIVE;
		return parent::select($query);
	}
	
	/**
	 * Function selectUserById
	 * 
	 * Selects one user based on user ID.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $user_id
	 * @return array
	 */
	public static function selectUserById($user_id)
	{
		$query = "SELECT gs_user_id, gs_user_firstname, gs_user_lastname, gs_user_email, gs_user_username, gs_user_password, gs_user_date_of_join, gs_user_date_of_activation, gs_user_state, gs_user_level FROM gs_user WHERE gs_user_id = ".$user_id;
		return parent::select($query);
	}
	
	/**
	 * Function selectUserByEmail
	 * 
	 * Selects one user based on email address.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $email
	 * @return array
	 */
	public static function selectUserByEmail($email)
	{
		$query = "SELECT gs_user_id, gs_user_firstname, gs_user_lastname, gs_user_email, gs_user_username, gs_user_date_of_join, gs_user_date_of_activation, gs_user_state, gs_user_level FROM gs_user WHERE gs_user_email = '".$email."'";
		return parent::select($query);
	}
	
	/**
	 * Function selectUserByUsername
	 * 
	 * Selects one user based on username.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $username
	 * @return array
	 */
	public static function selectUserByUsername($username)
	{
		$query = "SELECT gs_user_id, gs_user_firstname, gs_user_lastname, gs_user_email, gs_user_username, gs_user_date_of_join, gs_user_date_of_activation, gs_user_state, gs_user_level FROM gs_user WHERE gs_user_username = '".$username."'";
		return parent::select($query);
	}
	
	/**
	 * Function selectAllUsers
	 * 
	 * Function selects all users.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @return array
	 */
	public static function selectAllUsers()
	{
		$query = "SELECT gs_user_id FROM gs_user";
		return parent::select($query);
	}
	
	/**
	 * Function addUser
	 * 
	 * Adds one new user. Returns new user's ID.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param object $user
	 * @return int
	 */
	public static function addUser($user)
	{
		$query = "INSERT INTO gs_user(gs_user_firstname, gs_user_lastname, gs_user_email, gs_user_username, gs_user_password, gs_user_date_of_join, gs_user_state, gs_user_level) VALUES ('".$user->getFirstname()."', '".$user->getLastname()."', '".$user->getEmail()."', '".$user->getUsername()."', '".$user->getPassword()."', '".$user->getCreated()."', ".$user->getState().", ".$user->getLevel().")";
		return parent::insertWithId($query);
		
	}
	
	/**
	 * Function modifyUser
	 * 
	 * Modifies an existing user.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param object $user
	 * @return boolean
	 */
	public static function modifyUser($user)
	{
		$query = "UPDATE gs_user SET gs_user_firstname = '".$user->getFirstname()."', gs_user_lastname = '".$user->getLastname()."', gs_user_email = '".$user->getEmail()."', gs_user_username = '".$user->getUsername()."', gs_user_password = '".$user->getPassword()."', gs_user_state = ".$user->getState().", gs_user_level = ".$user->getLevel()." WHERE gs_user_id = ".$user->getId();
		return parent::update($query);
	}
	
	/**
	 * Function deleteUser
	 * 
	 * Deletes an existing user.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param object $user
	 * @return boolean
	 */
	public static function deleteUser($user)
	{
		$query = "DELETE FROM gs_user WHERE gs_user_id = ".$user->getId();
		return parent::delete($query);
	}
	
}
?>
