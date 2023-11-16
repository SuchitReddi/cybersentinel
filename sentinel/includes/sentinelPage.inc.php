<?php

if( !defined( 'SENTINEL_WEB_PAGE_TO_ROOT' ) ) {
	die( 'Cyber Sentinel System error- WEB_PAGE_TO_ROOT undefined' );
	exit;
}

if (!file_exists(SENTINEL_WEB_PAGE_TO_ROOT . 'config/config.inc.php')) {
	die ("Cyber Sentinel System error - config file not found. Copy config/config.inc.php.dist to config/config.inc.php and configure to your environment.");
}

// Include configs
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'config/config.inc.php';

// Declare the $html variable
if( !isset( $html ) ) {
	$html = "";
}

// Valid security levels
$security_levels = array('low', 'medium', 'high', 'impossible');
if( !isset( $_COOKIE[ 'security' ] ) || !in_array( $_COOKIE[ 'security' ], $security_levels ) ) {
	// Set security cookie to impossible if no cookie exists
	if( in_array( $_SENTINEL[ 'default_security_level' ], $security_levels) ) {
		sentinelSecurityLevelSet( $_SENTINEL[ 'default_security_level' ] );
	} else {
		sentinelSecurityLevelSet( 'impossible' );
	}
}

// This will setup the session cookie based on
// the security level.

if (sentinelSecurityLevelGet() == 'impossible') {
	$httponly = true;
	$samesite = true;
}
else {
	$httponly = false;
	$samesite = false;
}

$maxlifetime = 86400;
$secure = false;

session_set_cookie_params([
	'lifetime' => $maxlifetime,
	'path' => '/',
	'domain' => $_SERVER['HTTP_HOST'],
	'secure' => $secure,
	'httponly' => $httponly,
	'samesite' => $samesite
]);
session_start();

if (!array_key_exists ("default_locale", $_SENTINEL)) {
	$_SENTINEL[ 'default_locale' ] = "en";
}

sentinelLocaleSet( $_SENTINEL[ 'default_locale' ] );

// sentinel version
function sentinelVersionGet() {
	return '1.10 *Development*';
}

// sentinel release date
function sentinelReleaseDateGet() {
	return '2015-10-08';
}


// Start session functions --

function &sentinelSessionGrab() {
	if( !isset( $_SESSION[ 'sentinel' ] ) ) {
		$_SESSION[ 'sentinel' ] = array();
	}
	return $_SESSION[ 'sentinel' ];
}


function sentinelPageStartup( $pActions ) {
	if (in_array('authenticated', $pActions)) {
		if( !sentinelIsLoggedIn()) {
			sentinelRedirect( SENTINEL_WEB_PAGE_TO_ROOT . 'login.php' );
		}
	}
}

function sentinelLogin( $pUsername ) {
	$sentinelSession =& sentinelSessionGrab();
	$sentinelSession[ 'username' ] = $pUsername;
}


function sentinelIsLoggedIn() {
	global $_SENTINEL;

	if (in_array("disable_authentication", $_SENTINEL) && $_SENTINEL['disable_authentication']) {
		return true;
	}
	$sentinelSession =& sentinelSessionGrab();
	return isset( $sentinelSession[ 'username' ] );
}


function sentinelLogout() {
	$sentinelSession =& sentinelSessionGrab();
	unset( $sentinelSession[ 'username' ] );
}


function sentinelPageReload() {
	sentinelRedirect( $_SERVER[ 'PHP_SELF' ] );
}

function sentinelCurrentUser() {
	$sentinelSession =& sentinelSessionGrab();
	return ( isset( $sentinelSession[ 'username' ]) ? $sentinelSession[ 'username' ] : 'Unknown') ;
}


// Function to check if the logged in user is still in the database. If not, logs out.
$check_user = sentinelCurrentUser();
function sentinelLoggedUserCheck($check_user) {
	global $_SENTINEL;

    // Connect to the database (adjust this part based on your setup)
    sentinelDatabaseConnect();

    // Sanitize the username to prevent SQL injection
    $check_user = mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $check_user);

    // Perform the query
    $query = "SELECT COUNT(*) as count FROM users WHERE user = '$check_user'";
	mysqli_select_db($GLOBALS["___mysqli_ston"], "sentinel");
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($GLOBALS["___mysqli_ston"]));
    }

    // Fetch the result
    $row = mysqli_fetch_assoc($result);
    $userCount = $row['count'];

    // Return true if the user exists, false otherwise
    return $userCount > 0;
}

