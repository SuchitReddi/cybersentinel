<?php

/*

This file contains all of the code to setup the initial MySQL database. (setup.php)

*/

if( !defined( 'SENTINEL_WEB_PAGE_TO_ROOT' ) ) {
	define( 'SENTINEL_WEB_PAGE_TO_ROOT', '../../../' );
}

if( !@($GLOBALS["___mysqli_ston"] = mysqli_connect( $_SENTINEL[ 'db_server' ],  $_SENTINEL[ 'db_user' ],  $_SENTINEL[ 'db_password' ], "", $_SENTINEL[ 'db_port' ] )) ) {
	sentinelMessagePush( "Could not connect to the database service.<br />Please check the config file.<br />Database Error #" . mysqli_connect_errno() . ": " . mysqli_connect_error() . "." );
	if ($_SENTINEL[ 'db_user' ] == "root") {
		sentinelMessagePush( 'Your database user is root, if you are using MariaDB, this will not work, please read the README.md file.' );
	}
	sentinelPageReload();
}

// Create database
$drop_db = "DROP DATABASE IF EXISTS {$_SENTINEL[ 'db_database' ]};";
if( !@mysqli_query($GLOBALS["___mysqli_ston"],  $drop_db ) ) {
	sentinelMessagePush( "Could not drop existing database<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	sentinelPageReload();
}

$create_db = "CREATE DATABASE {$_SENTINEL[ 'db_database' ]};";
if( !@mysqli_query($GLOBALS["___mysqli_ston"],  $create_db ) ) {
	sentinelMessagePush( "Could not create database<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	sentinelPageReload();
}
sentinelMessagePush( "Database has been created." );


// Create table 'users'
if( !@((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $_SENTINEL[ 'db_database' ])) ) {
	sentinelMessagePush( 'Could not connect to database.' );
	sentinelPageReload();
}

$create_tb = "CREATE TABLE users (user_id int(6),first_name varchar(15),last_name varchar(15), user varchar(15), password varchar(32),avatar varchar(70), last_login TIMESTAMP, failed_login INT(3), PRIMARY KEY (user_id));";
if( !mysqli_query($GLOBALS["___mysqli_ston"],  $create_tb ) ) {
	sentinelMessagePush( "Table could not be created<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	sentinelPageReload();
}
sentinelMessagePush( "'users' table was created." );


// Insert some data into users
$base_dir= str_replace ("setup.php", "", $_SERVER['SCRIPT_NAME']);
$avatarUrl  = $base_dir . 'docs/users/';

$insert = "INSERT INTO users VALUES
	('1','admin','admin','admin',MD5('password'),'{$avatarUrl}admin.jpg', NOW(), '0'),
	('2','Gordon','Brown','gordonb',MD5('abc123'),'{$avatarUrl}gordonb.jpg', NOW(), '0'),
	('3','Hack','Me','1337',MD5('charley'),'{$avatarUrl}1337.jpg', NOW(), '0'),
	('4','Pablo','Picasso','pablo',MD5('letmein'),'{$avatarUrl}pablo.jpg', NOW(), '0'),
	('5','Bob','Smith','smithy',MD5('password'),'{$avatarUrl}smithy.jpg', NOW(), '0');";
if( !mysqli_query($GLOBALS["___mysqli_ston"],  $insert ) ) {
	sentinelMessagePush( "Data could not be inserted into 'users' table<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	sentinelPageReload();
}
sentinelMessagePush( "Data inserted into 'users' table." );


// Create guestbook table
$create_tb_guestbook = "CREATE TABLE guestbook (comment_id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT, comment varchar(300), name varchar(100), PRIMARY KEY (comment_id));";
if( !mysqli_query($GLOBALS["___mysqli_ston"],  $create_tb_guestbook ) ) {
	sentinelMessagePush( "Table could not be created<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	sentinelPageReload();
}
sentinelMessagePush( "'guestbook' table was created." );


// Insert data into 'guestbook'
$insert = "INSERT INTO guestbook VALUES ('1','I might store my malicious code here. Just for a little while, I promise!','Dear User!');";
if( !mysqli_query($GLOBALS["___mysqli_ston"],  $insert ) ) {
	sentinelMessagePush( "Data could not be inserted into 'guestbook' table<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
	sentinelPageReload();
}
sentinelMessagePush( "Data inserted into 'guestbook' table." );




// Copy .bak for a fun directory listing vuln
$conf = SENTINEL_WEB_PAGE_TO_ROOT . 'config/config.inc.php';
$bakconf = SENTINEL_WEB_PAGE_TO_ROOT . 'config/config.inc.php.bak';
if (file_exists($conf)) {
	// Who cares if it fails. Suppress.
	@copy($conf, $bakconf);
}

sentinelMessagePush( "Backup file /config/config.inc.php.bak automatically created" );

// Done
sentinelMessagePush( "<em>Setup successful</em>!" );

if( !sentinelIsLoggedIn())
	sentinelMessagePush( "Please <a href='login.php'>login</a>.<script>setTimeout(function(){window.location.href='login.php'},5000);</script>" );
sentinelPageReload();

?>
