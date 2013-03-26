<?php

/**
 * Class Bank
 * 
 * Bank holds all constants used by GuildSite. Basically this is the place for variables that classes use.
 * 
 * @copyright Iiro Vaahtojärvi
 * @author Iiro Vaahtojärvi
 */

final class Bank
{
	// Database connection info
	const DB_HOST					= "localhost";
	const DB_NAME					= "scytheguildsite";
	const DB_USER					= "guildsiteadmin";
	const DB_PASS					= "adminguildsit3!";

	// Site name variable. Needs to be different for each site instance.
	const GUILDSITE_NAME			= "scythe";
	
	// System pages
	const SYSTEM_INDEX_PAGE			= "index.php";
	const SYSTEM_TEST_PAGE			= "index.php";
	
	// Feature switches
	const EMAIL_SENDING_ACTIVE		= true;
	
	// Right levels
	const RIGHT_LEVEL_NONE			= 0; // No need for logging in
	const RIGHT_LEVEL_USER			= 1; // Logged in user
	const RIGHT_LEVEL_MEMBER		= 2; // Logged in with member level access
	const RIGHT_LEVEL_MODERATOR		= 3; // Logged in with moderator level access
	const RIGHT_LEVEL_ADMIN			= 4; // Logged in with admin level access
	const RIGHT_LEVEL_SUPERADMIN	= 5; // Logged in with system admin level access (might not be needed)
	
	// User states
	const USER_STATE_INACTIVE		= 0; // Hasn't activated account yet. No right to log in etc.
	const USER_STATE_ACTIVE			= 1; // Activated user, email verified.
	
	// Restricting vars
	const PASSWORD_MIN_LENGTH		= 6;
	const USERNAME_MIN_LENGTH		= 3;
	const USERNAME_MAX_LENGTH		= 25;
	
	// Page ID's (from DB: gs_view)
	const PAGE_ID_FRONTPAGE			= 1;
	const PAGE_ID_LOGOUT			= 2;
	const PAGE_ID_UNAUTHORIZED		= 3;
	const PAGE_ID_404				= 4;
	const PAGE_ID_JOIN				= 10;
	const PAGE_ID_JOINED_USER		= 11;
	const PAGE_ID_DEFAULT_USER		= 100;
	const PAGE_ID_EDIT_USER			= 101;
	const PAGE_ID_MODIFIED_USER		= 102;
	const PAGE_ID_DEFAULT_MEMBER	= 200;
	const PAGE_ID_DEFAULT_MODERATOR	= 300;
	const PAGE_ID_DEFAULT_ADMIN		= 400;
	const PAGE_ID_ADMIN_EDIT_USER	= 401;
	const PAGE_ID_USER_LIST			= 402;
	
	// Helper view ID's (from DB: gs_view_helper)
	const VIEW_ID_ERRORTEMPLATE		= 1;
	const VIEW_ID_USER_LIST_TEMPLATE= 2;
	const VIEW_ID_USER_LIST_CONTENT	= 3;
	
	// Error message id's (from DB: gs_error)
	const ERROR_LOGIN_FAILED		= 1;
	const ERROR_PASSWORD_MISMATCH	= 2;
	const ERROR_PASSWORD_TOO_SHORT	= 3;
	const ERROR_INVALID_EMAIL		= 4;
	const ERROR_USERNAME_TOO_SHORT	= 5;
	const ERROR_USERNAME_TOO_LONG	= 6;
	const ERROR_MISSING_FIRSTNAME	= 7;
	const ERROR_MISSING_LASTNAME	= 8;
	const ERROR_MISSING_EMAIL		= 9;
	const ERROR_MISSING_USERNAME	= 10;
	const ERROR_MISSING_PASSWORD	= 11;
	const ERROR_MISSING_PASSWORD2	= 11;
	const ERROR_EMAIL_IN_USE		= 12;
	const ERROR_USERNAME_IN_USE		= 13;
	const ERROR_UPDATE_FAILED		= 14;
	
