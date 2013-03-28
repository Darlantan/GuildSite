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
		$user = self::getUserById($user_id);
		$edituser_id = 0;
		
		if(isset($post[Bank::INPUT_USER_ID])) {
			// If a user ID is submitted with the form, find the existing user.
			$edituser_id = $post[Bank::INPUT_USER_ID];
			$edituser = self::getUserById($edituser_id);
			if(!$edituser) {
				$edituser_id = 0;
			}
		}
		
		// Check whether all data is submitted. Currently required:
		// Firstname
		// Lastname
		// Email
		// Username
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
		
		if($edituser_id == 0) {
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
			
			$return_user_id = Sql_user::addUser($newuser);
			
			$log_type = Bank::LOG_TYPE_USER_REGISTERED;
			$log_info = Bank::LOG_INFO_USER_REGISTERED.$newuser->getUsername();
			Ctrl_log::createLogEvent($return_user_id, $log_type, $log_info);
			
			return $return_user_id;
		} else {
			// If user exists
			
			// User right level check
			if($user->getId() != $edituser->getId()) {
				// If editing user id is different than the user being edited
				if(($user->getLevel() < Bank::ALLOW_EDIT_USER_LEVEL) || ($user->getLevel() < $edituser->getLevel())) {
					// If editing users level is below the allowed edit user limit OR
					// if the editing users level is below the edited user, give error.
					$errors[] = Bank::ERROR_UNAUTHORIZED_EDIT;
					return $errors;
				}
			}
			
			if(!empty($post[Bank::INPUT_USER_RIGHT_LEVEL])) {
				// If any user right level was submitted, set that.
				$edituser->setLevel($post[Bank::INPUT_USER_RIGHT_LEVEL]);
			}
			
			$email = $post[Bank::INPUT_USER_EMAIL];
			if($email != $edituser->getEmail()) {
				// If email was updated
				// Select from database with new email
				$tmpresult = Sql_user::selectUserByEmail($email);
				if(!Ctrl_guildsite::validateEmail($email)){
					// If email is invalid, add error
					$errors[] = Bank::ERROR_INVALID_EMAIL;
				} else if($tmpresult && ($edituser->getId() != $tmpresult[Bank::INPUT_USER_ID])) {
					// If result is found, compare returned user ID with current user id. 
					// If there is no match, email is in use by someone else, return error.
					$errors[] = Bank::ERROR_EMAIL_IN_USE;
				} else {
					// Everything OK, set new email.
					$edituser->setEmail($email);
				}
			}
			
			$username = $post[Bank::INPUT_USER_USERNAME];
			if($username != $edituser->getUsername()) {
				// If username was updated
				// Select from database with new username
				$tmpresult = Sql_user::selectUserByUsername($username);
				if(strlen($username) < Bank::USERNAME_MIN_LENGTH) {
					// If username is too short
					$errors[] = Bank::ERROR_USERNAME_TOO_SHORT;
				} else if (strlen($username) > Bank::USERNAME_MAX_LENGTH) {
					// If username is too long
					$errors[] = Bank::ERROR_USERNAME_TOO_LONG;
				} else if($tmpresult && ($edituser->getId() != $tmpresult[Bank::INPUT_USER_ID])) {
					// If result is found, compare returned user ID with current user id.
					// If there is no match, username is in use by someone else, return error.
					$errors[] = Bank::ERROR_USERNAME_IN_USE;
				} else {
					// No problems, change username.
					$edituser->setUsername($username);
				}
			}
			
			$password1 = $post[Bank::INPUT_USER_PASSWORD];
			$password2 = $post[Bank::INPUT_USER_PASSWORD2];
			if(empty($password1) && empty($password2)) {
				// If both submitted passwords are empty, don't change anything.
			} else if($password1 === $password2 && strlen($password1) >= Bank::PASSWORD_MIN_LENGTH) {
				// If submitted passwords match and are long enough, crypt and set the password
				$edituser->setPassword(Ctrl_guildsite::createPassword($password1));
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
			
			// Set firstname and lastname from $post
			$edituser->setFirstname($post[Bank::INPUT_USER_FIRSTNAME]);
			$edituser->setLastname($post[Bank::INPUT_USER_LASTNAME]);
			
			$result = Sql_user::modifyUser($edituser);
			
			if($result == true) {
				if ($user->getLevel() > Bank::RIGHT_LEVEL_MEMBER) {
					// If user editing is an admin, use admin log variables.
					$log_type = Bank::LOG_TYPE_USER_UPDATED_BY_ADMIN;
					$log_info = Bank::LOG_INFO_USER_UPDATED_BY_ADMIN.$edituser->getUsername()." / ".$user->getUsername();
				} else {
					$log_type = Bank::LOG_TYPE_USER_UPDATED;
					$log_info = Bank::LOG_INFO_USER_UPDATED.$edituser->getUsername();
				}
				Ctrl_log::createLogEvent($edituser_id, $log_type, $log_info);
				
				return $edituser_id;
			} else {
				$errors[] = Bank::ERROR_UPDATE_FAILED;
				return $errors;
			}
		}
	}
	
	/**
	 * Function deleteUser
	 * 
	 * Function handles user rights for deletion and deletes the user requested.
	 * Returns true if user was deleted, errors array if not.
	 * 
	 * @author Iiro Vaahtojärvi
	 * @param $post array
	 * @param $user_id int
	 * @return $result
	 */
	public static function deleteUser($post, $user_id)
	{
		$user = self::getUserById($user_id);
		$deleteuser = self::getUserById($post[Bank::INPUT_USER_ID]);
		
		$errors = array();
		
		if($user->getId() != $deleteuser->getId()) {
			// If user isn't deleting self
			if(($user->getLevel() <= $deleteuser->getLevel()) && ($user->getLevel() != Bank::RIGHT_LEVEL_SUPERADMIN)) {
				// If user trying to delete another user has same level or lower or user isn't a superadmin, give error.
				$errors[] = Bank::ERROR_UNAUTHORIZED_DELETE;
			}
			
			if(!empty($errors)) {
				// If there were errors, return them.
				return $errors;
			} else {
				// No errors, delete user
				if(!Sql_user::deleteUser($deleteuser)) {
					// Delete was unsuccessful
					$errors[] = Bank::ERROR_DELETE_FAILED;
					return $errors;
				} else {
					// Delete was successful
					return true;
				}
			}
		}
	}
}
?>
