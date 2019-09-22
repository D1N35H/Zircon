<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect them back to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Zircon: Your uploaded images</title>
<link rel="icon" type="image/ico" href="../favicon.ico" />
<link href="../dashboard.css" rel="stylesheet" type="text/css"/>
<link href="https://fonts.googleapis.com/css?family=Lato|Open+Sans|Roboto:100,400|Source+Sans+Pro:200|Titillium+Web|Ubuntu" rel="stylesheet">

<style>
table {
    border-collapse: collapse;
    width: 100%;
}

th {
    background-color: #fc2b53;
    color: white;
}

tr{
  background-color: #f2f2f2
}

th, td {
    text-align: center;
    padding: 6px;
}
</style>


</head>

<body>
<div id="auth-heading">
<img alt="Zircon Logo" src="../resources/images/zircon-transparent.png" style="height:75px;"/>
<a href="upload.php" style="text-decoration:none; color:white">Zircon's User Dashboard</a>
</div>

<div class="topnav" id="myTopnav">
  <a href="upload.php">Upload</a>
  <a href="myimages.php" class="active">My Uploaded Images</a>
  <a href="usersettings.php">User Settings</a>
  <a href="logout.php" style="float:right">Logout</a>
</div>


	<h1><?php echo htmlspecialchars($_SESSION["username"]); ?>'s uploaded images</h1>
	<table border="1">
		<tr>
			<th>Image Name</th>
			<th>Image</th>
			<th>Options</th>

      <?php

      	// Retreiving account username
      	$username = $_SESSION["username"];

      	//table content that retrieved from database
      	$conn = mysqli_connect("localhost","root","root","ZirconDB")
      	or die("<script>alert('Error in Database Connection, please try again later!');</script>");

      			//select from the database
      			$sql= "Select * FROM images WHERE Username = '$username'";
      			$result = mysqli_query($conn,$sql)
      			or die("<script>alert('Check your query statement');</script>");


      			while($rows = mysqli_fetch_array($result))
      			{
      				echo "<tr>";

      				echo "<td>".$rows['image_name']."</td>";
      				//e.g.download1.jpg / download2.png

      				$getFileType = pathinfo($rows['image_name'],PATHINFO_EXTENSION);

      				echo "<td><img src='data:image/$getFileType;base64,".
      				$rows['image']."' height='150px'/></td>";

              echo "<td><a href='delete.php?id=".$rows['id']."'>Delete</a></td>";
      				echo "</tr>";
      			}
?>

</body>
</html>
