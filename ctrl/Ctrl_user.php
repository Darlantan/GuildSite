<?php

/**
 * Class Ctrl_user
 * 
 * Control class for user actions
 * 
 * @author Iiro Vaahtojärvi
 * @copyright Iiro Vaahtojärvi
 */
class Ctrl_user
{
	/**
	 * Function lookForUser
	 * 
	 * Function looks for a user based on username and password, returns a user object.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param string $username
	 * @param string $password
	 * @return object
	 */
	public static function lookForUser($username, $password)
	{
		$result = Sql_user::selectUserByPass($username, $password);
		$user = new User();
		$resultlength = count($result);
		// If the query for some reason returns more than one user, don't return a completed user.
		if($resultlength == 1) {
			$user_id = $result[0][Bank::DB_TABLE_USER."_id"];
			$user = self::getUserById($user_id);
		}
		return $user;
	}
	
	/**
	 * Function getUserById
	 * 
	 * Function builds a full user object based on ID.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param int $user_id
	 * @return object
	 */
	public static function getUserById($user_id)
	{
		$result = Sql_user::selectUserById($user_id);
		$user = new User();
		$user->setId($user_id);
		$user->setFirstname($result[0]["gs_user_firstname"]);
		$user->setLastname($result[0]["gs_user_lastname"]);
		$user->setEmail($result[0]["gs_user_email"]);
		$user->setUsername($result[0]["gs_user_username"]);
		$user->setPassword($result[0]["gs_user_password"]);
		$user->setCreated($result[0]["gs_user_date_of_join"]);
		$user->setState($result[0]["gs_user_state"]);
		$user->setLevel($result[0]["gs_user_level"]);
		
		return $user;
	}
	
