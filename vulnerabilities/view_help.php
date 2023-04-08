<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '../' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( 'authenticated' ) );

$page = sentinelPageNewGrab();
$page[ 'title' ] = 'Help' . $page[ 'title_separator' ].$page[ 'title' ];

if (array_key_exists ("id", $_GET) &&
	array_key_exists ("security", $_GET) &&
	array_key_exists ("locale", $_GET)) {
	$id       = $_GET[ 'id' ];
	$security = $_GET[ 'security' ];
	$locale = $_GET[ 'locale' ];

	ob_start();
	if ($locale == 'en') {
		eval( '?>' . file_get_contents( SENTINEL_WEB_PAGE_TO_ROOT . "vulnerabilities/{$id}/help/help.php" ) . '<?php ' );
	} else {
		eval( '?>' . file_get_contents( SENTINEL_WEB_PAGE_TO_ROOT . "vulnerabilities/{$id}/help/help.{$locale}.php" ) . '<?php ' );
	}
	$help = ob_get_contents();
	ob_end_clean();
} else {
	$help = "<p>Not Found</p>";
}

$page[ 'body' ] .= "
<div class=\"body_padded\">
	{$help}
</div>\n";

sentinelHelpHtmlEcho( $page );

?>
