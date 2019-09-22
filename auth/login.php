<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to upload page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: ../dashboard/upload.php");
  exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: ../dashboard/upload.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
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
<title>Authentication</title>
<link rel="icon" type="image/ico" href="../favicon.ico" />
<link href="../main.css" rel="stylesheet" type="text/css"/>
<link href="https://fonts.googleapis.com/css?family=Lato|Open+Sans|Roboto:100,400|Source+Sans+Pro:200|Titillium+Web|Ubuntu" rel="stylesheet">
<script src="../resources/js/jquery.min.js"></script>
</head>

<body>

<div id="auth-heading">
<img alt="Zircon Logo" src="../resources/images/zircon-transparent.png" style="height:100px;"/>
<a href="../index.html" style="text-decoration:none; color:white">Zircon</a>
</div>

<div class="transbox">
<div id="auth-title" style="color:black">Authentication System</div>
	<br/>
		<br/>
		<div id="form-styling">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			
				Username: <input type="text" name="username" required="required"><br/>
				<span class="usernameerr-block"><?php echo $username_err; ?></span>
								
				
				Password: <input type="password" name="password" required="required"><br/><br/>
				<span class="passworderr-block"><?php echo $password_err; ?></span>
					
				<input onclick="reset" class="resetbutton-design" type="reset" value="Clear"/>
				<input onclick="submit" class="registerbutton-design"  type="submit" value="Login"/>
			</form>
		</div>
</div>


</body>
</html>


