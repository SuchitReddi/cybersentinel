<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( ) );

if (!@($GLOBALS["___mysqli_ston"] = mysqli_connect($_SENTINEL['db_server'], $_SENTINEL['db_user'], $_SENTINEL['db_password'], "", $_SENTINEL['db_port']))) {
    sentinelMessagePush("Could not connect to the database service.<br />Please check the config file.<br />Database Error #" . mysqli_connect_errno() . ": " . mysqli_connect_error() . ".");
    if ($_SENTINEL['db_user'] == "root") {
        sentinelMessagePush('Your database user is root, if you are using MariaDB, this will not work, please read the README.md file.');
    }
    sentinelPageReload();
}

// Check if the 'sentinel' database exists. If the database doesn't exist, we create it automatically. If it does, user can reset it.
$databaseExistsQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$_SENTINEL['db_database']}'";
$databaseExistsResult = mysqli_query($GLOBALS["___mysqli_ston"], $databaseExistsQuery);

if (!$databaseExistsResult || mysqli_num_rows($databaseExistsResult) == 0) {
    // The 'sentinel' database doesn't exist, create it
	// Create database
	$drop_db = "DROP DATABASE IF EXISTS {$_SENTINEL[ 'db_database' ]};"; // Do not drop the database //
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
	
	//$create_tb_users = "CREATE TABLE users (user_id int(6),first_name varchar(15),last_name varchar(15), user varchar(15), password varchar(32),avatar varchar(70), last_login TIMESTAMP, failed_login INT(3), PRIMARY KEY (user_id));";
	$create_tb_users = "CREATE TABLE users (user_id int(6) auto_increment, first_name varchar(15), last_name varchar(15), user varchar(15), password varchar(32), last_login TIMESTAMP default NOW(), failed_login INT(3) default '0', PRIMARY KEY (user_id));";
	if( !mysqli_query($GLOBALS["___mysqli_ston"],  $create_tb_users ) ) {
		sentinelMessagePush( "Table could not be created<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
		sentinelPageReload();
	}
	sentinelMessagePush( "'users' table was created." );

	// Insert some data into users
	$base_dir= str_replace ("setup.php", "", $_SERVER['SCRIPT_NAME']);
	//$avatarUrl  = $base_dir . 'docs/users/';

	//$enteredPassword = password_hash($enteredPassword, PASSWORD_DEFAULT); // More secure way.
	$insert = "INSERT INTO users (first_name, last_name, user, password) VALUES
		('admin','admin','admin',MD5('password')),
		('Gordon','Brown','gordonb',MD5('abc123')),
		('Hack','Me','1337',MD5('charley')),
		('Pablo','Picasso','pablo',MD5('letmein')),
		('Bob','Smith','smithy',MD5('password'));";
	if( !mysqli_query($GLOBALS["___mysqli_ston"],  $insert ) ) {
		sentinelMessagePush( "Data could not be inserted into 'users' table<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
		sentinelPageReload();
	}
	sentinelMessagePush( "Data inserted into 'users' table." );

	// Create table 'new_users' for signups
	$create_tb_new_users = "CREATE TABLE new_users (new_user_id int(6) auto_increment, new_first_name varchar(15), new_last_name varchar(15), new_user varchar(15), new_password varchar(32), created_time TIMESTAMP default NOW(), failed_login INT(3) default '0',PRIMARY KEY (new_user_id));";
	mysqli_select_db($GLOBALS["___mysqli_ston"],  $_SENTINEL[ 'db_database' ] );
	if( !@mysqli_query($GLOBALS["___mysqli_ston"],  $create_tb_new_users ) ) {
		sentinelMessagePush( "Table could not be created<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
		sentinelPageReload();
	}
	sentinelMessagePush( "'new_users' table was created." );

	// Create trigger to add new users to users table
	$create_trigger_add_user = "CREATE TRIGGER add_user AFTER INSERT ON users FOR EACH ROW INSERT INTO new_users (new_user_id, new_first_name, new_last_name, new_user, new_password) VALUES (NEW.user_id, NEW.first_name, NEW.last_name, NEW.user, NEW.password);";
	if( !@mysqli_query($GLOBALS["___mysqli_ston"],  $create_trigger_add_user ) ) {
		sentinelMessagePush( "Trigger could not be created<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
		sentinelPageReload();
	}
	sentinelMessagePush( "'add_user' trigger was created." );

	// Create old_users table for deleted users.
	$create_tb_old_users = "CREATE TABLE old_users (old_user_id int(6) auto_increment, old_first_name varchar(15), old_last_name varchar(15), old_user varchar(15), old_password varchar(32), deleted_time TIMESTAMP default NOW(), failed_login INT(3) default '0', PRIMARY KEY (old_user_id));";
	if( !mysqli_query($GLOBALS["___mysqli_ston"],  $create_tb_old_users ) ) {
		sentinelMessagePush( "Table could not be created<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
		sentinelPageReload();
	}
	sentinelMessagePush( "'old_users' table was created." );

	// Create trigger to add deleted users to old_users table
	$create_trigger_remove_user = "CREATE TRIGGER remove_user AFTER DELETE ON users FOR EACH ROW INSERT INTO old_users (old_user_id, old_first_name, old_last_name, old_user, old_password) VALUES (OLD.user_id, OLD.first_name, OLD.last_name, OLD.user, OLD.password);";
	if( !@mysqli_query($GLOBALS["___mysqli_ston"],  $create_trigger_remove_user ) ) {
		sentinelMessagePush( "Trigger could not be created<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
		sentinelPageReload();
	}
	sentinelMessagePush( "'remove_user' trigger was created." );

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

	/* // Copy .bak for a fun directory listing vuln
	$conf = SENTINEL_WEB_PAGE_TO_ROOT . 'config/config.inc.php';
	$bakconf = SENTINEL_WEB_PAGE_TO_ROOT . 'config/config.inc.php.bak';
	if (file_exists($conf)) {
		// Who cares if it fails. Suppress.
		@copy($conf, $bakconf);
	}

	sentinelMessagePush( "Backup file /config/config.inc.php.bak automatically created" );
	*/

	// Create logs table
	$create_tb_logs = "CREATE TABLE logs (log_id INT NOT NULL AUTO_INCREMENT, user varchar(15), ip TEXT, visited TEXT, time DATETIME, PRIMARY KEY (log_id));";
	if( !mysqli_query($GLOBALS["___mysqli_ston"],  $create_tb_logs ) ) {
		sentinelMessagePush( "Log table could not be created<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
		sentinelPageReload();
	}
	sentinelMessagePush( "'Logs' table was created." );

	/* //You can test log table by uncommenting this code
	// Insert data into 'logs'
	$insert = "INSERT INTO logs (user, ip, visited, time) VALUES ('admin', '127.0.0.1', 'index.php', NOW());"; 
	if( !mysqli_query($GLOBALS["___mysqli_ston"],  $insert ) ) {
		sentinelMessagePush( "Data could not be inserted into 'logs' table<br />SQL: " . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) );
		sentinelPageReload();
	}
	sentinelMessagePush( "Data inserted into 'logs' table." ); */

	sentinelMessagePush( "<em>Setup successful</em>!" );

	if( !sentinelIsLoggedIn())
	sentinelMessagePush( "Please <a href='login.php'>login</a>.<script>setTimeout(function(){window.location.href='login.php'},5000);</script>" );
	sentinelPageReload();

} else {
    // The 'sentinel' database already exists, show the setup page
    sentinelMessagePush("The 'sentinel' has been created. Do you want to Reset it?");
}

if( !@($GLOBALS["___mysqli_ston"] = mysqli_connect( $_SENTINEL[ 'db_server' ],  $_SENTINEL[ 'db_user' ],  $_SENTINEL[ 'db_password' ], "", $_SENTINEL[ 'db_port' ] )) ) {
	sentinelMessagePush( "Could not connect to the database service.<br />Please check the config file.<br />Database Error #" . mysqli_connect_errno() . ": " . mysqli_connect_error() . "." );
	if ($_SENTINEL[ 'db_user' ] == "root") {
		sentinelMessagePush( 'Your database user is root, if you are using MariaDB, this will not work, please read the README.md file.' );
	}
	sentinelPageReload();
}

$page = sentinelPageNewGrab();
$page[ 'title' ]   = 'Setup' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'setup';

if( isset( $_POST[ 'create_db' ] ) ) {
	// Anti-CSRF
	if (array_key_exists ("session_token", $_SESSION)) {
		$session_token = $_SESSION[ 'session_token' ];
	} else {
		$session_token = "";
	}

	checkToken( $_REQUEST[ 'user_token' ], $session_token, 'setup.php' );

	if( $DBMS == 'MySQL' ) {
		include_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/DBMS/MySQL.php';
	}
	elseif($DBMS == 'PGSQL') {
		// include_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/DBMS/PGSQL.php';
		sentinelMessagePush( 'PostgreSQL is not yet fully supported.' );
		sentinelPageReload();
	}
	else {
		sentinelMessagePush( 'ERROR: Invalid database selected. Please review the config file syntax.' );
		sentinelPageReload();
	}
}

// Anti-CSRF
generateSessionToken();

$database_type_name = "Unknown - The site is probably now broken";
if( $DBMS == 'MySQL' ) {
	$database_type_name = "MySQL/MariaDB";
} elseif($DBMS == 'PGSQL') {
	$database_type_name = "PostgreSQL";
}

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Database Setup <img src=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "sentinel/images/spanner.png\" /></h1>

	<p>Click on the 'Create / Reset Database' button below to create or reset your database.<br />
	If you get an error make sure you have the correct user credentials in: <em>" . realpath(  getcwd() . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.inc.php" ) . "</em><br />
	If the database already exists, <em>it will be cleared and the data will be reset</em>.<br />
	You can also use this to reset the administrator credentials (\"<em>admin</em> // <em>password</em>\") at any stage.</p>
	<!-- Create db button -->
	<form action=\"#\" method=\"post\">
		<input name=\"create_db\" type=\"submit\" value=\"Create / Reset Database\">
		" . tokenField() . "
	</form>
	<!--<hr />-->
<!--
	<h2>Setup Check</h2>
	<br>
	{$SERVER_NAME}<br />
	<br />
	{$SENTINELOS}<br />
	<br />
	PHP version: <em>" . phpversion() . "</em><br />
	{$phpDisplayErrors}<br />
	{$phpSafeMode}<br/ >
-->
<!--{$phpURLInclude}<br/ >
	{$phpURLFopen}<br />
	{$phpMagicQuotes}<br />
	{$phpGD}<br />
	{$phpMySQL}<br />
	{$phpPDO}<br />
-->
<!--
	<br />
	Backend database: <em>{$database_type_name}</em><br />
	{$MYSQL_USER}<br />
	{$MYSQL_PASS}<br />
	{$MYSQL_DB}<br />
	{$MYSQL_SERVER}<br />
	{$MYSQL_PORT}<br />
	<br />
-->
<!--{$SENTINELRecaptcha}<br />
	{$SENTINELUploadsWrite}<br />
	<br />
	{$bakWritable}
	<br />
-->
<!--<i><span class=\"failure\">Status in red</span>, indicate there will be an issue when trying to complete some modules.</i><br />
	<br />
	If you see disabled on either <i>allow_url_fopen</i> or <i>allow_url_include</i>, set the following in your php.ini file and restart Apache.<br />
	<pre><code>allow_url_fopen = On
allow_url_include = On</code></pre>
	These are only required for the file inclusion labs so unless you want to play with those, you can ignore them. -->
<!--<br /><br /><br />-->
	<!--<hr />-->
</div>";

sentinelHtmlEcho( $page );

?>
