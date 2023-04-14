<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( ) );

$page = sentinelPageNewGrab();
$page[ 'title' ]   = 'Tutorials' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'tutorials';

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Tutorials</h1>
	<br />

	<p>Here are some tutorials to help you understand these attacks better!</p>
		<ul>
			<li><a href=\"./vulnerabilities/exec/code/cmd_inj.html\" target=\"_blank\" rel\"noopener nofollow noreferrer\">Command Injection</a></li>
			<li><a href=\"./vulnerabilities/xss_r/code/xss-ref.html\" target=\"_blank\" rel\"noopener nofollow noreferrer\">Cross-Site Scripting: Reflected</a></li>
			<li><a href=\"./vulnerabilities/xss_r/code/xss-sto.html\" target=\"_blank\" rel\"noopener nofollow noreferrer\">Cross-Site Scripting: Stored</a></li>
			<li><a href=\"./vulnerabilities/xss_d/code/xss-dom.html\" target=\"_blank\" rel\"noopener nofollow noreferrer\">Cross-Site Scripting: DOM</a></li>
			<li><a href=\"./vulnerabilities/\" target=\"_blank\" rel\"noopener nofollow noreferrer\"></a></li>
		</ul>
</div>";

sentinelHtmlEcho( $page );
