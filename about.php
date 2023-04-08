<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( ) );

$page = sentinelPageNewGrab();
$page[ 'title' ]   = 'About' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'about';

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h2>About</h2>
	<p>Cyber Sentinel is a PHP/MySQL intentionally vulnerable application.<br>
	Its main goals are to be an aid for security professionals to test their skills and tools in a legal environment, help web developers better understand the processes of securing web applications and aid teachers/students to teach/learn web application security in a class room environment</p>
	<p>It is an ongoing project</p>

	<h2>Links</h2>
	<ul>
		<li>Project Home: " . sentinelExternalLinkUrlGet( 'https://github.com/SuchitReddi/CyberSentinel' ) . "</li>
	</ul>

	<h2>Credits</h2>
	<ul>
		<li>Suchit Reddi: " . sentinelExternalLinkUrlGet( 'https://suchitreddi.github.io/','suchitreddi.github.io' ) . "</li>
	</ul>

	<h2>License</h2>
	<p>Cyber Sentinel is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.</p>

	<h2>Development</h2>
	<p>Everyone is welcome to contribute and help make Cyber Sentinel as successful as it can be.</p>
</div>\n";

sentinelHtmlEcho( $page );

exit;

?>
