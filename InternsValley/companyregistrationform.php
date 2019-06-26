<?php
session_start();
//echo $_SESSION["companyusername"];
//var_dump( $_SESSION );

?>

<!doctype html>

<html>

<head>
	
<meta charset = "UTF-8" />
<meta name = "description" content = "Company Registration Form for InternsValley" />
<meta name = "keywords" content = "Company Registration Form, Interns Valley" />
<meta name = "viewport" content = "width=device-width, initial-scale = 1.0" />

<title>Company Registration Form</title>

<link rel = "stylesheet" type = "text/css" href = "headernav.css" />
<link rel = "stylesheet" type = "text/css" href = "font.css" />
<link rel = "stylesheet" type = "text/css" href = "input.css" />
<link rel = "stylesheet" type = "text/css" href = "content.css" />

<style>
h2,h4 {
	color : orange;
	font-size : 150%;
 }
div {
	color : orange;
	font-size : 120%;
 }
</style>

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<body>

<?php
$uname=$companyname=$website=$lineofbusiness=$established=$addlocation=$addcity=$addstate=$hqlocation=$hqcity=$hqstate=$phone=$logo=$description="";
$unameerr = $logoerr=$status=$uploadok=$websiteerr="";

if(! empty($_SESSION["companyusername"] ) ) {
	$uname = $_SESSION["companyusername"];
 }
