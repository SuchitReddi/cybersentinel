<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '../../' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( 'authenticated' ) );

$page = sentinelPageNewGrab();
$page[ 'title' ]   = 'Vulnerability: Reflected Cross Site Scripting (XSS)' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'xss_r';
$page[ 'help_button' ]   = 'xss_r';
$page[ 'source_button' ] = 'xss_r';

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

require_once SENTINEL_WEB_PAGE_TO_ROOT . "vulnerabilities/xss_r/source/{$vulnerabilityFile}";

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Vulnerability: Reflected Cross Site Scripting (XSS)</h1>

	<center>
	<button>
    <a href=\"./code/xss-ref.html\">Tutorial</a>
	</button>
	</center>
	
	<div class=\"vulnerable_code_area\">
		<form name=\"XSS\" action=\"#\" method=\"GET\">
			<p>
				What's your name?
				<input type=\"text\" name=\"name\">
				<input type=\"submit\" value=\"Submit\">
			</p>\n";

if( $vulnerabilityFile == 'impossible.php' )
	$page[ 'body' ] .= "			" . tokenField();

$page[ 'body' ] .= "
		</form>
		{$html}
	</div>

	<h2>More Information</h2>
	<ul>
		<li>Port Swigger: " . sentinelExternalLinkUrlGet( 'https://portswigger.net/web-security/cross-site-scripting/reflected' ) . "</li>
		<li>Port Swigger XSS Cheat Sheet: " . sentinelExternalLinkUrlGet( 'https://portswigger.net/web-security/cross-site-scripting/cheat-sheet' ) . "</li>
		<li>OWASP: " . sentinelExternalLinkUrlGet( 'https://owasp.org/www-community/attacks/xss/' ) . "</li>
		<li>OWASP XSS Cheat Sheet: " . sentinelExternalLinkUrlGet( 'https://owasp.org/www-community/xss-filter-evasion-cheatsheet' ) . "</li>
		<li>Wikipedia: " . sentinelExternalLinkUrlGet( 'https://en.wikipedia.org/wiki/Cross-site_scripting' ) . "</li>
		<li>Patches and Fixes: " . sentinelExternalLinkUrlGet( 'https://www.hacksplaining.com/prevention/xss-reflected' ) . "</li>
	</ul>
</div>\n";

sentinelHtmlEcho( $page );

?>
