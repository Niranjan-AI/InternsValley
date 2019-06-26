<?php

session_start();
if( !isset ( $_SESSION["companylogin"] ) ) {
	header ( "location: companylogin.php" );
 }

if ( ! empty ( $_SESSION["companyusername"] ) ) {
	$uname = $_SESSION["companyusername"];
 }
else {
	$uname = "";
 }

?>

<! doctype html>

<html>

<head>

<title>Profile - <?php echo $uname; ?> - Interns Valley</title>
<meta charset = "utf-8" />
<meta name = "keywords" content = "Interns Valley" />
<meta name = "description" content = "Company Internships Page" />
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
	window.location.href = "companylogout.php";
 }
</script>

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<nav>

<ul>
<li><a href = "companyprofile.php" >Home</a></li>
<li><a href = "companymanage.php" >Manage Internships</a></li>
<li><a href = "#" >View Applied Internships</a></li>
<li><a href = "invitedapplications.php" >Invited Applicants</a></li>
<li><button type = "button" onclick = logout() >Logout</button></li>
</ul>

</nav>

<body>
<h2>List of Applied Students</h2>
<h3 id = "message" ></h3>

<?php
$status = "";
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

if ( isset ( $_POST["Ignore"] ) ) {
	try {
		$internidpost = test_input( $_POST["internid"] );
		$studentusernamepost = test_input ( $_POST["studentusername"] );

		//Connecting to server
		$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
		$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$sql = "USE $database";
		$sql1 = "UPDATE appliedinternships
			SET status = \"Company Ignored\"
			WHERE internid = \"$internidpost\" and studentusername = \"$studentusernamepost\" ";

		$conn->beginTransaction();

		$conn->exec ( $sql );
		$conn->exec ( $sql1 );
		$status = true;

		$conn->commit();
		$conn =null;
	 }
	catch ( PDOException $e ) {
		//rollback
		$conn->rollback();
		//echo $e->getMessage();
	 }
 }

if( isset ( $_POST["Invite"] ) ) {
	try {
		$internidpost = test_input( $_POST["internid"] );
		$studentusernamepost = test_input ( $_POST["studentusername"] );

		//Connecting to server
		$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
		$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$sql = "USE $database";
		$sql1 = "UPDATE appliedinternships
			SET status = \"Company Invited\"
			WHERE internid = \"$internidpost\" and studentusername = \"$studentusernamepost\" ";

		$conn->beginTransaction();

		$conn->exec ( $sql );
		$conn->exec ( $sql1 );
		$status = true;

		$conn->commit();
		$conn =null;
	 }
	catch ( PDOException $e ) {
		//rollback
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
		$sql1 = "SELECT * FROM appliedinternships WHERE companyusername = \"$uname\" and status = \"Pending\" ";

		$conn->beginTransaction();
		$conn->exec( $sql );

		$stmt = $conn->prepare ( $sql1 );
		$stmt->execute();

		$result = $stmt->setFetchMode ( PDO::FETCH_ASSOC );
		$arr = $stmt->fetchAll();

		foreach ( $arr as $value ) {
			$internid = $value["internid"];
			$title = $value["title"];
			$studentusername = $value["studentusername"];
			
			$sql2 = "SELECT firstname, lastname, degree, skills FROM studentprofile WHERE username = \"$studentusername\" ";
			$stmt2 = $conn->prepare( $sql2 );
			$stmt2->execute();

			$result = $stmt2->setFetchMode ( PDO::FETCH_ASSOC );
			$arr2 = $stmt2->fetchAll();

			$studentfirstname = $arr2[0]["firstname"];
			$studentlastname = $arr2[0]["lastname"];
			$studentskills = $arr2[0]["skills"];
			$studentdegree = $arr2[0]["degree"];

			echo '<div><h2>'; echo $title; echo '</h2>';
			echo '<h4>Student : '; echo $studentfirstname." ".$studentlastname;

			echo '	<form method = "POST" action = "profileapplied.php" >';
			echo '<input type = "hidden" name = "studentuname" value = "'; echo $studentusername;  echo '" /> ';
			echo '<input type = "submit" name = "submit" value = "View Profile" />';
			echo '</form>';

			echo '<br />Qualification : '; echo $studentdegree;
			echo '<br />Skills : '; echo $studentskills; echo '</h4>';

			echo '<form method = "POST" action = " '; echo htmlspecialchars ( $_SERVER["PHP_SELF"] ); echo ' " >';
			echo '<input type = "hidden" name = "internid" value = " '; echo $internid; echo ' " />';
			echo '<input type = "hidden" name = "studentusername" value = " '; echo $studentusername; echo ' " />';
			echo '<input type = "submit" name = "Ignore" value = "Ignore" />';
			echo '						';
			echo '<input type = "submit" name = "Invite" value = "Invite" />';
			echo '</form>';
			echo '</div>';
		 }
	 }
	catch ( PDOException $e ) {
		//rollback
		$conn->rollback();
		//echo $e->getMessage();
	 }
 }

if ( $status === true ) {
	echo '<script>document.getElementById("message").innerHTML = "Done Successfully"</script>';
 }

?>

</body>

</html>
