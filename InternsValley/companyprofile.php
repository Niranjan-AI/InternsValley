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

<!--<script>
function logout () {
	<?php //session_unset(); ?>
	window.location.href = "companylogin.php";
 }
</script>-->

<script>
function logout () {
	window.location.href = "companylogout.php";
 }

function edit() {
	window.location.href = "companyedit.php";
 }
</script>

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<nav>

<ul>
<li><a href = "companymanage.php" >Manage Internships</a></li>
<li><a href = "appliedinternships.php" >View Applied Internships</a></li>
<li><a href = "invitedapplications.php" >Invited Applicants</a></li>
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
		$sql1 = "SELECT * from companyprofile where username = \"$uname\"";
		$sql2 = "SELECT email from companylogin where username = \"$uname\" ";

		//begin Transaction
		$conn->beginTransaction();

		$conn->exec($sql);
		$stmt=$conn-> prepare ( $sql1 );
		$stmt->execute();

		$result = $stmt->setFetchMode( PDO::FETCH_ASSOC );
		$arr = $stmt -> fetchAll();

		$companyname = strtoupper ( $arr[0]["companyname"] );
		$website =  $arr[0]["website"] ;
		$lineofbusiness = $arr[0]["lineofbusiness"];
		$established = $arr[0]["established"];
		$addlocation = $arr[0]["addlocation"];
		$addcity = ucwords ( $arr[0]["addcity"] );
		$addstate = ucwords ( $arr[0]["addstate"] );
		$hqlocation = $arr[0]["hqlocation"];
		$hqcity = ucwords ( $arr[0]["hqcity"] );
		$hqstate = ucwords ( $arr[0]["hqstate"] );
		$phone = $arr[0]["phone"] ;
		$logo = $arr[0]["logo"];
		$description = $arr[0]["description"];

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
<img src = "upload/<?php echo $logo; ?>" style = "width : 250px; height : 250px; border-radius : 50%" /><br />
<button type = "button" onclick = edit() >Edit</button>
</div>

<div>
<h2><?php echo $companyname; ?><br /></h2>
<h3><p><?php echo $addlocation; ?><br />
<?php echo $addcity." , ".$addstate; ?><p></h3>
<h4><?php echo $phone; ?></h4>
<h4><?php echo $email; ?></h4>
</div>

<div>
<h2>Company Information</h2>
<h3>Website : <?php echo $website; ?></h3>
<h3>Line of Business : <?php echo $lineofbusiness; ?></h3>
<h3>Established : <?php echo $established; ?></h3>
<h2>Headquarters</h2>
<h3><p><?php echo $hqlocation; ?><br />
<?php echo $hqcity." , ".$hqstate; ?><p></h3>
</div>

<div><?php if ( $description != "" ) {
	echo '<h2>Description </h2>';
	echo '<div><h3>'; echo $description; echo '</h3></div>';
 } ?>
</div>

</body>

</html>