if (sentinelIsLoggedIn() AND !sentinelLoggedUserCheck($check_user)) {
	echo ("Tough Luck!! Your account was removed from the database.");
    sentinelLogout();
}

// -- END (Session functions)

function &sentinelPageNewGrab() {
	$returnArray = array(
		'title'           => 'Cyber Sentinel',
		'title_separator' => ' - ',
		'body'            => '',
		'page_id'         => '',
		'help_button'     => '',
		'source_button'   => '',
	);
	return $returnArray;
}


function sentinelSecurityLevelGet() {
	global $_SENTINEL;

	// If there is a security cookie, that takes priority.
	if (isset($_COOKIE['security'])) {
		return $_COOKIE[ 'security' ];
	}

	// If not, check to see if authentication is disabled, if it is, use
	// the default security level.
	if (in_array("disable_authentication", $_SENTINEL) && $_SENTINEL['disable_authentication']) {
		return $_SENTINEL[ 'default_security_level' ];
	}

	// Worse case, set the level to impossible.
	return 'impossible';
}


function sentinelSecurityLevelSet( $pSecurityLevel ) {
	if( $pSecurityLevel == 'impossible' ) {
		$httponly = true;
	}
	else {
		$httponly = false;
	}

	setcookie( 'security', $pSecurityLevel, 0, "/", "", false, $httponly );
}

function sentinelLocaleGet() {	
	$sentinelSession =& sentinelSessionGrab();
	return $sentinelSession[ 'locale' ];
}

function sentinelSQLiDBGet() {
	global $_SENTINEL;
	return $_SENTINEL['SQLI_DB'];
}

function sentinelLocaleSet( $pLocale ) {
	$sentinelSession =& sentinelSessionGrab();
	$locales = array('en', 'zh');
	if( in_array( $pLocale, $locales) ) {
		$sentinelSession[ 'locale' ] = $pLocale;
	} else {
		$sentinelSession[ 'locale' ] = 'en';
	}
}

// Start message functions --

function sentinelMessagePush( $pMessage ) {
	$sentinelSession =& sentinelSessionGrab();
	if( !isset( $sentinelSession[ 'messages' ] ) ) {
		$sentinelSession[ 'messages' ] = array();
	}
	$sentinelSession[ 'messages' ][] = $pMessage;
}


function sentinelMessagePop() {
	$sentinelSession =& sentinelSessionGrab();
	if( !isset( $sentinelSession[ 'messages' ] ) || count( $sentinelSession[ 'messages' ] ) == 0 ) {
		return false;
	}
	return array_shift( $sentinelSession[ 'messages' ] );
}


function messagesPopAllToHtml() {
	$messagesHtml = '';
	while( $message = sentinelMessagePop() ) {   // TODO- sharpen!
		$messagesHtml .= "<div class=\"message\">{$message}</div>";
	}

	return $messagesHtml;
}

// --END (message functions)

