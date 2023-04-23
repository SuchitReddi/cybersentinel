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
			<li><a href=\"./vulnerabilities/xss_s/code/xss-sto.html\" target=\"_blank\" rel\"noopener nofollow noreferrer\">Cross-Site Scripting: Stored</a></li>
			<li><a href=\"./vulnerabilities/xss_d/code/xss-dom.html\" target=\"_blank\" rel\"noopener nofollow noreferrer\">Cross-Site Scripting: DOM</a></li>
			<li><a href=\"./vulnerabilities/\" target=\"_blank\" rel\"noopener nofollow noreferrer\"></a></li>
		</ul>
</div>

<div style=\"font-weight: initial; padding-left: 50px;\">
Examples of some vulnerabilities:<br><br>

SQL Injection: Exploiting a security vulnerability in a database-driven website by inserting malicious SQL code.<br>
Cross-Site Scripting (XSS): Injecting malicious scripts into a website to execute unauthorized actions.<br>
Session Hijacking: Stealing a user's session identifier to gain unauthorized access to a system or account.<br>
Remote Code Execution: Exploiting a vulnerability to execute malicious code on a target system remotely.<br>
URL Redirection: Manipulating URLs to send users to malicious websites.<br>
ARP Spoofing: Associating an attacker's MAC address with the IP address of a target device to intercept data packets.<br>
Clickjacking: Concealing malicious links or buttons within legitimate web content to trick users into performing unintended actions.<br>
Privilege Escalation: Exploiting a vulnerability to gain higher-level permissions on a system.<br>
Brute Force Attacks: Attempting all possible password combinations to gain unauthorized access.<br>
Dictionary Attacks: Attempting common words or phrases as passwords.<br>
Drive-By Downloads: Compromising a website to automatically download malware onto a visitor's computer.<br>
Phishing: Sending fraudulent emails or messages to trick recipients into providing sensitive information.<br>
Social Engineering: Manipulating people into revealing confidential information or performing actions that compromise security.<br>
Password Attacks: Attempting to crack or guess user passwords.<br>
Keylogging: Monitoring and recording a user's keystrokes to collect sensitive information.<br>
Man-in-the-Middle Attacks: Intercepting and altering communication between two parties without their knowledge.<br>
Denial-of-Service Attacks: Overwhelming a system or network with traffic, making it unavailable to users.<br>
Distributed Denial-of-Service Attacks: Using multiple systems to conduct a coordinated DoS attack.<br>
Zero-Day Exploits: Taking advantage of previously unknown security vulnerabilities in software or hardware.<br>
Malware: Using malicious software to gain unauthorized access, disrupt operations, or steal sensitive information.<br>
Ransomware: Encrypting a victim's data and demanding payment for its release.<br>
DNS Hijacking: Redirecting traffic from a legitimate website to a malicious one.<br>
Watering Hole Attacks: Compromising a website commonly visited by a targeted group to distribute malware.<br>
Wireless Network Attacks: Gaining unauthorized access to Wi-Fi networks.<br>

</div>";

sentinelHtmlEcho( $page );