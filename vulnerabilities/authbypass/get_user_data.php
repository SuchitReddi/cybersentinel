<?php
define( 'SENTINEL_WEB_PAGE_TO_ROOT', '../../' );
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelDatabaseConnect();

/*
On high and impossible, only the admin is allowed to retrieve the data.
*/
if ((sentinelSecurityLevelGet() == "high" || sentinelSecurityLevelGet() == "impossible") && sentinelCurrentUser() != "admin") {
	print json_encode (array ("result" => "fail", "error" => "Access denied"));
}

$query  = "SELECT user_id, first_name, last_name FROM users";
//Uncomment if you are getting an error saying no database selected.
//mysqli_select_db($GLOBALS["___mysqli_ston"],  "sentinel" );
$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query );

$guestbook = ''; 
$users = array();

while ($row = mysqli_fetch_row($result) ) { 
	if( sentinelSecurityLevelGet() == 'impossible' ) { 
		$user_id = $row[0];
		$first_name = htmlspecialchars( $row[1] );
		$surname = htmlspecialchars( $row[2] );
	} else {
		$user_id = $row[0];
		$first_name = $row[1];
		$surname = $row[2];
	}   

	$user = array (
					"user_id" => $user_id,
					"first_name" => $first_name,
					"surname" => $surname
				);
	$users[] = $user;
}

print json_encode ($users);
exit;
?>
