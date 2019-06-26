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
<meta name = "description" content = "Company Profile Page" />
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
<li><a href = "#" >Manage Internships</a></li>
<li><a href = "appliedinternships.php" >View Applied Internships</a></li>
<li><a href = "invitedapplications.php" >Invited Applicants</a></li>
<li><button type = "button" onclick = logout() >Logout</button></li>
</ul>

</nav>

<form method = "POST" action = "addinternships.php" >
<input type = "Submit" value = "ADD Internship" />
</form>

<body>

<h2>POSTED INTERNSHIPS</h2>

<?php

if ( isset ( $_POST["delete"] ) ) {
	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "webinternship";
	//$database = "demowebinternship";

	$internidpost = $_POST["internid"];

	try {
		//Connecting to server
		$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
		$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$sql = "USE $database";
		$sql1 = "DELETE FROM internships WHERE username = \"$uname\" and internid = \"$internidpost\" ";
		
		$conn->beginTransaction();
		$conn->exec( $sql );
		$conn->exec($sql1);

		//commit
		$conn->commit();
		$conn = null;
					
	 } // end of try
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
		$sql1 = "SELECT * FROM internships WHERE username = \"$uname\" ";

		$conn->beginTransaction();
		$conn->exec( $sql );

		$stmt = $conn->prepare ( $sql1 );
		$stmt->execute();

		$result = $stmt->setFetchMode( PDO::FETCH_ASSOC );
		$arr = $stmt -> fetchAll();

		foreach ( $arr as $value ) {
			$internid = $value["internid"];
			$title = $value["title"];
			$degree = $value["degree"];
			$field = $value["field"];
			$skills = $value["skills"];
			$stipend = $value["stipend"];
			$lastdate = $value["lastdate"];
			$duration = $value["duration"];
			$startdate = $value["startdate"];
			$description = $value["description"];

			$lastdatearr = preg_split ( "/-/", "$lastdate", -1 );
			$startdatearr = preg_split ( "/-/", "$startdate", -1 );

			echo '<div><h2>'; echo $title; 
			echo '</h2><h4>Skills : '; echo $skills;
			echo '<br />Qualification : '; echo $degree; echo '<br />';
			if ( ! empty ( $field ) ) { echo 'Field of Study : '; echo $field; echo '<br />'; }
			echo 'Last Date to apply : ';echo $lastdatearr[2]; echo '-';
			echo $lastdatearr[1]; echo '-'; echo $lastdatearr[0]; echo '<br />';
			if ( ! empty ( $stipend ) ) { echo 'Stipend : '; echo $stipend; echo '<br />'; }
			echo 'Duration : '; echo $duration;
			echo '<br />Start Date : ';echo $startdatearr[2]; echo '-';echo $startdatearr[1];
			echo '-'; echo $startdatearr[0]; echo '<br />';
			if ( ! empty ( $description ) ) { echo 'Description : '; echo $description; }
			echo '</h4>';

			echo '<form action = " '; echo htmlspecialchars( $_SERVER["PHP_SELF"] );
			echo  ' "method = "POST" >';
			echo '<input type = "hidden" name = "internid" value = " ';echo $internid; echo ' " />';
			echo '<input type = "submit" name = "delete" value = "Delete" />';
			echo '</form>';
			echo '</div>';
		 }
		//commit
		$conn->commit();
		$conn = null;
					
	 } // end of try
	catch ( PDOException $e ) {
		//rollback
		$conn->rollback();
		//echo $e->getMessage();
	 }

 }
?>

</body>

</html>