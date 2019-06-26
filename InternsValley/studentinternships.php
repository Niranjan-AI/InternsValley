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

<title>Profile - <?php echo $uname; ?> - Internships</title>
<meta charset = "utf-8" />
<meta name = "keywords" content = "Interns Valley" />
<meta name = "description" content = "Student Internship Page" />
<meta name = "viewport" content = "width = device-width, initial-scale = 1.0" />

<link rel = "stylesheet" type = "text/css" href = "headernav.css" />
<link rel = "stylesheet" type = "text/css" href = "font.css" />
<link rel = "stylesheet" type = "text/css" href = "input.css" />
<link rel = "stylesheet" type = "text/css" href = "content.css" />
<link rel = "stylesheet" type = "text/css" href = "div.css" />

<script>
function logout () {
	window.location.href = "studentlogout.php";
 }
</script>

<style>
li { 
	display : inline;
	padding : 15px;
 }

h2,h3 {
	font-size : 150%;
	color : orange;
 }
</style>

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<nav>

<ul>
<li><a href = "studentprofile.php">Home</a>
<li><a href = "#" >Find Internships</a></li>
<li><a href = "studentappliedinternships.php" >Applied Internships</a></li>
<li><button type = "button" onclick = logout() >Logout</button></li>
</ul>

</nav>

<body>

<?php

$applyerr = "";
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
 }
	
if ( isset ( $_POST["Apply"] ) ) {

	$internidpost = test_input ( $_POST["internid"] );
	$titlepost = test_input ( $_POST["title"] );
	$companyunamepost = test_input ( $_POST["company"] );

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
		$sql1 = "SELECT studentusername FROM appliedinternships where internid = \"$internidpost\" ";
		$sql2 = "INSERT INTO appliedinternships ( internid, title, companyusername, studentusername )
			values ( $internidpost, \"$titlepost\", \"$companyunamepost\", \"$uname\" )";

		//begin Transaction
		$conn->beginTransaction();

		$conn->exec( $sql );
		$stmt = $conn->prepare( $sql1 );
		$stmt->execute();

		$result = $stmt -> setFetchMode( PDO::FETCH_ASSOC );
		$arr = $stmt -> fetchAll();

		foreach ( $arr as $value ) {
			if ( $value["studentusername"] == $uname ) {
				$applyerr = "Already Applied";
				break;
			 }
		 }

		if ( $applyerr == "" ) {
			$conn ->exec ( $sql2 );
			$applyerr = "Applied Successfully";
		 }

		$conn->commit();
		$conn = null;

		//echo "Applied Successfully";
	}
	catch (PDOException $e ) {
		//rollback
		$conn->rollback();
		//echo $e->getMessage();

		//echo "You have Already Applied";
	 }
 }
?>

