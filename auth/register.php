<?php
session_start();

// Check if the user is already logged in, if yes then redirect him to upload page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: ../dashboard/upload.php");
  exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
					echo '<script type="text/javascript">',
						 'usernameexists();',
						 '</script>';
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
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
<title>Register with Zircon today!</title>
<link rel="icon" type="image/ico" href="../favicon.ico" />
<link href="https://fonts.googleapis.com/css?family=Lato|Open+Sans|Roboto:100,400|Source+Sans+Pro:200|Titillium+Web|Ubuntu" rel="stylesheet">

<link href="../main.css" rel="stylesheet" type="text/css"/>
</head>

<body>

<div id="auth-heading">
<img alt="Zircon Logo" src="../resources/images/zircon-transparent.png" style="height:100px;"/>
<a href="../index.html" style="text-decoration:none; color:white">Zircon</a>
</div>

<div class="transbox">
<div id="auth-title" style="color:black">Registration System</div>
	<br/>
		<div id="form-styling">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

				Username: <input type="text" name="username" required="required"><br/>
				<span class="usernameerr-block"><?php echo $username_err; ?></span>

				Password: <input type="password" name="password" required="required"><br/>
				<span class="passworderr-block"><?php echo $password_err; ?></span>

				Confirm Password: <input type="password" name="confirm_password" required="required"><br/><br/>
				<span class="passworderr-block"><?php echo $confirm_password_err; ?></span>

				<input onclick="reset" class="resetbutton-design" type="reset" value="Clear"/>
				<input onclick="submit" class="registerbutton-design" type="submit" value="Register"/>
			</form>
		</div>
</div>

</body>
</html>
