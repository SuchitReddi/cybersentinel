<?php

define('SENTINEL_WEB_PAGE_TO_ROOT', '');
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup(array('authenticated'));

$page = sentinelPageNewGrab();
$page['title']   = 'Welcome' . $page['title_separator'] . $page['title'];
$page['page_id'] = 'home';

$page['body'] .= "
<div class=\"body_padded\">
	<h1 style=\"text-align: center;\">Welcome to Cyber Sentinel!</h1>
	<p>Cyber Sentinel is a PHP/MySQL web application that is made intentionally vulnerable! The aim of this project is to increase cyber awareness among modern
	web developers.<br>
	Incorporating security during development stage itself is a good practice which isn't always possible due to deadlines. So, you can learn how to give your 
	applications the minimum level of security, which is always better than leaving them unprotected. I added tutorials which will show you how to use this web 
	application to improve or learn about different vulnerabilities step by step.</p>
	<p>Cyber Sentinel helps you to practice some of the most common, yet dangerous web vulnerabilities, teach you their patches, with a simple interface.</p>
	<hr />
	<br />

	<h2 style=\"text-align: center;\">Instructions</h2>
	<p>It's up to you, how you want to approach Cyber Sentinel. I provided tutorials for the low difficulty level. I will try to add them for other levels too.<br> 
	You can work on every module at a fixed difficulty level, or select one module and try to reach the highest level you can before moving onto the next one.
	There is no fixed objective to complete a module, if you feel you've exploited the system thoroughly, the goal is reached!</p>
	<p>There is a help button at the bottom to view hints & tips for that vulnerability. There are additional links for further reading on any vulnerability.</p>
	<hr />
	<br />

	<h2 style=\"text-align: center;\">WARNING!</h2>
	<p>This application is vulnerable! There are many documented vulnerabilities causing more undocumented ones. <em>Do not upload it to any internet facing 
	servers</em>, as they will be compromised.<br>
	It is recommend using a virtual machine such as 
	(" . sentinelExternalLinkUrlGet('https://www.virtualbox.org/', 'VirtualBox') . " or " . sentinelExternalLinkUrlGet('https://www.vmware.com/', 'VMware') ."),
	which is set to NAT networking mode. You can download and install " . sentinelExternalLinkUrlGet('https://www.apachefriends.org/', 'XAMPP') . " 
	for the web server and database.<br>
	I'm trying to dockerize the application which is supposed to provide similar protection as a virtual machine, but doesn't take up as much storage.</p>
	<br />
	<h3 style=\"text-align: center;\">Disclaimer</h3>
	<p>I do not take responsibility for the way in which any one uses this application (Cyber Sentinel). I have made the purposes of the application clear and it
	should not be used maliciously. I have given warnings and taken measures to prevent users from installing Cyber Sentinel on to live web servers. 
	If any web server is compromised via an installation of Cyber Sentinel, it is not my responsibility. It is the responsibility of the person/s who uploaded and
	installed it.</p>
</div>";

sentinelHtmlEcho($page);
