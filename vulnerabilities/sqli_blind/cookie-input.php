<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '../../' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( 'authenticated' ) );

$page = sentinelPageNewGrab();
$page[ 'title' ] = 'Blind SQL Injection Cookie Input' . $page[ 'title_separator' ].$page[ 'title' ];

if( isset( $_POST[ 'id' ] ) ) {
	setcookie( 'id', $_POST[ 'id' ]);
	$page[ 'body' ] .= "Cookie ID set!<br /><br /><br />";
	$page[ 'body' ] .= "<script>window.opener.location.reload(true);</script>";
}

$page[ 'body' ] .= "
<form action=\"#\" method=\"POST\">
	<input type=\"text\" size=\"15\" name=\"id\">
	<input type=\"submit\" name=\"Submit\" value=\"Submit\">
</form>
<hr />
<br />

<button onclick=\"self.close();\">Close</button>";

sentinelSourceHtmlEcho( $page );

?>


