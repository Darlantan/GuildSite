<?php
/**
 * Index file, all GuildSite requests go through a similar file
 * 
 * @copyright Iiro Vaahtojärvi
 * @author Iiro Vaahtojärvi
 */

// Load classes and server paths
include_once("../supplement/Path.php");

// Unset global variables
$get    = $_GET;
$post   = $_POST;
$files  = $_FILES;
$ip     = $_SERVER["REMOTE_ADDR"];

unset($_GET);
unset($_POST);
unset($_FILES);
unset($_SERVER);

// Run GuildSite
Ctrl_guildsite::runGuildSite($get, $post, $files, $ip);

?>