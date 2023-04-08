<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '../../' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( 'authenticated' ) );

$page = sentinelPageNewGrab();
$page[ 'title' ]   = 'Vulnerability: DOM Based Cross Site Scripting (XSS)' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'xss_d';
$page[ 'help_button' ]   = 'xss_d';
$page[ 'source_button' ] = 'xss_d';

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

require_once SENTINEL_WEB_PAGE_TO_ROOT . "vulnerabilities/xss_d/source/{$vulnerabilityFile}";

# For the impossible level, don't decode the querystring
$decodeURI = "decodeURI";
if ($vulnerabilityFile == 'impossible.php') {
	$decodeURI = "";
}

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Vulnerability: DOM Based Cross Site Scripting (XSS)</h1>

	<center>
	<button>
    <a href=\"./code/xss-dom.html\">Tutorial</a>
	</button>
	</center>

	<div class=\"vulnerable_code_area\">
 
 		<p>Please choose a language:</p>

		<form name=\"XSS\" method=\"GET\">
			<select name=\"default\">
				<script>
					if (document.location.href.indexOf(\"default=\") >= 0) {
						var lang = document.location.href.substring(document.location.href.indexOf(\"default=\")+8);
						document.write(\"<option value='\" + lang + \"'>\" + $decodeURI(lang) + \"</option>\");
						document.write(\"<option value='' disabled='disabled'>----</option>\");
					}
					    
					document.write(\"<option value='English'>English</option>\");
					document.write(\"<option value='French'>French</option>\");
					document.write(\"<option value='Spanish'>Spanish</option>\");
					document.write(\"<option value='German'>German</option>\");
				</script>
			</select>
			<input type=\"submit\" value=\"Select\" />
		</form>
	</div>";

$page[ 'body' ] .= "
	<h2>More Information</h2>
	<ul>
		<li>Port Swigger: " . sentinelExternalLinkUrlGet( 'https://portswigger.net/web-security/cross-site-scripting/dom-based' ) . "</li>
		<li>OWASP: " . sentinelExternalLinkUrlGet( 'https://owasp.org/www-community/attacks/DOM_Based_XSS' ) . "</li>
		<li>XSS Cheat Sheet: " . sentinelExternalLinkUrlGet( 'https://portswigger.net/web-security/cross-site-scripting/cheat-sheet' ) . "</li>
		<li>Patches and Fixes: " . sentinelExternalLinkUrlGet( 'https://www.hacksplaining.com/prevention/xss-dom' ) . "</li>
	</ul>
</div>\n";

sentinelHtmlEcho( $page );

?>
