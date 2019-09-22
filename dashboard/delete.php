<?php
// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    echo "<script>alert('Please log in to delete files!');</script>";
    echo "<script>window.location.href='../auth/login.php';</script>";
    exit;
}
?>
<?php
// Retreiving Image ID
$id=intval($_GET['id']);

//Linking username from database to a variable for verification purposes.
$conn = mysqli_connect("localhost","root","root","ZirconDB")
or die("<script>alert('Error in Database Connection, please try again later!');</script>");

	$sql = "SELECT username FROM images WHERE id=$id";
	$result = mysqli_query($conn, $sql);

	if(mysqli_affected_rows($conn)>0)
	{
		while($row = $result->fetch_assoc()) {
        $username = $row["username"];
		
	}
	} else {
    echo "<script>alert('Error in Database Connection, please try again later!');</script>";
	}
	
//Checks whether session username and database username is the same.
	if($username != $_SESSION['username']){
		echo "<script>alert('You are not the owner of this image!');</script>";
		die("<script>window.history.go(-1);</script>");
		
	} else {
		
		$conn = mysqli_connect("localhost","root","root","ZirconDB")
		or die("<script>alert('Error in Database Connection, please try again later!');</script>");

		$sql = "DELETE FROM images WHERE id=$id";
		$result = mysqli_query($conn, $sql);

		if(mysqli_affected_rows($conn)<=0)
		{
			echo "<script>alert('Unable to delete image from database!');";
			die ("window.location.href='myimages.php';</script>");
		}

		mysqli_close($conn);

		echo "<script>alert('Image deleted!');</script>";
		echo "<script>window.location.href='myimages.php';</script>";
	}
?>


    