	/**
	 * Function modifyUserData
	 * 
	 * Modifies and updates existing user data, if no user ID is found, create a new user. Returns array of errors if any are found.
	 * This function also goes through validating the submitted user data.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param array $post
	 * @param int $user_id
	 * @return array
	 */
	public static function modifyUserData($post, $user_id)
	{
		if(isset($post[Bank::INPUT_USER_ID])) {
			$user_id = $post[Bank::INPUT_USER_ID];
		}
		$user = self::getUserById($user_id);
		
		// Check whether all data is submitted
		if(empty($post[Bank::INPUT_USER_FIRSTNAME])) {
			$errors[] = Bank::ERROR_MISSING_FIRSTNAME;
		}
		if(empty($post[Bank::INPUT_USER_LASTNAME])) {
			$errors[] = Bank::ERROR_MISSING_LASTNAME;
		}
		if(empty($post[Bank::INPUT_USER_EMAIL])) {
			$errors[] = Bank::ERROR_MISSING_EMAIL;
		}
		if(empty($post[Bank::INPUT_USER_USERNAME])) {
			$errors[] = Bank::ERROR_MISSING_USERNAME;
		}
		
		// If any data was missing, stop execution and return errors.
		if(empty($errors) === false) {
			return $errors;
		}
		
		if($user_id == 0) {
			// If user doesn't exist
			
			if(empty($post[Bank::INPUT_USER_PASSWORD])) {
				$errors[] = Bank::ERROR_MISSING_PASSWORD;
			}
			if(empty($post[Bank::INPUT_USER_PASSWORD2])) {
				$errors[] = Bank::ERROR_MISSING_PASSWORD2;
			}
			
			$newuser = new User();
			$newuser->setFirstname($post[Bank::INPUT_USER_FIRSTNAME]);
			$newuser->setLastname($post[Bank::INPUT_USER_LASTNAME]);
			$newuser->setCreated(date("Y-m-d H:i:s"));
			$newuser->setLevel(Bank::RIGHT_LEVEL_USER);
			$newuser->setState(Bank::USER_STATE_ACTIVE);
			
			$email = $post[Bank::INPUT_USER_EMAIL];
			if(!Ctrl_guildsite::validateEmail($email)) {
				// If email is invalid add an errors
				$errors[] = Bank::ERROR_INVALID_EMAIL;
			} else if(Sql_user::selectUserByEmail($email)) {
				// If email is already in use, add an error
				$errors[] = Bank::ERROR_EMAIL_IN_USE;
			} else {
				// If email is valid, add it to $newuser
				$newuser->setEmail($email);
			}
			
			$username = $post[Bank::INPUT_USER_USERNAME];
			if(Sql_user::selectUserByUsername($username)) {
				// If username already exists
				$errors[] = Bank::ERROR_USERNAME_IN_USE;
			} else if(strlen($username) < Bank::USERNAME_MIN_LENGTH) {
				// If username is too short
				$errors[] = Bank::ERROR_USERNAME_TOO_SHORT;
			} else if(strlen($username) > Bank::USERNAME_MAX_LENGTH) {
				// If username is too long
				$errors[] = Bank::ERROR_USERNAME_TOO_LONG;
			} else {
				// If all is well
				$newuser->setUsername($username);
			}
			
			$password1 = $post[Bank::INPUT_USER_PASSWORD];
			$password2 = $post[Bank::INPUT_USER_PASSWORD2];
			if(strlen($password1) < Bank::PASSWORD_MIN_LENGTH) {
				// If submitted password is too short
			}
			if($password1 === $password2 && strlen($password1) >= Bank::PASSWORD_MIN_LENGTH) {
				// If submitted passwords match and are long enough, crypt the password
				$newuser->setPassword(Ctrl_guildsite::createPassword($password1));
			} else if($password1 !== $password2) {
				// If submitted passwords don't match, add an error.
				$errors[] = Bank::ERROR_PASSWORD_MISMATCH;
			} else if(strlen($password1) < Bank::PASSWORD_MIN_LENGTH) {
				// If submitted password is too short
				$errors[] = Bank::ERROR_PASSWORD_TOO_SHORT;
			}
			
			// If there were no errors, create the user and log the event. Else return errors array.
			if(empty($errors) === false) {
				return $errors;
			}
			
			$user_id = Sql_user::addUser($newuser);
			
			return $user_id;
		} else {
			// If user exists
			$newuser = new User();
			$newuser->setId($user_id);
			$newuser->setFirstname($post[Bank::INPUT_USER_FIRSTNAME]);
			$newuser->setLastname($post[Bank::INPUT_USER_LASTNAME]);
			$newuser->setState(Bank::USER_STATE_ACTIVE);
			
			if(empty($post[Bank::INPUT_USER_RIGHT_LEVEL])) {
				$newuser->setLevel($user->getLevel());
			} else {
				$newuser->setLevel($post[Bank::INPUT_USER_RIGHT_LEVEL]);
			}
			
			$email = $post[Bank::INPUT_USER_EMAIL];
			if($email == $user->getEmail()) {
				$newuser->setEmail($email);
			} else if(!Ctrl_guildsite::validateEmail($email)) {
				// If email is invalid add an errors
				$errors[] = Bank::ERROR_INVALID_EMAIL;
			} else if(Sql_user::selectUserByEmail($email)) {
				// If email is already in use, add an error
				$errors[] = Bank::ERROR_EMAIL_IN_USE;
			} else {
				// If email is valid, add it to $newuser
				$newuser->setEmail($email);
			}
			
			$username = $post[Bank::INPUT_USER_USERNAME];
			if($username == $user->getUsername()) {
				$newuser->setUsername($username);
			} else if(Sql_user::selectUserByUsername($username)) {
				// If username already exists
				$errors[] = Bank::ERROR_USERNAME_IN_USE;
			} else if(strlen($username) < Bank::USERNAME_MIN_LENGTH) {
				// If username is too short
				$errors[] = Bank::ERROR_USERNAME_TOO_SHORT;
			} else if(strlen($username) > Bank::USERNAME_MAX_LENGTH) {
				// If username is too long
				$errors[] = Bank::ERROR_USERNAME_TOO_LONG;
			} else {
				// If all is well
				$newuser->setUsername($username);
			}
			
			$password1 = $post[Bank::INPUT_USER_PASSWORD];
			$password2 = $post[Bank::INPUT_USER_PASSWORD2];
			if(empty($password1) && empty($password2)) {
				$newuser->setPassword($user->getPassword());
			} else if($password1 === $password2 && strlen($password1) >= Bank::PASSWORD_MIN_LENGTH) {
				// If submitted passwords match and are long enough, crypt the password
				$newuser->setPassword(Ctrl_guildsite::createPassword($password1));
			} else if($password1 !== $password2) {
				// If submitted passwords don't match, add an error.
				$errors[] = Bank::ERROR_PASSWORD_MISMATCH;
			} else if(strlen($password1) < Bank::PASSWORD_MIN_LENGTH) {
				// If submitted password is too short
				$errors[] = Bank::ERROR_PASSWORD_TOO_SHORT;
			}
			
			// If there were no errors, create the user and log the event. Else return errors array.
			if(empty($errors) === false) {
				return $errors;
			}
			
			$result = Sql_user::modifyUser($newuser);
			
			if($result == true) {
				return $user_id;
			} else {
				$errors[] = Bank::ERROR_UPDATE_FAILED;
				return $errors;
			}
		}
	}
}
?>