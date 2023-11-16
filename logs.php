<?php

define('SENTINEL_WEB_PAGE_TO_ROOT', '');
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup(array('authenticated'));

sentinelDatabaseConnect();
/*
//This whole code should be run in every page to get the log of every page view
// Function to get the user's IP address
function getUserIP() {
    $ip = '';

    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED'];
    } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
        $ip = $_SERVER['HTTP_FORWARDED'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

// Function to insert a log entry
function insertLogEntry($ip, $visited, $time) {
    $query = "INSERT INTO logs (ip, visited, time) VALUES ('$ip', '$visited', '$time')";
    mysqli_select_db($GLOBALS["___mysqli_ston"],  "sentinel" );
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

    if (!$result) {
        die("Failed to insert log entry: " . mysqli_error($GLOBALS["___mysqli_ston"]));
    }
}

// Test: Insert log entry when the page is accessed
//$user = isset($_SESSION['user']) ? $_SESSION['user'] : 'guest'; // Replace with your actual user data
$ip = getUserIP();
$visited = $_SERVER['REQUEST_URI'];
$time = date("Y-m-d H:i:s");

insertLogEntry($ip, $visited, $time);
 */
$page = sentinelPageNewGrab();
$page['title'] = 'Log Viewer';
$page['page_id'] = 'log_viewer';

// Fetch logs from the 'logs' table
$query = "SELECT * FROM logs";
mysqli_select_db($GLOBALS["___mysqli_ston"], "sentinel");
$result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

// Fetch logs count from the 'logs' table
$countQuery = "SELECT COUNT(*) as log_count FROM logs";
mysqli_select_db($GLOBALS["___mysqli_ston"], "sentinel");
$countResult = mysqli_query($GLOBALS["___mysqli_ston"], $countQuery);
$logCount = ($countResult) ? mysqli_fetch_assoc($countResult)['log_count'] : 0;

// Clear button logic
if (isset($_POST['clear_logs'])) {
    $clearQuery = "TRUNCATE TABLE logs";
    mysqli_query($GLOBALS["___mysqli_ston"], $clearQuery);
    sentinelMessagePush('All log records cleared.');
    sentinelPageReload();
}

$logHtml = '<h2>Log Records</h2>';
$logHtml .= '<table border="10px" style="border-color: black;">
    <tr>
        <th>Log ID</th>
        <th>User</th>
        <th>IP</th>
        <th>Visited Page</th>
        <th>Time</th>
    </tr>';

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $logHtml .= "<tr>
            <td>{$row['log_id']}</td>
            <td>{$row['user']}</td>
            <td>{$row['ip']}</td>
            <td>{$row['visited']}</td>
            <td>{$row['time']}</td>
        </tr>";
    }
    mysqli_free_result($result);
} else {
    $logHtml .= '<tr><td colspan="5">No Records Found</td></tr>';
}

$logHtml .= '</table>';

$page['body'] = "
<div class=\"body_padded\">
    <h1>Log Viewer</h1>
    <br>
    {$logCount} Log Records Found
    <form action=\"#\" method=\"POST\">
        <input type=\"submit\" value=\"Clear\" name=\"clear_logs\">
    </form>
    <br />
    {$logHtml}
</div>";

sentinelHtmlEcho($page);

?>
