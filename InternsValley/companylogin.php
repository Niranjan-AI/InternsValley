<?php
//Starting Session
session_start();
?>

<!doctype html>

<html>

<head>
	
<meta charset = "UTF-8" />
<meta name = "description" content = "Company Login Page for InternsValley" />
<meta name = "keywords" content = "Company Login Page, Interns Valley" />
<meta name = "viewport" content = "width=device-width, initial-scale = 1.0" />

<title>Company Login Page</title>

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
div {
	font-size : 120%;
 }
</style>

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<nav>

<ul>
<li><a href = "internsvalley.htm" >Home</a></li>
<li><a href = "studentlogin.php" >Student Login</a></li>
<li><a href = "#" >Company Login</a></li>
</ul>

</nav>

<body>

<?php
$uname=$pwd=$status="";
$unameerr=$pwderr="";

//function to trim data
function test_input ( $data ) {
	$data = trim ( $data );
	$data = stripslashes ( $data );
	$data = htmlspecialchars ( $data );
	return $data;
 }

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
	
	$uname = test_input( $_POST["username"]);
	$pwd = test_input( $_POST["password"]);

	//Connecting To Server
	$servername = "localhost";
	$username = "root";
	$password = "";
	$database = "webinternship";
	//$database = "demowebinternship";

	try {
		$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
		$conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		//echo "Connected";

		//begin Transaction to execute multiple statements
		$conn->beginTransaction();
		
		$sql = "USE $database";
		$conn->exec($sql);
		
		$stmt = $conn->prepare("SELECT username, password FROM companylogin");
		$stmt->execute();

		$result = $stmt->setFetchMode( PDO::FETCH_ASSOC );
		//echo $result;
		$arr = $stmt->fetchAll();
		//var_dump($arr);

		foreach ( $arr as $value ) {
			if ( $value["username"] == $uname ) {
				if ( $value["password"] == $pwd ) {
					$status = true;
				 }
				else {
					$status = false;
					$unameerr = "";
					$pwderr = "Incorrect Password";
				 }
				break;
			 }
			else {
				$status = false;
				$unameerr = "Username Doesn't Exists";
			 }
		 }

		//Commit Transaction
		$conn->commit();

		$conn = null;
	 }
	catch (PDOException $e ) {
		$conn->rollback();
		//echo $e->getMessage();
	 }
 }
?>

<h2>COMPANY LOGIN PAGE</h2>

<div>
<span>* required</span><br /><br />

<form method = "POST" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" >

<label for = "username">Username</label>
<input type = "text" id = "username" name = "username" placeholder = "Username" value = "<?php echo $uname; ?>" required autocomplete = "off" /><span> * <?php echo $unameerr; ?></span><br /><br />

<label for = "password">Password</label>
<input type = "password" id = "password" name = "password" placeholder = "Password" value = "<?php echo $pwd; ?>" required autocomplete = "off" /><span> * <?php echo $pwderr; ?></span><br /><br />

<input type = "Submit" value = "Submit" /><br /><br />

</form>

<?php
if ( $status === true ) {
	//Recording username
	$_SESSION["companyusername"] = $uname;
	$_SESSION["companylogin"] = "active";

	//echo "<script>window.location.href = \"companyprofile.php\"</script>";
	
	//redirecting page with contents saved
	header( 'Location: https://localhost/Webproject/companyprofile.php' );
	exit();
 }
?>

<p><a href = "companyregistration.php" >New User?</a></p>
</div>

</body>

</html>