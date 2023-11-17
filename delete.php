<?php

define('SENTINEL_WEB_PAGE_TO_ROOT', ''); // Adjust as needed
require_once SENTINEL_WEB_PAGE_TO_ROOT . 'sentinel/includes/sentinelPage.inc.php';

sentinelPageStartup(array());
sentinelDatabaseConnect();

$messagesHtml = messagesPopAllToHtml();

// Assuming you have a function to get the current user
$currentuser = sentinelCurrentUser();

// Display the confirmation form
echo "<!DOCTYPE html>
<html lang=\"en-GB\">
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />
    <title>Delete User :: Cyber Sentinel</title>
    <link rel=\"stylesheet\" type=\"text/css\" href=\"" . SENTINEL_WEB_PAGE_TO_ROOT . "sentinel/css/login.css\" />
</head>
<body>
    <div id=\"header\">
        <div id=\"main_menu_padded\">
            <div style=\"padding: 10px\" id=\"main_body\">
            <center>
                <h2>Confirm User Deletion</h2>
                <p>Are you sure you want to delete the current user ({$currentuser})?<br>
                This action is irreversible!</p>
                <form action=\"delete.php\" method=\"post\">
                    <label style=\"float: none; text-align: center;\" for=\"password\">Enter Password:</label>
                    <input type=\"password\" name=\"password\" AUTOCOMPLETE=\"off\" size=\"20\" required>
                    <input type=\"submit\" name=\"delete_confirm\" value=\"Yes, Delete\">
                </form>
                <button><a style=\"text-decoration: none; color: black;\" href=\"/cybersentinel/index.php\">Cancel</a></button>
                {$messagesHtml}
            </center>
            </div>
        </div>
    </div>
</body>
</html>";

// Function to validate the password (replace with your actual validation logic)
function validatePassword($currentuser, $enteredPassword) {
    //$enteredPassword = password_hash($enteredPassword, PASSWORD_DEFAULT); // More secure way.
    $enteredPassword = stripslashes($enteredPassword);
    $enteredPassword = md5($enteredPassword); // Less secure way. (don't use MD5 hashing)

    // Password validation logic
    $query = "SELECT password FROM users WHERE user = '{$currentuser}'";
    mysqli_select_db($GLOBALS["___mysqli_ston"], "sentinel");
    $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);

    if ($result && mysqli_num_rows($result) != 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['password'];
        // Example: Check against the stored hashed password in the database
        if ($hashedPassword === $enteredPassword) {
            return true;
        }
        else {
            return false;
        }
    }
    sentinelMessagePush("There is an error with the username");
    return false;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_confirm'])) {
        $enteredPassword = $_POST['password']; // Assuming your form has an input named 'password'
        
        // Validate the password
        if (validatePassword($currentuser, $enteredPassword)) {
            // Password is correct, proceed with deletion
            $deleteuser = "DELETE FROM users WHERE user = '{$currentuser}'";
            $result = mysqli_query($GLOBALS["___mysqli_ston"], $deleteuser);

            if ($result) {
                sentinelMessagePush("User '{$currentuser}' deleted successfully.");
                sentinelRedirect(SENTINEL_WEB_PAGE_TO_ROOT . 'login.php'); // Redirect to the desired page
            } else {
                sentinelMessagePush("Error deleting user '{$currentuser}': " . mysqli_error($GLOBALS["___mysqli_ston"]));
            }
        } else {
            sentinelMessagePush("Incorrect password. User not deleted.");
            sentinelPageReload();
        }
    }
}
?>