function sentinelHtmlEcho( $pPage ) {

	// Get security cookie --
	$securityLevelHtml = '';
	switch( sentinelSecurityLevelGet() ) {
		case 'low':
			$securityLevelHtml = 'low';
			break;
		case 'medium':
			$securityLevelHtml = 'medium';
			break;
		case 'high':
			$securityLevelHtml = 'high';
			break;
		default:
			$securityLevelHtml = 'impossible';
			break;
	}
	// -- END (security cookie)

	$userInfoHtml = 'Username: ' . ( sentinelCurrentUser() );
	$securityLevelHtml = "Security Level: {$securityLevelHtml}";
	$localeHtml = 'Locale: ' . ( sentinelLocaleGet() );
	$sqliDbHtml = 'SQLi DB: ' . ( sentinelSQLiDBGet() );
	

	$messagesHtml = messagesPopAllToHtml();
	if( $messagesHtml ) {
		$messagesHtml = "<div class=\"body_padded\">{$messagesHtml}</div>";
	}

	$systemInfoHtml = "";
	if( sentinelIsLoggedIn() ) 
		$systemInfoHtml = "<div align=\"left\">{$userInfoHtml} | {$securityLevelHtml}<br>{$localeHtml} | {$sqliDbHtml}</div>";
	if( $pPage[ 'source_button' ] ) {
		$systemInfoHtml = sentinelButtonSourceHtmlGet( $pPage[ 'source_button' ] ) . " $systemInfoHtml";
	}
	if( $pPage[ 'help_button' ] ) {
		$systemInfoHtml = sentinelButtonHelpHtmlGet( $pPage[ 'help_button' ] ) . " $systemInfoHtml";
	}

// Logging every page visit. It can be viewed from log.php	
sentinelDatabaseConnect();

// Function to get the user's IP address
function getUserIP() {
    $ip = '';

    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

// Function to insert a log entry
function insertLogEntry($user, $ip, $visited, $time) {
    $ip = getUserIP();
	$user=sentinelCurrentUser();
    $query = "INSERT INTO logs (user, ip, visited, time) VALUES ('$user', '$ip', '$visited', '$time')";

	global $_SENTINEL;
	global $DBMS;
	global $DBMS_errorFunc;
	global $db;
	global $sqlite_db_connection;
		
	// Check if the 'sentinel' database exists. If the database doesn't exist, we create it automatically. If it does, user can reset it.
	$databaseExistsQuery = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$_SENTINEL['db_database']}'";
	$databaseExistsResult = mysqli_query($GLOBALS["___mysqli_ston"], $databaseExistsQuery);

	// If the database doesn't exist, redirect to setup.php
	if (!$databaseExistsResult || mysqli_num_rows($databaseExistsResult) === 0) {
		sentinelLogout();
		sentinelMessagePush('The database does not exist. Redirecting to setup.');
		$_SESSION['redirected_to_setup'] = true;
		sentinelRedirect(SENTINEL_WEB_PAGE_TO_ROOT . 'setup.php');
	}
		mysqli_select_db($GLOBALS["___mysqli_ston"],  "sentinel" );
		$result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

		if (!$result) {
			die("Failed to insert log entry: " . mysqli_error($GLOBALS["___mysqli_ston"]));
		}
	}

// Capture logs for every page visit
$ip = getUserIP();
$user=sentinelCurrentUser();
$visited = $_SERVER['REQUEST_URI'];
$time = date("Y-m-d H:i:s");

insertLogEntry($user, $ip, $visited, $time);

// Send Headers + main HTML code
Header( 'Cache-Control: no-cache, must-revalidate');   // HTTP/1.1
Header( 'Content-Type: text/html;charset=utf-8' );     // TODO- proper XHTML headers...
Header( 'Expires: Tue, 23 Jun 2009 12:00:00 GMT' );    // Date in the past

echo "<!DOCTYPE html>

<html lang=\"en-GB\">

	<head>

<!-- Google tag (gtag.js) -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=G-TF3WKZS5Q4\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-TF3WKZS5Q4');
</script>

		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />

		<title>{$pPage[ 'title' ]}</title>

		<link rel=\"stylesheet\" type=\"text/css\" href=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "sentinel/css/main.css\" />

		<link rel=\"icon\" type=\"\image/ico\" href=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "favicon.ico\" />

		<script type=\"text/javascript\" src=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "sentinel/js/sentinelPage.js\"></script>

	</head>

	<body class=\"home\">
		<div id=\"container\">

			<center><img src=\"/cybersentinel/sentinel/images/logo.png\" alt=\"Cyber Sentinel\" style=\"width: 100px; background: black;\"/></center>
			
			<div id=\"main_total\">

				<div id=\"main_menu_padded\">
				<ul class=\"menuBlocks\">
				  <li class=\"selected\"><a href=\"/cybersentinel\">Home</a></li>
				  <li class=\"\"><a href=\"/cybersentinel/instructions.php\">Instructions</a></li>
				  <li class=\"\"><a href=\"/cybersentinel/tutorials.php\">Tutorials</a></li>
				  <li class=\"\"><a href=\"/cybersentinel/setup.php\">Setup / Reset DB</a></li>
				  <li class=\"vulnerabilities-menu\">
					<a href=\"#\">Vulnerabilities  <span class=\"arrow\">&#9660;</span></a>
					<ul class=\"vulnerabilities-list\">
					  <li><a href=\"/cybersentinel/vulnerabilities/exec/\">Command Injection</a></li><hr />
					  <li><a href=\"/cybersentinel/vulnerabilities/authbypass/\">Authentication Bypass</a></li><hr />
					  <li><a href=\"/cybersentinel/vulnerabilities/xss_r/\">XSS (Reflected)</a></li><hr />
					  <li><a href=\"/cybersentinel/vulnerabilities/xss_s/\">XSS (Stored)</a></li><hr />
					  <li><a href=\"/cybersentinel/vulnerabilities/xss_d/\">XSS (DOM)</a></li><hr />
					  <li><a href=\"/cybersentinel/vulnerabilities/sqli/\">SQL Injection</a></li><hr />
					  <li><a href=\"/cybersentinel/vulnerabilities/sqli_blind/\">SQL Injection (Blind)</a></li><hr />
					  <li><a href=\"/cybersentinel/vulnerabilities/csp/\">CSP Bypass</a></li><hr />
					  <li><a href=\"/cybersentinel/vulnerabilities/javascript/\">JavaScript</a></li><hr />
					  <li><a href=\"/cybersentinel/vulnerabilities/open_redirect/\">Open HTTP Redirect</a></li>
					</ul>
				  </li>
				  <li class=\"\"><a href=\"/cybersentinel/difficulty.php\">Difficulty</a></li>
				  <li class=\"\"><a href=\"/cybersentinel/phpinfo.php\">PHP Info</a></li>
				  <li class=\"\"><a href=\"/cybersentinel/logs.php\">Logs</a></li>
				  <li class=\"\"><a href=\"/cybersentinel/about.php\">About</a></li>
				  <li class=\"\"><a href=\"/cybersentinel/logout.php\">Logout</a></li>
				</ul>
				</div>

				<div id=\"main_body\">

					{$pPage[ 'body' ]}
					<br /><br />
					{$messagesHtml}

				</div>

			</div>

			<div class=\"clear\">
			</div>

			<div id=\"footer\">

				<p><a style=\"font-size: 13px; background: black;\" href=\"https://github.com/SuchitReddi/cybersentinel\" target=\"_blank\" rel=\"nofollow noreferrer noopener\">Cyber Sentinel</a></p>
				<script src='" . SENTINEL_WEB_PAGE_TO_ROOT . "/sentinel/js/add_event_listeners.js'></script>

			</div>
			<div id=\"header\">
				<div id=\"system_info\">
					{$systemInfoHtml}
				</div>
			</div>
		</div>

	</body>

</html>";
}


