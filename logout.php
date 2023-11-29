<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( ) );

if( !sentinelIsLoggedIn() ) {	// The user shouldn't even be on this page
	sentinelMessagePush( "Please login or signup" );
	sentinelRedirect( 'login.php' );
}

sentinelLogout();
sentinelMessagePush( "You have been logged out" );
sentinelRedirect( 'login.php' );

?>