	// Log ID's
	const LOG_TYPE_USER_LOGIN		= 1;
	const LOG_TYPE_MEMBER_LOGIN		= 2;
	const LOG_TYPE_MODERATOR_LOGIN	= 3;
	const LOG_TYPE_ADMIN_LOGIN		= 4;
	const LOG_TYPE_SUPERADMIN_LOGIN	= 5;
	const LOG_TYPE_LOGIN_INVALID	= 6;
	const LOG_TYPE_PAGE_NOT_FOUND	= 7;
	const LOG_TYPE_UNAUTHORIZED		= 8;
	
	// Log info strings
	const LOG_INFO_USER_LOGIN		= "User logged in successfully from IP: ";
	const LOG_INFO_MEMBER_LOGIN		= "Member logged in successfully from IP: ";
	const LOG_INFO_MODERATOR_LOGIN	= "Moderator logged in successfully from IP: ";
	const LOG_INFO_ADMIN_LOGIN		= "Admin logged in successfully from IP: ";
	const LOG_INFO_SUPERADMIN_LOGIN	= "Superadmin logged in successfully from IP: ";
	const LOG_INFO_LOGIN_INVALID	= "Invalid login: ";
	const LOG_INFO_PAGE_NOT_FOUND	= "User tried to access non-existing page: ";
	const LOG_INFO_UNAUTHORIZED		= "User tried to access unauthorized page: ";
	
	// System reserved tags
	const PARAM_TAG_MARK			= "##";
	const TAG_CONTENT				= "##CONTENT##";
	const FORM_ERRORS				= "##FORM_ERRORS##";
	const ERROR_CONTENTS			= "##ERROR_CONTENTS##";
	const MENU_TAG					= "##MENU##";
	const EXTRA_CONTENT				= "##EXTRA_CONTENT##";
	const USER_LIST					= "##USER_LIST##";
	const USER_LIST_CONTENT			= "##USER_LIST_CONTENT##";
	
	// Possible values for strings inside tags (##something##)
	const TAG_USER_ID				= "USER_ID";
	const TAG_FIRSTNAME				= "FIRSTNAME";
	const TAG_LASTNAME				= "LASTNAME";
	const TAG_EMAIL					= "EMAIL";
	const TAG_USERNAME				= "USERNAME";
	const TAG_EDIT_USER_ID			= "EDIT_USER_ID";
	const TAG_EDIT_FIRSTNAME		= "EDIT_FIRSTNAME";
	const TAG_EDIT_LASTNAME			= "EDIT_LASTNAME";
	const TAG_EDIT_EMAIL			= "EDIT_EMAIL";
	const TAG_EDIT_USERNAME			= "EDIT_USERNAME";
	
	/* 
	 * Input names. The values of these must be used in view strings in order for the functionality to work.
	 * For example <input type="button" name="SUBMIT_LOGIN" value="Log in!" />
	 */
	// Submits
	const SUBMIT_LOGIN				= "SUBMIT_LOGIN";
	const SUBMIT_USER_JOIN			= "SUBMIT_USER_JOIN";
	const SUBMIT_USER_EDIT			= "SUBMIT_USER_EDIT";
	const SUBMIT_EDIT_USER			= "SUBMIT_EDIT_USER";
	
	// Inputs
	const INPUT_USER_ID				= "INPUT_USER_ID";
	const INPUT_USER_FIRSTNAME		= "INPUT_USER_FIRSTNAME";
	const INPUT_USER_LASTNAME		= "INPUT_USER_LASTNAME";
	const INPUT_USER_EMAIL			= "INPUT_USER_EMAIL";
	const INPUT_USER_USERNAME		= "INPUT_USER_USERNAME";
	const INPUT_USER_PASSWORD		= "INPUT_USER_PASSWORD";
	const INPUT_USER_PASSWORD2		= "INPUT_USER_PASSWORD2";
	const INPUT_USER_RIGHT_LEVEL	= "INPUT_USER_RIGHT_LEVEL";
	
	// Database table names
	const DB_TABLE_USER				= "gs_user";
	const DB_TABLE_LOG				= "gs_log";
	const DB_TABLE_ERROR			= "gs_error";
	const DB_TABLE_VIEW				= "gs_view";
}
?>