function sentinelHelpHtmlEcho( $pPage ) {
	// Send Headers
	Header( 'Cache-Control: no-cache, must-revalidate');   // HTTP/1.1
	Header( 'Content-Type: text/html;charset=utf-8' );     // TODO- proper XHTML headers...
	Header( 'Expires: Tue, 23 Jun 2009 12:00:00 GMT' );    // Date in the past

	echo "<!DOCTYPE html>

<html lang=\"en-GB\">

	<head>

<!-- Google tag (gtag.js) -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=G-TF3WKZS5Q4\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-TF3WKZS5Q4');
</script>


		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />

		<title>{$pPage[ 'title' ]}</title>

		<link rel=\"stylesheet\" type=\"text/css\" href=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "sentinel/css/help.css\" />

		<link rel=\"icon\" type=\"\image/ico\" href=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "favicon.ico\" />

	</head>

	<body>

	<div id=\"container\">

			{$pPage[ 'body' ]}

		</div>

	</body>

</html>";
}


function sentinelSourceHtmlEcho( $pPage ) {
	// Send Headers
	Header( 'Cache-Control: no-cache, must-revalidate');   // HTTP/1.1
	Header( 'Content-Type: text/html;charset=utf-8' );     // TODO- proper XHTML headers...
	Header( 'Expires: Tue, 23 Jun 2009 12:00:00 GMT' );    // Date in the past

	echo "<!DOCTYPE html>

<html lang=\"en-GB\">

	<head>

<!-- Google tag (gtag.js) -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id=G-TF3WKZS5Q4\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-TF3WKZS5Q4');
</script>


		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />

		<title>{$pPage[ 'title' ]}</title>

		<link rel=\"stylesheet\" type=\"text/css\" href=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "sentinel/css/source.css\" />

		<link rel=\"icon\" type=\"\image/ico\" href=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "favicon.ico\" />

	</head>

	<body>

		<div id=\"container\">

			{$pPage[ 'body' ]}

		</div>

	</body>

</html>";
}

