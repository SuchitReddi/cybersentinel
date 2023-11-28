<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '../../' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( 'authenticated' ) );

$page = sentinelPageNewGrab();
$page[ 'title' ]   = 'Vulnerability: Command Injection' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'exec';
$page[ 'help_button' ]   = 'exec';
$page[ 'source_button' ] = 'exec';

sentinelDatabaseConnect();

$vulnerabilityFile = '';
switch( sentinelSecurityLevelGet() ) {
	case 'low':
		$vulnerabilityFile = 'low.php';
		break;
	case 'medium':
		$vulnerabilityFile = 'medium.php';
		break;
	case 'high':
		$vulnerabilityFile = 'high.php';
		break;
	default:
		$vulnerabilityFile = 'impossible.php';
		break;
}

require_once SENTINEL_WEB_PAGE_TO_ROOT . "vulnerabilities/exec/source/{$vulnerabilityFile}";

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Vulnerability: Command Injection</h1>

	<center>
	<button>
    <a href=\"./tut/cmd_inj.html\">Tutorial</a>
	</button>
	</center>

	<div class=\"vulnerable_code_area\">
		<h2>Ping a device</h2>

		<form name=\"ping\" action=\"#\" method=\"post\">
			<p>
				Enter an IP address:
				<input type=\"text\" name=\"ip\" size=\"30\">
				<input type=\"submit\" name=\"Submit\" value=\"Submit\">
			</p>\n";

if( $vulnerabilityFile == 'impossible.php' )
	$page[ 'body' ] .= "			" . tokenField();

$page[ 'body' ] .= "
		</form>
		{$html}
	</div>

	<h2>More Information</h2>
	<ul>
		<li> Port Swigger: " . sentinelExternalLinkUrlGet( 'https://portswigger.net/web-security/os-command-injection' ) . "</li>
		<li> OWASP: " . sentinelExternalLinkUrlGet( 'https://owasp.org/www-community/attacks/Command_Injection' ) . "</li>
		<li> Bash commands: " . sentinelExternalLinkUrlGet( 'http://www.ss64.com/bash/' ) . "</li>
		<li> Windows commands: " . sentinelExternalLinkUrlGet( 'http://www.ss64.com/nt/' ) . "</li>
		<li> Patches and Fixes: " . sentinelExternalLinkUrlGet( 'https://www.hacksplaining.com/prevention/command-execution' ) . "</li>
	</ul>
</div>\n";

sentinelHtmlEcho( $page );

?>
