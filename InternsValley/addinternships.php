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
<meta name = "description" content = "Company Add Internship Page" />
<meta name = "viewport" content = "width = device-width, initial-scale = 1.0" />

<link rel = "stylesheet" type = "text/css" href = "headernav.css" />
<link rel = "stylesheet" type = "text/css" href = "font.css" />
<link rel = "stylesheet" type = "text/css" href = "input.css" />
<link rel = "stylesheet" type = "text/css" href = "content.css" />

<style>
li {
	display : inline;
	padding : 15px;
}
h2 {
	color : orange;
	font-size : 150%;
 }
label {
	color : orange;
	font-size : 110%;
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

<!--
<nav>

<ul>
<li><a href = "companyprofile.php" >Home</a></li>
<li><a href = "#" >Manage Internships</a></li>
<li><a href = "appliedinternships.php" >View Applied Internships</a></li>
<li><a href = "invitedapplications.php" >Invited Applicants</a></li>
<li><button type = "button" onclick = logout() >Logout</button></li>
</ul>

</nav>-->

<form method = "POST" action = "companymanage.php" >
<input type = "Submit" value = "Back" />
</form>

<body>

<?php
$title=$degree=$field=$skills=$stipend=$lastdate=$lastmonth=$lastyear=$duration=$startdate=$startmonth=$startyear=$description="";
$status="";

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
 }

