<?php 
//Starting a Session
session_start();
?>

<!doctype html>

<html>

<head>
	
<meta charset = "UTF-8" />
<meta name = "description" content = "Company Registration Page for InternsValley" />
<meta name = "keywords" content = "Company Registration Page, Interns Valley" />
<meta name = "viewport" content = "width=device-width, initial-scale = 1.0" />

<title>Company Registration Page</title>

<link rel = "stylesheet" type = "text/css" href = "headernav.css" />
<link rel = "stylesheet" type = "text/css" href = "font.css" />
<link rel = "stylesheet" type = "text/css" href = "input.css" />
<link rel = "stylesheet" type = "text/css" href = "content.css" />

<style>
h2 {
	color : orange;
	font-size : 150%;
 }
div {
	font-size : 120%;
 }
</style>
<!--
<style>
#next {
	display : none
}
</style>
-->

<!--
<script>
//Next leads to next page i.e companyregistrationform.php
function nextpage() {
	document.window.location.href = "companyregistrationform.php";
}
//Haven't Used this. This Failed.
//Displays Next Button onchange of Status
function display() {
	getElementById("next").style.display = "block";
}
</script>-->

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<body>

<?php
//setting variables to null
$email=$uname=$pwd=$cpwd=$status="";
$emailerr=$unameerr=$cpwderr="";

//function to trim data so as to be secure from XSS
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//if form is submitted
if($_SERVER["REQUEST_METHOD"]=="POST") {
	
	$email=test_input($_POST["email"]);
	//Check if Email is of correct format
	if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
		$emailerr="Invalid Email Format";
	 }
	
	$uname = test_input($_POST["username"]);
	$pwd = test_input($_POST["password"]);
	$cpwd=test_input($_POST["cpassword"]);

	if($pwd != $cpwd) {
		$cpwderr="Incorrect Password. Please Retype Password same as above.";
	 }

	//Connecting To server after everything is right
	if($emailerr=="" && $cpwderr=="") {
		$servername="localhost";
		$username="root";
		$password="";
		$database="webinternship";
		//$database="demowebinternship";

		try {
			$conn = new PDO("mysql:host = $servername;dbname = $database", $username, $password);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
			//begin transaction
			$conn->beginTransaction();

			$sql = "USE $database";
			$conn->exec($sql);

			$stmt=$conn->prepare("SELECT email, username FROM companylogin");
			$stmt->execute();
		
			//Setting to FetchMode
			$result=$stmt->setFetchMode(PDO::FETCH_ASSOC);

			//Fetching in the form of Array
			$arr = $stmt->fetchAll();

			//Checking if username or emailid matches
			foreach ($arr as $value) {
				if( $value["email"] == $email ) {
					$emailerr = "Email ID already Registered";
					break;
				 }
			 }
			foreach ($arr as $value) {
				if( $value["username"] == $uname ) {
					$unameerr = "Username already Exists";
					break;
				 }
			 }
		
			//If username and Email Doesn't Exists
			if( $emailerr=="" && $unameerr=="" ) {
				$sql = "INSERT INTO companylogin (email, username, password ) VALUES (\"$email\", \"$uname\", \"$pwd\")";
				$conn->exec($sql);
				$status = true;
			 }
		
			//Commit transaction
			$conn->commit();

			$conn = null;		
	 	 }
		catch (PDOException $e) {
			//Roll back if something in the transaction is wrong
			$conn->rollback();
			//echo $e->getMessage();
			$unameerr = "Username Already Exists";
		 }
		
 	 }
 }

?>

<h2>COMPANY REGISTRATION</h2>
<div>

<span>* required</span><br /><br />

<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" > <!--Submitting form to the same page-->

Email ID : <input type = "text" name = "email" placeholder = "Email ID" value = "<?php echo $email; ?>" required autocomplete ="off" /><span>  *<?php echo $emailerr;?></span><br /><br /> <!--Displaying Errors on the same page and retaining values after submit-->

Create Username : <input type = "text" name = "username" placeholder = "Username" value = "<?php echo $uname; ?>" required  autocomplete ="off" /><span>  *<?php echo $unameerr;?></span><br /><br />

Create Password : <input type = "password" name = "password" placeholder = "Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 6 or more characters" value = "<?php echo $pwd; ?>" required /><span>  *</span><br /><br /> <!--(?= refer to metacharacter that does not store the matched pattern in any of the buffers-->

Confirm Password : <input type = "password" name = "cpassword" placeholder = "Confirm Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}" title="Please re-enter the same password" value = "<?php echo $cpwd; ?>" required /><span>  *<?php echo $cpwderr;?></span><br /><br />
<input type = "Submit" value = "Submit" />

</form>

</div>

<?php
	if( $status === true ) {

		$_SESSION["companyusername"] = $uname;
		//echo "<script> window.location.href = \"companyregistrationform.php\" </script><br />";

		//Redirecting to page
		header ( 'Location: companyregistrationform.php' );
		exit();
	 }
?>

</body>

</html>