<h3><?php echo $applyerr; ?></h3>

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
		$sql1 = "SELECT skills, degree, fieldofstudy FROM studentprofile where username = \"$uname\" ";
		$sql2 = "SELECT * FROM internships";

		//begin Transaction
		$conn->beginTransaction();

		$conn->exec($sql);
		$stmt1 =$conn-> prepare ( $sql1 );
		$stmt1->execute();

		$result = $stmt1 -> setFetchMode( PDO::FETCH_ASSOC );
		$arr = $stmt1 -> fetchAll();

		$studentdegree = $arr[0]["degree"];
		$studentfield = $arr[0]["fieldofstudy"];
		$studentskills = $arr[0]["skills"];
		$studentskillsarr = preg_split( "/,/" , "$studentskills", -1);

		$stmt2 = $conn-> prepare ( $sql2 );
		$stmt2 ->execute();

		$result = $stmt2->setFetchMode( PDO::FETCH_ASSOC );
		$arr = $stmt2->fetchAll();

		//print_r($arr);

		foreach ( $arr as $value ) {  //for every company

			//print_r($value);
			$status = false;
			$companyskills = preg_split ( "/,/", "$value[skills]", -1);
			//based on skills
			foreach ( $studentskillsarr as $val1 ) {   //for every skill of student

				foreach( $companyskills as $val2 ) {

					if ( $val1 == $val2 ) {

						$status = true;
						$internid = $value["internid"];
						$companyuname = $value["username"];
						$title = $value["title"];
						$degree = $value["degree"];
						$field = $value["field"];
						$stipend = $value["stipend"];
						$lastdate = $value["lastdate"];
						$duration = $value["duration"];
						$startdate = $value["startdate"];
						$description = $value["description"];

						$lastdatearr = preg_split ( "/-/", "$lastdate", -1 );
						$startdatearr = preg_split ( "/-/", "$startdate", -1 );//var_dump($startdatearr);

						$sql3 = "SELECT companyname FROM companyprofile where username = \"$companyuname\" ";
						$stmt3 = $conn->prepare( $sql3 );
						$stmt3->execute();
						$result = $stmt3->setFetchMode( PDO::FETCH_ASSOC );
						$arr3 = $stmt3->fetchAll();

						$companyname = $arr3[0]["companyname"];

						echo '<div><h2>'; echo $title; 
						echo '</h2><h2>Company : '; echo $companyname;

						echo '<form method = "POST" action = "profileinternships.php" >';
						echo '<input type = "hidden" name = "companyuname" value = "'; echo$companyuname;  echo '" /> ';
						echo '<input type = "submit" name = "submit" value = "View Profile" />';
						echo '</form>';

						echo '</h2><h4>Skills : ';
						foreach( $companyskills as $val3 ) { echo $val3; echo '  '; }
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
						echo '<input type = "hidden" name = "title" value = " ';echo $title; echo ' " />';
						echo '<input type = "hidden" name = "company" value = " ';echo $companyuname; echo ' " />';
						echo '<input type = "submit" name = "Apply" value = "Apply" />';
						echo '</form>';
						echo '</div>';
						

					 } //end of if
					if ( $status === true ) { break; } //break works for for each inner

				 } //end of for each inner
				//if ( $status === true ) { break; } //break works for for each middle

			 } // end of foreach middle

			//Based on Degree
			if ( $studentdegree == $value["degree"] ) {
				if ( $status === false ) {  //if company not shown
					$status = true;
					$internid = $value["internid"];
					$companyuname = $value["username"];
					$title = $value["title"];
					$degree = $value["degree"];
					$field = $value["field"];
					$stipend = $value["stipend"];
					$lastdate = $value["lastdate"];
					$duration = $value["duration"];
					$startdate = $value["startdate"];
					$description = $value["description"];

					$lastdatearr = preg_split ( "/-/", "$lastdate", -1 );
					$startdatearr = preg_split ( "/-/", "$startdate", -1 );//var_dump($startdatearr);

					$sql3 = "SELECT companyname FROM companyprofile where username = \"$companyuname\" ";
					$stmt3 = $conn->prepare( $sql3 );
					$stmt3->execute();
					$result = $stmt3->setFetchMode( PDO::FETCH_ASSOC );
					$arr3 = $stmt3->fetchAll();

					$companyname = $arr3[0]["companyname"];

					echo '<div><h2>'; echo $title; 
					echo '</h2><h2>Company : '; echo $companyname;
					
					echo '<form method = "POST" action = "profileinternships.php" >';
					echo '<input type = "hidden" name = "companyuname" value = "'; echo$companyuname;  echo '" /> ';
					echo '<input type = "submit" name = "submit" value = "View Profile" />';
					echo '</form>';

					echo '</h2><h4>Skills : ';
					foreach( $companyskills as $val3 ) { echo $val3; echo '  '; }
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
					echo '<input type = "hidden" name = "internid" value = " ';echo $internid; echo ' " />';							echo '<input type = "hidden" name = "title" value = " ';echo $title; echo ' " />';
					echo '<input type = "hidden" name = "company" value = " ';echo $companyuname; echo ' " />';
					echo '<input type = "submit" name = "Apply" value = "Apply" />';
					echo '</form>';
					echo '</div>';
					
				 } // end of inner if	
		 	 } // end of outer if

			//Based on Field Of study
			if ( $studentfield != "" && $value["field"] != "" ) {  // if field of study is not empty in both cases

				if ( $studentfield == $value["field"] ) { //if field of study are same

					if ( $status === false ) {  // if company not shown already

						$status = true;
						$internid = $value["internid"];
						$companyuname = $value["username"];
						$title = $value["title"];
						$degree = $value["degree"];
						$field = $value["field"];
						$stipend = $value["stipend"];
						$lastdate = $value["lastdate"];
						$duration = $value["duration"];
						$startdate = $value["startdate"];
						$description = $value["description"];

						$lastdatearr = preg_split ( "/-/", "$lastdate", -1 );
						$startdatearr = preg_split ( "/-/", "$startdate", -1 );

						$sql3 = "SELECT companyname FROM companyprofile where username = \"$companyuname\" ";
						$stmt3 = $conn->prepare( $sql3 );
						$stmt3->execute();
						$result = $stmt3->setFetchMode( PDO::FETCH_ASSOC );
						$arr3 = $stmt3->fetchAll();

						$companyname = $arr3[0]["companyname"];

						echo '<div><h2>'; echo $title; 
						echo '</h2><h2>Company : '; echo $companyname;
						
						echo '<form method = "POST" action = "profileinternships.php" >';
						echo '<input type = "hidden" name = "companyuname" value = "'; echo$companyuname;  echo '" /> ';
						echo '<input type = "submit" name = "submit" value = "View Profile" />';
						echo '</form>';

						echo '</h2><h4>Skills : ';
						foreach( $companyskills as $val3 ) { echo $val3; echo '  '; }
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
						echo '<input type = "hidden" name = "title" value = " ';echo $title; echo ' " />';
						echo '<input type = "hidden" name = "company" value = " ';echo $companyuname; echo ' " />';
						echo '<input type = "submit" name = "Apply" value = "Apply" />';
						echo '</form>';
						echo '</div>';
						
					 } //end of inner if 
				 } // end of middle if
			 } // end of outer if

		 } //end of for each outer
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