// To be used on all external links --
function sentinelExternalLinkUrlGet( $pLink,$text=null ) {
	if(is_null( $text )) {
		return '<a href="' . $pLink . '" target="_blank" rel="noopener noreferrer nofollow">' . $pLink . '</a>';
	}
	else {
		return '<a href="' . $pLink . '" target="_blank" rel="noopener noreferrer nofollow">' . $text . '</a>';
	}
}
// -- END ( external links)

function sentinelButtonHelpHtmlGet( $pId ) {
	$security = sentinelSecurityLevelGet();
	$locale = sentinelLocaleGet();
	return "<input type=\"button\" value=\"View Help\" class=\"popup_button\" id='help_button' data-help-url='" . SENTINEL_WEB_PAGE_TO_ROOT . "vulnerabilities/view_help.php?id={$pId}&security={$security}&locale={$locale}' )\">";
}


function sentinelButtonSourceHtmlGet( $pId ) {
	$security = sentinelSecurityLevelGet();
	return "<input type=\"button\" value=\"View Source\" class=\"popup_button\" id='source_button' data-source-url='" . SENTINEL_WEB_PAGE_TO_ROOT . "vulnerabilities/view_source.php?id={$pId}&security={$security}' )\">";
}


// Database Management --

if( $DBMS == 'MySQL' ) {
	$DBMS = htmlspecialchars(strip_tags( $DBMS ));
	$DBMS_errorFunc = 'mysqli_error()';
}
elseif( $DBMS == 'PGSQL' ) {
	$DBMS = htmlspecialchars(strip_tags( $DBMS ));
	$DBMS_errorFunc = 'pg_last_error()';
}
else {
	$DBMS = "No DBMS selected.";
	$DBMS_errorFunc = '';
}

//$DBMS_connError = '
//	<div align="center">
//		<img src="' . SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/images/logo.png" />
//		<pre>Unable to connect to the database.<br />' . $DBMS_errorFunc . '<br /><br /></pre>
//		Click <a href="' . SENTINEL_WEB_PAGE_TO_ROOT . 'setup.php">here</a> to setup the database.
//	</div>';

