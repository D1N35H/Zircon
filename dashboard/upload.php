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

if (isset($_POST['submit']))
{
	// Retreiving account username
	$username = $_SESSION["username"];

	//Retreiving the basename from the file path e.g. C:/wamp64/image1.jpg
	$filename = trim(basename($_FILES['image']['name']));

	//Retreiving the filesize
	$getFileSize = $_FILES['image']['size'];

	//Retreiving the filetype, modified to lowercase to avoid false error with captalised Format names.
	$getFileType = strtolower(pathinfo($filename,PATHINFO_EXTENSION));

	//mediumblob max size = 16777215 (2^24 - 1)
	if($getFileSize > 1000000)
	{
		echo "<script>alert('File size is too big. Please choose a smaller image!');</script>";
		die("<script>window.history.go(-1);</script>");
	}

	//check for the file type
	if($getFileType != "jpg" && $getFileType != "jpeg" && $getFileType != "png" && $getFileType !="gif")
	{
		echo "<script>alert('Wrong file type.";
		echo "Please choose jpg/jpeg/png/gif images!');</script>";
		die("<script>window.history.go(-1);</script>");
	}

	//Validation passed then move image from tmp folder to database, image encoded into Base64 Format.

    $fileobjectpath = $_FILES['image']['tmp_name'];
	$getimageobj = base64_encode(file_get_contents($fileobjectpath));

	//connection to db
	$conn = mysqli_connect("localhost","root","root","ZirconDB")
	or die("<script>alert('Error in database connection, try again later!');</script>");

	//insert to the database
	$sql= "INSERT INTO images (image_name, image, username) VALUES ('$filename','$getimageobj','$username');";
	mysqli_query($conn,$sql) or die("<script>alert('Error in query');</script>");

	echo "<script>alert('The image has been successfully uploaded!');</script>";
	mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Zircon: Upload your images now!</title>
<link rel="icon" type="image/ico" href="../favicon.ico" />
<link href="../dashboard.css" rel="stylesheet" type="text/css"/>
<link href="https://fonts.googleapis.com/css?family=Lato|Open+Sans|Roboto:100,400|Source+Sans+Pro:200|Titillium+Web|Ubuntu" rel="stylesheet">

<style>
.whitebackground {
    background-color:#eeeff0;
    padding:10px;
}

p {
	color:black;
	font-size:20px;
	margin-left:5px;
}

</style>

</head>

<body>
<div id="auth-heading">
<img alt="Zircon Logo" src="../resources/images/zircon-transparent.png" style="height:75px;"/>
<a href="upload.php" style="text-decoration:none; color:white">Zircon's User Dashboard</a>
</div>

<div class="topnav">
  <a href="upload.php" class="active">Upload</a>
  <a href="myimages.php">My Uploaded Images</a>
  <a href="usersettings.php">User Settings</a>
  <a href="logout.php" style="float:right">Logout</a>
</div>

<h1>Image Uploader</h1>

<div class="whitebackground">
<form action="upload.php" method="post" enctype="multipart/form-data">

<p>Select image to upload:</p>
    <input class="resetbutton-design" type="file" accept="image/*" name="image" required="required" style="width:300px;">
    <input class="registerbutton-design" type="submit" value="Upload Image" name="submit" style="width:300px;">
<p><span style="color:#f21818">Maximum File Size is 1 MB!</p>
</form>
</div>

</body>
</html>
