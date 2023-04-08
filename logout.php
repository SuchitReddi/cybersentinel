<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( ) );

if( !sentinelIsLoggedIn() ) {	// The user shouldn't even be on this page
	// sentinelMessagePush( "You were not logged in" );
	sentinelRedirect( 'login.php' );
}

sentinelLogout();
sentinelMessagePush( "You have logged out" );
sentinelRedirect( 'login.php' );

?>