function sentinelDatabaseConnect() {
	global $_SENTINEL;
	global $DBMS;
	global $DBMS_errorFunc;
	global $db;
	global $sqlite_db_connection;

	/*
	if( $DBMS == 'MySQL' ) {
		if( !@($GLOBALS["___mysqli_ston"] = mysqli_connect( $_SENTINEL[ 'db_server' ],  $_SENTINEL[ 'db_user' ],  $_SENTINEL[ 'db_password' ], "", $_SENTINEL[ 'db_port' ] ))
		|| !@((bool)mysqli_query($GLOBALS["___mysqli_ston"], "USE " . $_SENTINEL[ 'db_database' ])) ) {
			//die( $DBMS_connError );
			sentinelLogout();
			sentinelMessagePush( 'Unable to connect to the database.<br />' . $DBMS_errorFunc );
			sentinelRedirect( SENTINEL_WEB_PAGE_TO_ROOT . 'setup.php' );
		}
		// MySQL PDO Prepared Statements (for impossible levels)
		$db = new PDO('mysql:host=' . $_SENTINEL[ 'db_server' ].';dbname=' . $_SENTINEL[ 'db_database' ].';port=' . $_SENTINEL['db_port'] . ';charset=utf8', $_SENTINEL[ 'db_user' ], $_SENTINEL[ 'db_password' ]);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	}
	*/
	
	if ($DBMS == 'MySQL') {
		// Attempt to connect to MySQL server without selecting a database
		if (!@($GLOBALS["___mysqli_ston"] = mysqli_connect($_SENTINEL['db_server'], $_SENTINEL['db_user'], $_SENTINEL['db_password'], "", $_SENTINEL['db_port']))) {
			sentinelLogout();
			sentinelMessagePush('Unable to connect to the database.<br />' . $DBMS_errorFunc);
			sentinelRedirect(SENTINEL_WEB_PAGE_TO_ROOT . 'setup.php');
		}
	
		// Check if the database exists
		$databaseExists = false;
		$result = mysqli_query($GLOBALS["___mysqli_ston"], "SHOW DATABASES");
		while ($row = mysqli_fetch_assoc($result)) {
			if ($row['Database'] == $_SENTINEL['db_database']) {
				$databaseExists = true;
				break;
			}
		}
	
		// Close the connection
		//mysqli_close($GLOBALS["___mysqli_ston"]);
	
		// If the database doesn't exist, redirect to setup.php
		if (!$databaseExists && !isset($_SESSION['redirected_to_setup'])) {
			sentinelLogout();
			sentinelMessagePush('The database does not exist. Redirecting to setup.');
			$_SESSION['redirected_to_setup'] = true;
			sentinelRedirect(SENTINEL_WEB_PAGE_TO_ROOT . 'setup.php');
			// Continue with the original code to establish the PDO connection
			$db = new PDO('mysql:host=' . $_SENTINEL['db_server'] . ';dbname=' . $_SENTINEL['db_database'] . ';port=' . $_SENTINEL['db_port'] . ';charset=utf8', $_SENTINEL['db_user'], $_SENTINEL['db_password']);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}
	

	}
	
	elseif( $DBMS == 'PGSQL' ) {
		//$dbconn = pg_connect("host={$_SENTINEL[ 'db_server' ]} dbname={$_SENTINEL[ 'db_database' ]} user={$_SENTINEL[ 'db_user' ]} password={$_SENTINEL[ 'db_password' ])}"
		//or die( $DBMS_connError );
		sentinelMessagePush( 'PostgreSQL is not currently supported.' );
		sentinelPageReload();
	}
	else {
		die ( "Unknown {$DBMS} selected." );
	}

	if ($_SENTINEL['SQLI_DB'] == SQLITE) {
		$location = SENTINEL_WEB_PAGE_TO_ROOT . "database/" . $_SENTINEL['SQLITE_DB'];
		$sqlite_db_connection = new SQLite3($location);
		$sqlite_db_connection->enableExceptions(true);
	#	print "sqlite db setup";
	}
}

// -- END (Database Management)


function sentinelRedirect( $pLocation ) {
	session_commit();
	header( "Location: {$pLocation}" );
	exit;
}

// XSS Stored guestbook function --
function sentinelGuestbook() {
	$query  = "SELECT name, comment FROM guestbook";
	mysqli_select_db($GLOBALS["___mysqli_ston"],  "sentinel" );
	$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query );

	$guestbook = '';

	while( $row = mysqli_fetch_row( $result ) ) {
		if( sentinelSecurityLevelGet() == 'impossible' ) {
			$name    = htmlspecialchars( $row[0] );
			$comment = htmlspecialchars( $row[1] );
		}
		else {
			$name    = $row[0];
			$comment = $row[1];
		}

		$guestbook .= "<div id=\"guestbook_comments\">Name: {$name}<br />" . "Message: {$comment}<br /></div>\n";
	}
	return $guestbook;
}
// -- END (XSS Stored guestbook)


// Token functions --
function checkToken( $user_token, $session_token, $returnURL ) {  # Validate the given (CSRF) token
	global $_SENTINEL;

	if (in_array("disable_authentication", $_SENTINEL) && $_SENTINEL['disable_authentication']) {
		return true;
	}

	if( $user_token !== $session_token || !isset( $session_token ) ) {
		sentinelMessagePush( 'CSRF token is incorrect' );
		sentinelRedirect( $returnURL );
	}
}

function generateSessionToken() {  # Generate a brand new (CSRF) token
	if( isset( $_SESSION[ 'session_token' ] ) ) {
		destroySessionToken();
	}
	$_SESSION[ 'session_token' ] = md5( uniqid() );
}

function destroySessionToken() {  # Destroy any session with the name 'session_token'
	unset( $_SESSION[ 'session_token' ] );
}

function tokenField() {  # Return a field for the (CSRF) token
	return "<input type='hidden' name='user_token' value='{$_SESSION[ 'session_token' ]}' />";
}
// -- END (Token functions)