//print_r($_SESSION);

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
 }

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
	//var_dump ($_POST);
	//checking if username field is empty
	if (empty( $uname ) ) {
		$unameerr = "Empty";
	 }

	$website = test_input($_POST["website"]);
	$website = ( strpos( $website, 'http' ) !== 0 ? "http://$website" : $website ); 
	//Check if Email is of correct format
	if( ! filter_var( $website, FILTER_VALIDATE_URL ) ) {
		$websiteerr="Invalid Website Format";
	 }

	$companyname = test_input($_POST["companyname"]);
	$lineofbusiness = test_input($_POST["lineofbusiness"]);
	$established = test_input($_POST["established"]);
	$addlocation = test_input($_POST["addlocation"]);
	$addcity = test_input($_POST["addcity"]);
	$addstate = test_input( $_POST["addstate"]);
	$hqlocation = test_input( $_POST["hqlocation"] );
	$hqcity = test_input($_POST["hqcity"]);
	$hqstate = test_input($_POST["hqstate"]);
	$phone = test_input( (float) ($_POST["phone"]));
	$logo = $_FILES["logo"];
	$description = test_input($_POST["description"]);

	//var_dump ($photo);

	if( $logo["name"] != "") {
		//Setting Directory for file upload
		$target_dir = "upload/";
		//$target_dir = "companylogo/";

		//Getting the name of file using basename() function
		$target_file = $target_dir.basename( $_FILES["logo"]["name"] );
		//Setting flag
		$uploadok = 1;

		//$imagename = $_FILES["logo"]["name"];
	
		/*$imagetype = $_FILES["logo"]["type"];
		$imagesize = $_FILES["logo"]["size"];
		$imagetemp = $_FILES["logo"]["tmp_name"];
		$imagebasename = basename($_FILES["logo"]["name"]);
		$image = file_get_contents($_FILES["logo"]["name"]);*/
	

		//Getting file extension of choosen file
		$imagefiletype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION ));
		//$imagefiletype = strtolower(pathinfo($imagename, PATHINFO_EXTENSION ));
	
		//Checking if image is actual or fake
		$check = getimagesize( $_FILES["logo"]["tmp_name"]);
		if( $check !== false ) {
			$uploadok = 1;
	 	 }
		else {
			$uploadok = 0;
			$logoerr = "File is not an Image";
		 }

		//Check If file already exists
		/*if ( file_exists ( $target_file) ) {
			$uploadok = 0;
			$logoerr = "File already Exists";
		 }*/

		//Checking File size
		if ( $_FILES["logo"]["size"] > 1048576 ) {
			$uploadok = 0;
			$logoerr = "File is too large";
		 }

		//Checking Extensions
		if ( $imagefiletype != "jpg" && $imagefiletype != "jpeg" && $imagefiletype != "png" && $imagefiletype != "gif" ) {
			$uploadok = 0;
			$logoerr = "File is not of Format jpg, jpeg, png or gif";
		 }

		//If image is fine then upload
		if ($uploadok == 1 ) {
			if ( ! move_uploaded_file( $_FILES["logo"]["tmp_name"], $target_file ) ) {
				$uploadok = 0;
				$logoerr = "Image Upload Failed";
			 }
		 }
	 }

	//Connecting To server
	if ( $unameerr == "" && $logoerr == "" && $websiteerr == "" ) {
		$servername = "localhost";
		$username = "root";
		$password = "";
		$database = "webinternship";
		//$database = "demowebinternship";

		try {
			$conn = new PDO( "mysql:host = $servername; dbname = $database", $username, $password );
			$conn->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			//echo "Connected";

			//beginTransaction
			$conn->beginTransaction();

			$sql = "USE $database";
			$conn->exec( $sql );

			$stmt = $conn->prepare ( "SELECT username FROM companyprofile" );
			$stmt->execute();

			$result = $stmt->setFetchMode ( PDO::FETCH_ASSOC);
			$arr = $stmt->fetchAll ( );

			foreach ( $arr as $value ) {
				//var_dump ($value);
				if ( $value["username"] == $uname ) {
					$unameerr = "User Already Exists";
					break;
				 }
			 }

			if ( $unameerr == "" ) {

				$stmt = "INSERT INTo companyprofile (username, companyname, website, lineofbusiness, established, addlocation, addcity, addstate, hqlocation, hqcity, hqstate, phone, logo,description )
					values ( \"$uname\", \"$companyname\", \"$website\", \"$lineofbusiness\", \"$established\", \"$addlocation\", \"$addcity\", \"$addstate\", \"$hqlocation\", \"$hqcity\", \"$hqstate\", \"$phone\", \"$logo[name]\", \"$description\" )";

				$conn -> exec($stmt);
				$status = "Registered";
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

<h2>COMPANY REGISTRATION FORM</h2>

<div>
<span> * required </span>

<form method = "POST" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype = "multipart/form-data" >

<h4>Company Information</h4>

<label>Username</label><br />
<input type = "text" name = "username" value = "<?php echo $uname; ?>"  disabled /><br /><br />

<label>Company Name</label><br />
<input type = "text" name = "companyname" placeholder = "Company Name" value = "<?php echo $companyname; ?>" required autocomplete = "off" /><span> *   </span><br /><br />

<label>Website</label><br />
<input type = "text" name = "website" placeholder = "Website" value = "<?php echo $website; ?>" required autocomplete = "off" /><span> *  <?php echo $websiteerr; ?></span><br /><br />

<label>Line of Business</label><span> * </span><br />
<input type = "text" name = "lineofbusiness" placeholder = "Line of Business" value = "<?php echo $lineofbusiness; ?>" required autocomplete = "off" /><span> *   </span><br /><br />

<label>Established</label><span> * </span><br />
<input type = "text" name = "established" placeholder = "Year" value = "<?php echo $established; ?>" pattern = "[0-9]{4}" title = "Proper Year Format" required autocomplete = "off" /><span> *   </span><br /><br />

<h4>Address</h4>

<label>Company Address</label><span> * </span><br />
<input type = "text" name = "addlocation" value = "<?php echo $addlocation; ?>" placeholder = "Location" required autocomplete = "off" /><span> *   </span><?php echo "		"; ?>

<input type = "text" name = "addcity" value = "<?php echo $addcity; ?>" placeholder = "City" required autocomplete = "off" /><span> *   </span><?php echo "		"; ?>

<select name = "addstate" value="<?php echo $addstate; ?>" >
<option value = "" >--State--</option>
<option value = "Andhra Pradesh" <?php if (isset($addstate) && $addstate == "Andhra Pradesh" ) echo "selected"; ?> >Andhra Pradesh</option>
<option value = "Arunachal Pradesh" <?php if (isset($addstate) && $addstate == "Arunachal Pradesh" ) echo "selected"; ?> >Arunachal Pradesh</option>
<option value = "Assam" <?php if (isset($addstate) && $addstate == "Assam" ) echo "selected"; ?> >Assam</option>
<option value = "Bihar" <?php if (isset($addstate) && $addstate == "Bihar" ) echo "selected"; ?> >Bihar</option>
<option value = "Chatisgarh" <?php if (isset($addstate) && $addstate == "Chatisgarh" ) echo "selected"; ?> >Chatisgarh</option>
<option value = "Goa" <?php if (isset($addstate) && $addstate == "Goa" ) echo "selected"; ?> >Goa</option>
<option value = "Gujarat" <?php if (isset($addstate) && $addstate == "Gujarat" ) echo "selected"; ?> >Gujarat</option>
<option value = "Haryana" <?php if (isset($addstate) && $addstate == "Haryana" ) echo "selected"; ?> >Haryana</option>
<option value = "Himachal Pradesh" <?php if (isset($addstate) && $addstate == "Himachal Pradesh" ) echo "selected"; ?> >Himachal Pradesh</option>
<option value = "Jammu Kashmir" <?php if (isset($addstate) && $addstate == "Jammu Kashmir" ) echo "selected"; ?> >Jammu Kashmir</option>
<option value = "Jharkand" <?php if (isset($addstate) && $addstate == "Jharkand" ) echo "selected"; ?> >Jharkand</option>
<option value = "Karnataka" <?php if (isset($addstate) && $addstate == "Karnataka" ) echo "selected"; ?> >Karnataka</option>
<option value = "Kerala" <?php if (isset($addstate) && $addstate == "Kerala" ) echo "selected"; ?> >Kerala</option>
<option value = "Madhya Pradesh" <?php if (isset($addstate) && $addstate == "Madhya Pradesh" ) echo "selected"; ?> >Madhya Pradesh</option>
<option value = "Maharastra" <?php if (isset($addstate) && $addstate == "Maharastra" ) echo "selected"; ?> >Maharastra</option>
<option value = "Manipur" <?php if (isset($addstate) && $addstate == "Manipur" ) echo "selected"; ?> >Manipur</option>
<option value = "Meghalaya" <?php if (isset($addstate) && $addstate == "Meghalaya" ) echo "selected"; ?> >Meghalaya</option>
<option value = "Mizoram" <?php if (isset($addstate) && $addstate == "Mizoram" ) echo "selected"; ?> >Mizoram</option>
<option value = "Nagaland" <?php if (isset($addstate) && $addstate == "Nagaland" ) echo "selected"; ?> >Nagaland</option>
<option value = "Orissa" <?php if (isset($addstate) && $addstate == "Orissa" ) echo "selected"; ?> >Orissa</option>
<option value = "Punjab" <?php if (isset($addstate) && $addstate == "Punjab" ) echo "selected"; ?> >Punjab</option>
<option value = "Rajasthan" <?php if (isset($addstate) && $addstate == "Rajasthan" ) echo "selected"; ?> >Rajasthan</option>
<option value = "Sikkim" <?php if (isset($addstate) && $addstate == "Sikkim" ) echo "selected"; ?> >Sikkim</option>
<option value = "Tamilnadu" <?php if (isset($addstate) && $addstate == "Tamilnadu" ) echo "selected"; ?> >Tamilnadu</option>
<option value = "Telangana" <?php if (isset($addstate) && $addstate == "Telangana" ) echo "selected"; ?> >Telangana</option>
<option value = "Tripura" <?php if (isset($addstate) && $addstate == "Tripura" ) echo "selected"; ?> >Tripura</option>
<option value = "Uttaranchal" <?php if (isset($addstate) && $addstate == "Uttaranchal" ) echo "selected"; ?> >Uttaranchal</option>
<option value = "Uttar Pradesh" <?php if (isset($addstate) && $addstate == "Uttar Pradesh" ) echo "selected"; ?> >Uttar Pradesh</option>
<option value = "West Bengal" <?php if (isset($addstate) && $addstate == "West Bengal" ) echo "selected"; ?> >West Bengal</option>
</select><span> *   </span><br /><br />

<label>Headquarters Address</label><span> * </span><br />
<input type = "text" name = "hqlocation" value = "<?php echo $hqlocation; ?>" placeholder = "Location" required autocomplete = "off" /><span> *   </span><?php echo "		"; ?>

<input type = "text" name = "hqcity" value = "<?php echo $hqcity; ?>" placeholder = "City" required autocomplete = "off" /><span> *   </span><?php echo "		"; ?>

<select name = "hqstate" value="<?php echo $hqstate; ?>" >
<option value = "" >--State--</option>
<option value = "Andhra Pradesh" <?php if (isset($hqstate) && $hqstate == "Andhra Pradesh" ) echo "selected"; ?> >Andhra Pradesh</option>
<option value = "Arunachal Pradesh" <?php if (isset($hqstate) && $hqstate == "Arunachal Pradesh" ) echo "selected"; ?> >Arunachal Pradesh</option>
<option value = "Assam" <?php if (isset($hqstate) && $hqstate == "Assam" ) echo "selected"; ?> >Assam</option>
<option value = "Bihar" <?php if (isset($hqstate) && $hqstate == "Bihar" ) echo "selected"; ?> >Bihar</option>
<option value = "Chatisgarh" <?php if (isset($hqstate) && $hqstate == "Chatisgarh" ) echo "selected"; ?> >Chatisgarh</option>
<option value = "Goa" <?php if (isset($hqstate) && $hqstate == "Goa" ) echo "selected"; ?> >Goa</option>
<option value = "Gujarat" <?php if (isset($hqstate) && $hqstate == "Gujarat" ) echo "selected"; ?> >Gujarat</option>
<option value = "Haryana" <?php if (isset($hqstate) && $hqstate == "Haryana" ) echo "selected"; ?> >Haryana</option>
<option value = "Himachal Pradesh" <?php if (isset($hqstate) && $hqstate == "Himachal Pradesh" ) echo "selected"; ?> >Himachal Pradesh</option>
<option value = "Jammu Kashmir" <?php if (isset($hqstate) && $hqstate == "Jammu Kashmir" ) echo "selected"; ?> >Jammu Kashmir</option>
<option value = "Jharkand" <?php if (isset($hqstate) && $hqstate == "Jharkand" ) echo "selected"; ?> >Jharkand</option>
<option value = "Karnataka" <?php if (isset($hqstate) && $hqstate == "Karnataka" ) echo "selected"; ?> >Karnataka</option>
<option value = "Kerala" <?php if (isset($hqstate) && $hqstate == "Kerala" ) echo "selected"; ?> >Kerala</option>
<option value = "Madhya Pradesh" <?php if (isset($hqstate) && $hqstate == "Madhya Pradesh" ) echo "selected"; ?> >Madhya Pradesh</option>
<option value = "Maharastra" <?php if (isset($hqstate) && $hqstate == "Maharastra" ) echo "selected"; ?> >Maharastra</option>
<option value = "Manipur" <?php if (isset($hqstate) && $hqstate == "Manipur" ) echo "selected"; ?> >Manipur</option>
<option value = "Meghalaya" <?php if (isset($hqstate) && $hqstate == "Meghalaya" ) echo "selected"; ?> >Meghalaya</option>
<option value = "Mizoram" <?php if (isset($hqstate) && $hqstate == "Mizoram" ) echo "selected"; ?> >Mizoram</option>
<option value = "Nagaland" <?php if (isset($hqstate) && $hqstate == "Nagaland" ) echo "selected"; ?> >Nagaland</option>
<option value = "Orissa" <?php if (isset($hqstate) && $hqstate == "Orissa" ) echo "selected"; ?> >Orissa</option>
<option value = "Punjab" <?php if (isset($hqstate) && $hqstate == "Punjab" ) echo "selected"; ?> >Punjab</option>
<option value = "Rajasthan" <?php if (isset($hqstate) && $hqstate == "Rajasthan" ) echo "selected"; ?> >Rajasthan</option>
<option value = "Sikkim" <?php if (isset($hqstate) && $hqstate == "Sikkim" ) echo "selected"; ?> >Sikkim</option>
<option value = "Tamilnadu" <?php if (isset($hqstate) && $hqstate == "Tamilnadu" ) echo "selected"; ?> >Tamilnadu</option>
<option value = "Telangana" <?php if (isset($hqstate) && $hqstate == "Telangana" ) echo "selected"; ?> >Telangana</option>
<option value = "Tripura" <?php if (isset($hqstate) && $hqstate == "Tripura" ) echo "selected"; ?> >Tripura</option>
<option value = "Uttaranchal" <?php if (isset($hqstate) && $hqstate == "Uttaranchal" ) echo "selected"; ?> >Uttaranchal</option>
<option value = "Uttar Pradesh" <?php if (isset($hqstate) && $hqstate == "Uttar Pradesh" ) echo "selected"; ?> >Uttar Pradesh</option>
<option value = "West Bengal" <?php if (isset($hqstate) && $hqstate == "West Bengal" ) echo "selected"; ?> >West Bengal</option>
</select><span> *   </span><br /><br />

<h4>Contact Information</h4>

<label>Phone</label><br />
<input type = "text" name = "phone" value = "<?php echo $phone; ?>" placeholder = "Phone No." pattern = "[0-9]{10}" title = "Proper Mobile Number Format" required autocomplete = "off" /><span> *   </span><br /><br />

<label>Upload Logo</label><br />
<input type = "file" name = "logo"  value = "<?php echo $logo["name"]; ?>" required /><span><?php echo $logoerr; ?><span> *   </span><br /><br />

<label>Description</label><br />
<textarea rows = "5" cols = "50" name = "description" placeholder = "About Your Company"><?php echo $description; ?></textarea><br /><br />

<input type = "Submit" name = "Submit" />

</form>

</div>

<?php
//If username is Empty i.e who have directly entered without creating username and password
if ( $unameerr == "Empty" ) {
	echo "<script>window.location.href = \"companyregistration.php\" </script>";
 }

//If User has directly entered registration page though his username exists
if ( $unameerr == "User Already Exists" ) {
	echo "<script>window.location.href = \"companylogin.php\" </script>" ;
 }

//on Successful Registration 
if ( $status == "Registered" ) {
	$_SESSION["companylogin"] = "active";
	echo "<script>window.location.href = \"companyprofile.php\"</script>";
 }
?>

</body>
</html>