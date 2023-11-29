<?php

define('SENTINEL_WEB_PAGE_TO_ROOT', '');
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup(array());

sentinelDatabaseConnect();

if (isset($_POST['Signup'])) {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    
    $username = $_POST['username'];
    $username = stripslashes($username);

    $password = $_POST['password'];
    $password = stripslashes($password);
    //$enteredPassword = password_hash($enteredPassword, PASSWORD_DEFAULT); // More secure way.
    $password = md5($password);

    // Insert user into 'users' table
    $insert_query = "INSERT INTO users (first_name, last_name, user, password) 
                     VALUES ('$first_name', '$last_name', '$username', '$password')";

    mysqli_select_db($GLOBALS["___mysqli_ston"], "sentinel");
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $insert_query);
    sentinelMessagePush( "Hi '{$first_name} {$last_name}', signup successful" );
    sentinelRedirect( SENTINEL_WEB_PAGE_TO_ROOT . 'login.php' );
}

$messagesHtml = messagesPopAllToHtml();

Header('Cache-Control: no-cache, must-revalidate');
Header('Content-Type: text/html;charset=utf-8');
Header('Expires: Tue, 23 Jun 2009 12:00:00 GMT');

echo <<<HTML
<!DOCTYPE html>
<html lang="en-GB">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Signup :: Cyber Sentinel</title>
    <link rel="stylesheet" type="text/css" href="/cybersentinel/sentinel/css/login.css" />
</head>
<style>
    .signupInput {
        float: center;
        color: #6B6B6B;
        width: 340px;
        background-color: #F4F4F4;
        border: 1px;
        border-style: solid;
        border-color: #c4c4c4;
        padding: 6px;
        margin-bottom: 6px;
}
</style>
<body>
<p><center><img src="/cybersentinel/sentinel/images/login_logo.png" style="width: 400px; align: center;" /></center></p>

<div id="wrapper">
    <div id="content">
        <form action="signup.php" method="post">
            <fieldset>
                <label for="first_name">First Name</label>
                <input type="text" class="signupInput" size="20" name="first_name" required><br />

                <label for="last_name">Last Name</label>
                <input type="text" class="signupInput" size="20" name="last_name" required><br />

                <label for="username">Username</label>
                <input type="text" class="signupInput" size="20" name="username" required><br />

                <label for="password">Password</label>
                <input type="password" class="signupInput" AUTOCOMPLETE="off" size="20" name="password" required><br />

                <p class="submit"><input type="submit" value="Signup" name="Signup"></p>
            </fieldset>
        </form>
        <br />
        {$messagesHtml}
        <br /><br /><br /><br /><br /><br /><br /><br />
        <div id="footer">
            <p><a href="https://github.com/SuchitReddi/cybersentinel" target="_blank" rel="noreferrer nofollow noopener">Cyber Sentinel</a>
        </div> <!-- <div id="footer"> -->
    </div> <!-- <div id="content"> -->
</div> <!-- <div id="wrapper"> -->
</body>
</html>
HTML;
?>
