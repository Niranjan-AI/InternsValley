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

<html>

<head>

<title>Profile - <?php echo $uname; ?> - Edit</title>
<meta charset = "utf-8" />
<meta name = "keywords" content = "Interns Valley" />
<meta name = "description" content = "Student Edit Page" />
<meta name = "viewport" content = "width = device-width, initial-scale = 1.0" />

<link rel = "stylesheet" type = "text/css" href = "headernav.css" />
<link rel = "stylesheet" type = "text/css" href = "font.css" />
<link rel = "stylesheet" type = "text/css" href = "input.css" />
<link rel = "stylesheet" type = "text/css" href = "content.css" />

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<body>

<?php
$pwd=$npwd=$cpwd=$status="";
$pwderr=$cpwderr="";

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
	$pwd = test_input( $_POST["password"] );
	$npwd = test_input( $_POST["newpassword"] );
	$cpwd = test_input( $_POST["cpassword"] );

	if($npwd != $cpwd) {
		$cpwderr="Incorrect Password. Please Retype Password same as above.";
	 }

	if ( $cpwderr == "" ) {
		$servername = "localhost";
		$username = "root";
		$password = "";
		$database="webinternship";
		//$database="demowebinternship";

		try {
			$conn = new PDO("mysql:host = $servername;dbname = $database", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
			//begin transaction
			$conn->beginTransaction();

			$sql = "USE $database";
			$conn->exec($sql);

			$stmt=$conn->prepare("SELECT username, password FROM studentlogin where username = \"$uname\" ");
			$stmt->execute();
		
			//Setting to FetchMode
			$result=$stmt->setFetchMode(PDO::FETCH_ASSOC);

			//Fetching in the form of Array
			$arr = $stmt->fetchAll();

			if ( $arr[0]["password"] != $pwd ) {
				$pwderr = "Wrong Password";
			 }

			if( $pwderr == "" ) {
				$stmt = "UPDATE studentlogin
					SET password = \"$npwd\"
					WHERE username = \"$uname\" ";
				$conn -> exec( $stmt );
				$status = true;
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
 }

?>

<h2 style = "font-size : 150%; color : orange;">CHANGE PASSWORD</h2>
<div style = "font-size : 150%">

<span>* required</span><br /><br />

<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" > <!--Submitting form to the same page-->

Username : <input type = "text" name = "username" placeholder = "Username" value = "<?php echo $uname; ?>" required  autocomplete ="off" disabled/><span>  *</span><br /><br />

Current Password : <input type = "password" name = "password" placeholder = "Current Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters" value = "<?php echo $pwd; ?>" required <?php if ( $status === true ) : echo "disabled"; endif; ?>/><span>  *<?php echo $pwderr;?></span><br /><br /> <!--(?= refer to metacharacter that does not store the matched pattern in any of the buffers-->

New Password : <input type = "password" name = "newpassword" placeholder = "New Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters" value = "<?php echo $npwd; ?>" required <?php if ( $status === true ) : echo "disabled"; endif; ?> /><span>  * </span><br /><br /> <!--(?= refer to metacharacter that does not store the matched pattern in any of the buffers-->

Confirm Password : <input type = "password" name = "cpassword" placeholder = "Confirm Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Please re-enter the same password" value = "<?php echo $cpwd; ?>" required <?php if ( $status === true ) : echo "disabled"; endif; ?> /><span>  *<?php echo $cpwderr;?></span><br /><br />
<input type = "Submit" value = "Submit" <?php if ( $status === true ) : echo "disabled"; endif; ?> />

</form>

<h2 id = "status" ></h2>

</div>

<?php
if ( $status === true ) {
	echo "<script>document.getElementById(\"status\").innerHTML = \"Password Changed Successfully\"</script>";
 }
?>

</body>

</html>