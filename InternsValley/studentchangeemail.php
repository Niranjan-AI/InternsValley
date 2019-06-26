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
$pwd=$email=$newemail=$status="";
$emailerr=$pwderr=$newemailerr="";

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
	$pwd = test_input( $_POST["password"] );
	$email = test_input( $_POST["email"] );
	$newemail = test_input( $_POST["newemail"] );

	if(! filter_var( $newemail, FILTER_VALIDATE_EMAIL ) ) {
		$newemailerr="Invalid Email Format";
	 }

	if ( $newemailerr == "" ) {
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

			$stmt=$conn->prepare("SELECT email, password FROM studentlogin where username = \"$uname\" ");
			$stmt->execute();
		
			//Setting to FetchMode
			$result=$stmt->setFetchMode(PDO::FETCH_ASSOC);

			//Fetching in the form of Array
			$arr = $stmt->fetchAll();

			if ( $arr[0]["password"] != $pwd ) {
				$pwderr = "Wrong Password";
			 }

			if ( $arr[0]["email"] != $email ) {
				$emailerr = "Wrong Email Id";
			 }

			if( $pwderr == "" && $emailerr == "" ) {
				$stmt = "UPDATE studentlogin
					SET email = \"$newemail\"
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
			//$newemailerr = "Email ID already Exists";
		 }
	 }
 }

?>

<h2 style = "font-size : 150%; color : orange;">CHANGE EMAIL</h2>
<div style = "font-size : 150%;">

<span>* required</span><br /><br />

<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" > <!--Submitting form to the same page-->

Username : <input type = "text" name = "username" placeholder = "Username" value = "<?php echo $uname; ?>" required  autocomplete ="off" disabled/><span>  *</span><br /><br />

Password : <input type = "password" name = "password" placeholder = "Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters" value = "<?php echo $pwd; ?>" required <?php if ( $status === true ) : echo "disabled"; endif; ?>/><span>  *<?php echo $pwderr;?></span><br /><br /> <!--(?= refer to metacharacter that does not store the matched pattern in any of the buffers-->

Current Email : <input type = "text" name = "email" placeholder = "Current Email" value = "<?php echo $email; ?>" required autocomplete = "off" <?php if ( $status === true ) : echo "disabled"; endif; ?> /><span>  * <?php echo $emailerr; ?></span><br /><br /> 

New Email : <input type = "text" name = "newemail" placeholder = "New Email" value = "<?php echo $newemail; ?>" required autocomplete = "off" <?php if ( $status === true ) : echo "disabled"; endif; ?> /><span>  *<?php echo $newemailerr;?></span><br /><br />
<input type = "Submit" value = "Submit"  <?php if ( $status === true ) : echo "disabled"; endif; ?> />

</form>

<h2 id = "status" ></h2>

</div>

<?php
if ( $status === true ) {
	echo "<script>document.getElementById(\"status\").innerHTML = \"Email Changed Successfully\"</script>";
 }
?>

</body>

</html>