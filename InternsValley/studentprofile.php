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

<title>Profile - <?php echo $uname; ?> - Interns Valley</title>
<meta charset = "utf-8" />
<meta name = "keywords" content = "Interns Valley" />
<meta name = "description" content = "Student Profile Page" />
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
</style>

<!--<script>
function logout () {
	<?php //session_unset(); ?>
	window.location.href = "studentlogin.php";
 }
</script>-->

<script>
function logout () {
	window.location.href = "studentlogout.php";
 }

function edit() {
	window.location.href = "studentedit.php";
 }
</script>

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<nav>

<ul>
<li><a href = "studentinternships.php" >Find Internships</a></li>
<li><a href = "studentappliedinternships.php" >Applied Internships</a></li>
<li><button type = "button" onclick = logout() >Logout</button></li>
</ul>

</nav>

<body>

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
		$sql1 = "SELECT * from studentprofile where username = \"$uname\"";
		$sql2 = "SELECT email from studentlogin where username = \"$uname\" ";

		//begin Transaction
		$conn->beginTransaction();

		$conn->exec($sql);
		$stmt=$conn-> prepare ( $sql1 );
		$stmt->execute();

		$result = $stmt->setFetchMode( PDO::FETCH_ASSOC );
		$arr = $stmt -> fetchAll();

		$firstname = strtoupper ( $arr[0]["firstname"] );
		$lastname = strtoupper ( $arr[0]["lastname"] );
		$gender = $arr[0]["gender"];
		$degreeab = $arr[0]["degree"];
		$college = $arr[0]["college"];
		$fieldofstudy = $arr[0]["fieldofstudy"];
		$fromyear = $arr[0]["fromyear"];
		$toyear = $arr[0]["toyear"];
		$skills = $arr[0]["skills"];
		$photo = $arr[0]["photo"]; //$photo is name of photo
		$state = ucwords ( $arr[0]["state"] );
		$city = ucwords ( $arr[0]["city"] );
		$phone = $arr[0]["phone"];

		switch ( $degreeab ) {
			case "B.E" : $degree = "Bachelor of Engineering ( B.E )";
				   break;
			case "M.Tech" : $degree = "Master of Technology ( M.Tech )";
				             break;
			case "B.C.A" : $degree = "Bachelor of Computer Applications ( B.C.A )";
				   break;
			case "M.C.A" : $degree = "Master of Computer Applications ( M.C.A )";
				             break;
			case "B.B.A" : $degree = "Bachelor of Business Administration ( B.B.A )";
				   break;
			case "M.B.A" : $degree = "Master of Business Administration ( M.B.A )";
				             break;
			case "B.Sc" : $degree = "Bachelor of Science ( B.Sc )";
				   break;
			case "M.Sc" : $degree = "Master of Science ( M.Sc )";
				             break;
			case "B.Com" : $degree = "Bachelor of Commerce ( B.Com )";
				   break;
			case "M.Com" : $degree = "Master of Commerce ( M.Com )";
				             break;
			case "B.A" : $degree = "Bachelor of Arts ( B.A )";
				   break;
			case "M.A" : $degree = "Master of Arts ( M.A )";
				             break;
		 }

		if ( empty ( $photo ) ) {
			$photo = "default.png";
		 }
		//echo $photo."<br />";
		//echo $firstname."<br />";
		
		//$skillsarr = preg_split( "/,/" , "$skills", -1);
		//var_dump( $skillsarr );

		$stmt = $conn->prepare ( $sql2 );
		$stmt -> execute();

		$arr = $stmt->fetchAll();
		$email = $arr[0]["email"];

		//echo $firstname."<br />".$lastname."<br />".$gender."<br />".$degree."<br />".$college."<br />".$fieldofstudy."<br />".$fromyear."<br />".$toyear."<br />".$skills."<br />".$photo."<br />".$state."<br />".$city."<br />".$phone."<br />".$email;

		//commit
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

<div>
<img src = "upload/<?php echo $photo; ?>" style = "width : 250px; height : 250px; border-radius : 50%; "  /><br />
<button type = "button" onclick = edit() >Edit</button>
</div>
<div>
<h2 style = "font-size : 200%; color : orange; " ><?php echo $firstname."  ".$lastname; ?><br /></h2>
<h4><p><?php if ( !empty ( $city ) ) {
	echo $city." , ";
 }
if( !empty ( $state ) ) {
	echo $state."<br />";
 } ?>
<?php echo $gender; ?></p></h4>
</div>

<div>
<h2 style = "color : orange; font-size : 150%">Education</h2>
<h4>Degree : <?php echo $degree; ?></h4>
<h4>College : <?php echo $college; ?></h4>
<h4><?php if (! empty ( $fieldofstudy ) ) {
	echo "Filed of Study : $fieldofstudy";
 } ?></h4>
<h4><?php if (!empty ( $fromyear ) && $fromyear != "0000" ) {
	echo "From : $fromyear";
		if (! empty ( $toyear )  && $toyear != "0000") {
			echo "	To : $toyear";
		 }
 } ?></h4>
</div>

<div>
<h2 style = "color : orange; font-size : 150%">Skills</h2>
<h4><?php echo $skills; ?></h4>
</div>

<div>
<h2 style = "color : orange; font-size : 150%">Contact Information</h2>
<h4><?php if (!empty ( $phone ) && $phone != "0" ) {
	echo "Phone : $phone";
 } ?></h4>
<h4>Email : <?php echo $email; ?></h4>
</div>

</body>

</html>