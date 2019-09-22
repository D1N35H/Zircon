<?php
// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../auth/login.php");
    exit;
}

// Include config file
require_once "../auth/config.php";

// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter the new password.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);

            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page

				echo '<script language="javascript">';
				echo 'alert("Your password has been updated!")';
				echo '</script>';

        echo('<script>window.history.go(-1);</script>');

            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html>

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Zircon: Your user settings</title>
<link rel="icon" type="image/ico" href="../favicon.ico" />
<link href="../dashboard.css" rel="stylesheet" type="text/css"/>
<link href="https://fonts.googleapis.com/css?family=Lato|Open+Sans|Roboto:100,400|Source+Sans+Pro:200|Titillium+Web|Ubuntu" rel="stylesheet">

<style>
.whitebackground {
    background-color:#eeeff0;
    padding:10px;
}
</style>


</head>

<body>
<!-- Dashboard Logo -->
<div id="auth-heading">
<img alt="Zircon Logo" src="../resources/images/zircon-transparent.png" style="height:75px;"/>
<a href="upload.php" style="text-decoration:none; color:white">Zircon's User Dashboard</a>
</div>

<!-- Navigational Bar -->
<div class="topnav" id="myTopnav">
  <a href="upload.php">Upload</a>
  <a href="myimages.php">My Uploaded Images</a>
  <a href="usersettings.php" class="active">User Settings</a>
  <a href="logout.php" style="float:right">Logout</a>
</div>

<!-- Actual Content of the website -->
<h1>Change Password</h1>

<div class="whitebackground">
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
  <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
        <label>New Password: </label>
        <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
	<span class="help-block"><?php echo $new_password_err; ?></span>
  </div>


	<div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
        <label>Confirm Password: </label>
        <input type="password" name="confirm_password" class="form-control">
        <span class="help-block"><?php echo $confirm_password_err; ?></span>
  </div>
	<br/>
  <div class="form-group">
    	<input type="reset" class="resetbutton-design" value="Reset">
      <input type="submit" class="registerbutton-design" value="Submit">
  </div>
</div>
</form>

</body>
</html>
