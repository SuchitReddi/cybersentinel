<?php

define( 'SENTINEL_WEB_PAGE_TO_ROOT', '../../' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup( array( 'authenticated' ) );

$page = sentinelPageNewGrab();
$page[ 'title' ]   = 'Vulnerability: Stored Cross Site Scripting (XSS)' . $page[ 'title_separator' ].$page[ 'title' ];
$page[ 'page_id' ] = 'xss_s';
$page[ 'help_button' ]   = 'xss_s';
$page[ 'source_button' ] = 'xss_s';

sentinelDatabaseConnect();

if (array_key_exists ("btnClear", $_POST)) {
	$query  = "TRUNCATE guestbook;";
	$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );
}

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

require_once SENTINEL_WEB_PAGE_TO_ROOT . "vulnerabilities/xss_s/source/{$vulnerabilityFile}";

$page[ 'body' ] .= "
<div class=\"body_padded\">
	<h1>Vulnerability: Stored Cross Site Scripting (XSS)</h1>

	<div class=\"vulnerable_code_area\">
		<form method=\"post\" name=\"guestform\" \">
			<table width=\"550\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\">
				<tr>
					<td width=\"100\">Name *</td>
					<td><input name=\"txtName\" type=\"text\" size=\"30\" maxlength=\"10\"></td>
				</tr>
				<tr>
					<td width=\"100\">Message *</td>
					<td><textarea name=\"mtxMessage\" cols=\"50\" rows=\"3\" maxlength=\"50\"></textarea></td>
				</tr>
				<tr>
					<td width=\"100\">&nbsp;</td>
					<td>
						<input name=\"btnSign\" type=\"submit\" value=\"Sign Guestbook\" onclick=\"return validateGuestbookForm(this.form);\" />
						<input name=\"btnClear\" type=\"submit\" value=\"Clear Guestbook\" onClick=\"return confirmClearGuestbook();\" />
					</td>
				</tr>
			</table>\n";

if( $vulnerabilityFile == 'impossible.php' )
	$page[ 'body' ] .= "			" . tokenField();

$page[ 'body' ] .= "
		</form>
		{$html}
	</div>
	<br />

	" . sentinelGuestbook() . "
	<br />

	<h2>More Information</h2>
	<ul>
		<li>" . sentinelExternalLinkUrlGet( 'https://owasp.org/www-community/attacks/xss' ) . "</li>
		<li>" . sentinelExternalLinkUrlGet( 'https://owasp.org/www-community/xss-filter-evasion-cheatsheet' ) . "</li>
		<li>" . sentinelExternalLinkUrlGet( 'https://en.wikipedia.org/wiki/Cross-site_scripting' ) . "</li>
		<li>" . sentinelExternalLinkUrlGet( 'http://www.cgisecurity.com/xss-faq.html' ) . "</li>
		<li>" . sentinelExternalLinkUrlGet( 'http://www.scriptalert1.com/' ) . "</li>
	</ul>
</div>\n";

sentinelHtmlEcho( $page );

?>
