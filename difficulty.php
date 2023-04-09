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
		$securityLevelHtml = "<p>Security setting is currently: <em>$securityLevel</em>.<p>";
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
			<li> Low - This security setting is completely vulnerable and <em>has no security measures at all</em>. It's use is to be as an example of how web application vulnerabilities manifest through bad coding practices and to serve as a platform to teach or learn basic exploitation techniques.</li>
			<li> Medium - This setting is mainly to give an example to the user of <em>bad security practices</em>, where the developer has tried but failed to secure an application. It also acts as a challenge to users to refine their exploitation techniques.</li>
			<li> High - This option is an extension to the medium difficulty, with a mixture of <em>harder or alternative bad practices</em> to attempt to secure the code. The vulnerability may not allow the same extent of the exploitation, similar in various Capture The Flags (CTFs) competitions.</li>
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