// Setup Functions --
$PHPUploadPath    = realpath( getcwd() . DIRECTORY_SEPARATOR . SENTINEL_WEB_PAGE_TO_ROOT . "docs" . DIRECTORY_SEPARATOR . "uploads" ) . DIRECTORY_SEPARATOR;
$PHPCONFIGPath       = realpath( getcwd() . DIRECTORY_SEPARATOR . SENTINEL_WEB_PAGE_TO_ROOT . "config");


$phpDisplayErrors = 'PHP function display_errors: <em>' . ( ini_get( 'display_errors' ) ? 'Enabled</em> <i>(Easy Mode!)</i>' : 'Disabled</em>' );                                                  // Verbose error messages (e.g. full path disclosure)
$phpSafeMode      = 'PHP function safe_mode: <span class="' . ( ini_get( 'safe_mode' ) ? 'failure">Enabled' : 'success">Disabled' ) . '</span>';                                                   // DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0
$phpMagicQuotes   = 'PHP function magic_quotes_gpc: <span class="' . ( ini_get( 'magic_quotes_gpc' ) ? 'failure">Enabled' : 'success">Disabled' ) . '</span>';                                     // DEPRECATED as of PHP 5.3.0 and REMOVED as of PHP 5.4.0
$phpURLInclude    = 'PHP function allow_url_include: <span class="' . ( ini_get( 'allow_url_include' ) ? 'success">Enabled' : 'failure">Disabled' ) . '</span>';                                   // RFI
$phpURLFopen      = 'PHP function allow_url_fopen: <span class="' . ( ini_get( 'allow_url_fopen' ) ? 'success">Enabled' : 'failure">Disabled' ) . '</span>';                                       // RFI
$phpGD            = 'PHP module gd: <span class="' . ( ( extension_loaded( 'gd' ) && function_exists( 'gd_info' ) ) ? 'success">Installed' : 'failure">Missing - Only an issue if you want to play with captchas' ) . '</span>';                    // File Upload
$phpMySQL         = 'PHP module mysql: <span class="' . ( ( extension_loaded( 'mysqli' ) && function_exists( 'mysqli_query' ) ) ? 'success">Installed' : 'failure">Missing' ) . '</span>';                // Core Cyber Sentinel
$phpPDO           = 'PHP module pdo_mysql: <span class="' . ( extension_loaded( 'pdo_mysql' ) ? 'success">Installed' : 'failure">Missing' ) . '</span>';                // SQLi
$SENTINELRecaptcha    = 'reCAPTCHA key: <span class="' . ( ( isset( $_SENTINEL[ 'recaptcha_public_key' ] ) && $_SENTINEL[ 'recaptcha_public_key' ] != '' ) ? 'success">' . $_SENTINEL[ 'recaptcha_public_key' ] : 'failure">Missing' ) . '</span>';

$SENTINELUploadsWrite = '[User: ' . get_current_user() . '] Writable folder ' . $PHPUploadPath . ': <span class="' . ( is_writable( $PHPUploadPath ) ? 'success">Yes' : 'failure">No' ) . '</span>';                                     // File Upload
$bakWritable = '[User: ' . get_current_user() . '] Writable folder ' . $PHPCONFIGPath . ': <span class="' . ( is_writable( $PHPCONFIGPath ) ? 'success">Yes' : 'failure">No' ) . '</span>';   // config.php.bak check                                  // File Upload

$SENTINELOS           = 'Operating system: <em>' . ( strtoupper( substr (PHP_OS, 0, 3)) === 'WIN' ? 'Windows' : '*nix' ) . '</em>';
$SERVER_NAME      = 'Web Server SERVER_NAME: <em>' . $_SERVER[ 'SERVER_NAME' ] . '</em>';                                                                                                          // CSRF

$MYSQL_USER       = 'Database username: <em>' . $_SENTINEL[ 'db_user' ] . '</em>';
$MYSQL_PASS       = 'Database password: <em>' . ( ($_SENTINEL[ 'db_password' ] != "" ) ? '******' : '*blank*' ) . '</em>';
$MYSQL_DB         = 'Database database: <em>' . $_SENTINEL[ 'db_database' ] . '</em>';
$MYSQL_SERVER     = 'Database host: <em>' . $_SENTINEL[ 'db_server' ] . '</em>';
$MYSQL_PORT       = 'Database port: <em>' . $_SENTINEL[ 'db_port' ] . '</em>';
// -- END (Setup Functions)

?>
