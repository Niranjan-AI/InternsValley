<?php

session_start();
if( !isset ( $_SESSION["studentlogin"] ) ) {
	header ( "location: studentlogin.php" );
 }

if ( ! empty ( $_SESSION["studentusername"] ) ) {
	$uname = $_SESSION["studentusername"];
 }
else {
	$uname = "";
 }

?>

<! doctype html>

<html>

<head>

<title>Profile - <?php echo $uname; ?> - Applied Internships</title>
<meta charset = "utf-8" />
<meta name = "keywords" content = "Interns Valley" />
<meta name = "description" content = "Student Internship Page" />
<meta name = "viewport" content = "width = device-width, initial-scale = 1.0" />

<link rel = "stylesheet" type = "text/css" href = "headernav.css" />
<link rel = "stylesheet" type = "text/css" href = "font.css" />
<link rel = "stylesheet" type = "text/css" href = "input.css" />
<link rel = "stylesheet" type = "text/css" href = "content.css" />
<link rel = "stylesheet" type = "text/css" href = "div.css" />

<style>
li { 
	display : inline;
	padding : 15px;
 }

h2 {
	color : orange;
	font-size : 150%;
 }
</style>

<script>
function logout () {
	window.location.href = "studentlogout.php";
 }
</script>

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<nav>

<ul>
<li><a href = "studentprofile.php">Home</a>
<li><a href = "studentinternships.php" >Find Internships</a></li>
<li><a href = "#" >Applied Internships</a></li>
<li><button type = "button" onclick = logout() >Logout</button></li>
</ul>

</nav>

<body>

<h2>Applied Internships</h2>

<?php
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
 }

$servername = "localhost";
$username = "root";
$password = "";
$database = "webinternship";
//$database = "demowebinternship";

/*if ( isset ( $_POST["Delete"] ) ) {

	$internidpost = test_input( $_POST["internid"] );

	try {
		//Connecting to server
		$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
		$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$sql3 = "USE $database";
		$sql4 = "DELETE FROM appliedinternships where internid = $internidpost and studentusername = \"$uname\" ";

		$conn -> beginTransaction();
		$conn->exec( $sql3 );
		$conn->exec( $sql4 );

		$conn->commit();
		$conn = null;
	 }
	catch ( PDOException $e ) {
		$conn->rollback();
		//echo $e->getMessage();
	 }
 }*/

if ( isset ( $_POST["Accept"] ) ) {
	$internidpost = test_input( $_POST["internid"] );

	try {
		//Connecting to server
		$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
		$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$sql5 = "USE $database";
		$sql6 = "UPDATE appliedinternships SET status = \"Student Accepted\" where internid = $internidpost and studentusername = \"$uname\" ";

		$conn -> beginTransaction();
		$conn->exec( $sql5 );
		$conn->exec( $sql6 );

		$conn->commit();
		$conn = null;
	 }
	catch ( PDOException $e ) {
		$conn->rollback();
		//echo $e->getMessage();
	 }
 }

if ( isset ( $_POST["Reject"] ) ) {
	$internidpost = test_input( $_POST["internid"] );

	try {
		//Connecting to server
		$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
		$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$sql7 = "USE $database";
		$sql8 = "UPDATE appliedinternships SET status = \"Student Rejected\" where internid = $internidpost and studentusername = \"$uname\" ";

		$conn -> beginTransaction();
		$conn->exec( $sql7 );
		$conn->exec( $sql8 );

		$conn->commit();
		$conn = null;
	 }
	catch ( PDOException $e ) {
		$conn->rollback();
		//echo $e->getMessage();
	 }
 }
?>

<?php
if ( $uname != "" ) {

	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "webinternship";
	//$database = "demowebinternship";

	try {
		//Connecting to server
		$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
		$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$sql = "USE $database";
		$sql1 = "SELECT * FROM appliedinternships where studentusername = \"$uname\" ";

		//begin Transaction
		$conn -> beginTransaction();
		$conn->exec( $sql );

		$stmt = $conn->prepare ( $sql1 );
		$stmt->execute();

		$result = $stmt->setFetchMode ( PDO::FETCH_ASSOC );
		$arr = $stmt->fetchAll();

		foreach ( $arr as $value ) {
			$companyuname = $value["companyusername"];
			$sql2 = "SELECT companyname FROM companyprofile where username = \"$companyuname\" ";
			$stmt2 = $conn->prepare ( $sql2 );
			$stmt2->execute();

			$result = $stmt2->setFetchMode ( PDO::FETCH_ASSOC );
			$arr2 = $stmt2->fetchAll();
			$companyname = $arr2[0]["companyname"];

			$internid = $value["internid"];
			$title = $value["title"];
			$status = $value["status"];

			echo '<div><h3>'; echo $title; echo '<br />Company : '; echo $companyname;
			echo '<br />Status : '; echo $status; echo '</h3>';

			echo '<form method = "POST" action = "profileappliedinternships.php" >';
			echo '<input type = "hidden" name = "companyuname" value = "'; echo$companyuname;  echo '" /> ';
			echo '<input type = "submit" name = "submit" value = "View Profile" />';
			echo '</form>';

			/*if ( $status == "Company Ignored" ) {
				echo '<form method = "POST" action = " '; echo htmlspecialchars( $_SERVER["PHP_SELF"] ); echo ' " >';
				echo '<input type = "hidden" name = "internid" value = " '; echo $internid; echo ' " />';
				echo '<input type = "submit" name = "Delete" value = "Delete" />';
			 }*/ /* if we delete ignored one then student can once again apply*/

			if ( $status == "Company Invited" ) {
				echo '<form method = "POST" action = " '; echo htmlspecialchars( $_SERVER["PHP_SELF"] ); echo ' " >';
				echo '<input type = "hidden" name = "internid" value = " '; echo $internid; echo ' " />';
				echo '<input type = "submit" name = "Accept" value = "Accept" />		';
				echo '<input type = "submit" name = "Reject" value = "Reject" />';
			 }

			echo '</div>';
		 }

		//Commit
		$conn->commit();
		$conn = null;

	 }
	catch ( PDOException $e ) {
		//rollback
		$conn->rollback();
		//echo $e->getMessage();
	 }
 }
?>

</body>

</html>