if ( isset ( $_POST["Submit"] ) ) {
	$title = test_input($_POST["title"]);
	$degree = test_input($_POST["degree"]);
	$field = test_input($_POST["field"]);
	$skills = test_input($_POST["skills"]);
	$stipend = test_input($_POST["stipend"]);
	$lastdate = test_input($_POST["lastdate"]);
	$lastmonth = test_input ($_POST["lastmonth"] );
	$lastyear = test_input( $_POST["lastyear"] );
	$duration = test_input( $_POST["duration"]);
	$startdate = test_input( $_POST["startdate"]);
	$startmonth = test_input($_POST["startmonth"]);
	$startyear = test_input( $_POST["startyear"] );
	$description = test_input ( $_POST["description"] );

	$lastdatecomplete = $lastyear."-".$lastmonth."-".$lastdate;
	$startdatecomplete = $startyear."-".$startmonth."-".$startdate;

	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "webinternship";
	//$database = "demowebinternship";

	try {
		$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
		$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		$sql = "USE $database";
		$sql1 = "INSERT into internships ( title, username, degree, field, skills, stipend, lastdate, duration, startdate, description )
			values ( \"$title\", \"$uname\", \"$degree\", \"$field\", \"$skills\", \"$stipend\", \"$lastdatecomplete\", \"$duration\", \"$startdatecomplete\", \"$description\" ) ";

		$conn->beginTransaction();

		$conn->exec( $sql );
		$conn->exec ( $sql1 );
		$status = true;

		$conn->commit();
		$conn = null;
	 }
	catch ( PDOException $e ) {
		$conn->rollback();
		//echo $e->getMessage();
	 }

 }
?>

<h2>ADD AN INTERNSHIP</h2>

<div>
<span>* required</span><br /><br />

<form method = "POST" action = "<?php echo htmlspecialchars( $_SERVER["PHP_SELF"] ); ?>" >

<label>Title</label><br />
<input type = "text" name = "title" value = "<?php echo $title; ?>" placeholder = "Title" required autocomplete = "off" /><span> * </span><br /><br />

<label>Expected Qualification</label><br />
<select name = "degree" value = "<?php echo $degree; ?>" required >
<option value = "" >--Select--</option>
<option value = "B.E" <?php if (isset($degree) && $degree == "B.E" ) echo "selected"; ?> >Bachelor of Engineering(B.E/B.Tech)</option>
<option value = "M.Tech" <?php if (isset($degree) && $degree == "M.Tech" ) echo "selected"; ?> >Master of Technology(M.Tech)</option>
<option value = "B.C.A" <?php if (isset($degree) && $degree == "B.C.A" ) echo "selected"; ?> >Bachelor of Computer Applications(B.C.A)</option>
 <option value = "M.C.A" <?php if (isset($degree) && $degree == "M.C.A" ) echo "selected"; ?> >Master of Computer Applications(M.C.A)</option>
<option value = "B.B.A" <?php if (isset($degree) && $degree == "B.B.A" ) echo "selected"; ?> >Bachelor of Business Administration(B.B.A)</option>
 <option value = "M.B.A" <?php if (isset($degree) && $degree == "M.B.A" ) echo "selected"; ?> >Master of Business Administration(M.B.A)</option>
<option value = "B.Sc" <?php if (isset($degree) && $degree == "B.Sc" ) echo "selected"; ?> >Bachelor of Science(B.Sc)</option>
<option value = "M.Sc" <?php if (isset($degree) && $degree == "M.Sc" ) echo "selected"; ?> >Master of Science(M.Sc)</option>
<option value = "B.Com" <?php if (isset($degree) && $degree == "B.Com" ) echo "selected"; ?> >Bachelor of Commerce(B.Com)</option>
 <option value = "M.Com" <?php if (isset($degree) && $degree == "M.Com" ) echo "selected"; ?> >Master of Commerce(M.Com)</option>
<option value = "B.A" <?php if (isset($degree) && $degree == "B.A" ) echo "selected"; ?> >Bachelor of Arts(B.A)</option>
<option value = "M.A" <?php if (isset($degree) && $degree == "M.A" ) echo "selected"; ?> >Master of Arts(M.A)</option>
</select><br /><br /><br />

<label>Field of Study</label><br /><br />
<input type = "text" name = "field" value = "<?php echo $field; ?>" placeholder = "Field of Study" autocomplete = "off" /><span></span><br /><br />

<label>Expected Skills</label><br />
<input type = "text" name = "skills" value = "<?php echo $skills; ?>" placeholder = "Skills" required autocomplete = "off" /><span> * </span><br /><br />

<label>Stipend Offered</label><br />
<input type = "text" name = "stipend" value = "<?php echo $stipend; ?>" placeholder = "Stipend" autocomplete = "off" /><span></span><br /><br />

<label>Last Date to Apply</label><br />
<input type = "text" name = "lastdate" value = "<?php echo $lastdate; ?>" placeholder = "Date" pattern = "[0-9]{2}" title = "Proper Date Format" required autocomplete = "off" /><span> * </span>
<input type = "text" name = "lastmonth" value = "<?php echo $lastmonth; ?>" placeholder = "Month" pattern = "[0-9]{2}" title = "Proper Month Format" required autocomplete = "off" /><span> * </span>
<input type = "text" name = "lastyear" value = "<?php echo $lastyear; ?>" placeholder = "Year" pattern = "[0-9]{4}" title = "Proper Year Format" required autocomplete = "off" /><span> * </span><br /><br />

<label>Duration of Internship</label><br />
<input type = "text" name = "duration" value = "<?php echo $duration; ?>" placeholder = "Duration" required autocomplete = "off" /><span> * </span><br /><br />

<label>Start Date of Internship</label><br />
<input type = "text" name = "startdate" value = "<?php echo $startdate; ?>" placeholder = "Date" pattern = "[0-9]{2}" title = "Proper Date Format" required autocomplete = "off" /><span> * </span>
<input type = "text" name = "startmonth" value = "<?php echo $startmonth; ?>" placeholder = "Month" pattern = "[0-9]{2}" title = "Proper Month Format" required autocomplete = "off" /><span> * </span>
<input type = "text" name = "startyear" value = "<?php echo $startyear; ?>" placeholder = "Year" pattern = "[0-9]{4}" title = "Proper Year Format" required autocomplete = "off" /><span> * </span><br /><br />

<label>Description</label><br />
<textarea rows = "5", cols = "50", name = "description" placeholder = "Description About Course" autocomplete = "off" /><?php echo $description; ?></textarea><span></span><br /><br />

<input type = "Submit" name = "Submit" value = "Submit" />
</form>

</div>

<?php
if ($status === true ) {
	echo '<script>window.location.href = "companymanage.php" </script>';
 }
?>

</body>

</html>