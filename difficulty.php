<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( 'authenticated') );

$page = sentinelPageNewGrab();
$page[ 'title' ]   = 'Difficulty Level' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'difficulty';

$securityHtml = '';
if( isset( $_POST['seclev_submit'] ) ) {
	// Anti-CSRF
	checkToken( $_REQUEST[ 'user_token' ], $_SESSION[ 'session_token' ], 'difficulty.php' );

	$securityLevel = '';
	switch( $_POST[ 'security' ] ) {
		case 'low':
			$securityLevel = 'low';
			break;
		case 'medium':
			$securityLevel = 'medium';
			break;
		case 'high':
			$securityLevel = 'high';
			break;
		default:
			$securityLevel = 'impossible';
			break;
	}

	sentinelSecurityLevelSet( $securityLevel );
	sentinelMessagePush( "Security setting is {$securityLevel}" );
	sentinelPageReload();
}

$securityOptionsHtml = '';
$securityLevelHtml   = '';
foreach( array( 'low', 'medium', 'high', 'impossible' ) as $securityLevel ) {
	$selected = '';
	if( $securityLevel == sentinelSecurityLevelGet() ) {
		$selected = ' selected="selected"';
		$securityLevelHtml = "<p>Security setting is currently: <em style=\"background: black; color: white;\">$securityLevel</em>.<p>";
	}
	$securityOptionsHtml .= "<option value=\"{$securityLevel}\"{$selected}>" . ucfirst($securityLevel) . "</option>";
}

// Anti-CSRF
generateSessionToken();

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Difficulty <img src=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "sentinel/images/lock.png\" /></h1>
	<br />

	<h2>Security Setting</h2>

	{$securityHtml}

	<form action=\"#\" method=\"POST\">
		{$securityLevelHtml}
		<p>You can set the security setting to low, medium, high or impossible. The security setting changes the vulnerability level of Cyber Sentinel:</p>
		<ol>
			<li> Low - It's completely vulnerable and <em>has no security measures at all</em>. It's explains how vulnerabilities manifest through bad coding practices, and learn basic exploitation techniques.</li>
			<li> Medium - It gives an example of <em>bad security practices</em>, where the developer has tried but failed to secure an application. It also challenges users to improve their techniques.</li>
			<li> High - It's an extension to the medium difficulty, attempting to secure the code with <em>\"harder to exploit\" bad practices</em>. This level doesn't allow exploitation using easier techniques, like in Capture The Flag (CTF) competitions.</li>
			<li> Impossible - This setting should be <em>secure against all vulnerabilities</em>. It is used to compare the vulnerable source code to the secure source code.<br /></li>
		</ol>
		<select name=\"security\">
			{$securityOptionsHtml}
		</select>
		<input type=\"submit\" value=\"Submit\" name=\"seclev_submit\">
		" . tokenField() . "
	</form>
</div>";

sentinelHtmlEcho( $page );

?>
