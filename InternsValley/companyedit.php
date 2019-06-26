<?php
//session start
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

<html>

<head>

<title>Profile - <?php echo $uname; ?> - Edit</title>
<meta charset = "utf-8" />
<meta name = "keywords" content = "Interns Valley" />
<meta name = "description" content = "Company Profile Page Edit" />
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
 }
</style>

<!--<script>
function remove() {
	<?php //$photo = "default.png"; ?>
	document.getElementById('remove').innerHTML = "<?php echo $photo; ?>";
 }
</script>-->

</head>

<header>
<h1>INTERNS VALLEY</h1>
</header>

<nav>
<ul>
<li><a href = "companychangepassword.php" target = "_blank" >Change Password</a></li>
<li><a href = "companychangeemail.php" target = "_blank" > Change Email</a></li>
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

<?php

$logoerr=$status=$uploadok=$websiteerr="";

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
 }

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
	
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
	$logonew = $_FILES["logo"];
	$description = test_input($_POST["description"]);


	if( $logonew["name"] != "" && ( $logo != $logonew["name"] ) ) {
		//Setting Directory for file upload
		$target_dir = "upload/";
		//$target_dir = "companylogo/";

		//Getting the name of file using basename() function
		$target_file = $target_dir.basename( $_FILES["logo"]["name"] );
		//Setting flag
		$uploadok = 1;

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
	else {
		$logonew["name"] = $logo;
	 }

	//Connecting To server
	if ( $logoerr == "" && $websiteerr == "" ) {
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

			//echo $photonew["name"];
			//echo $photo;

			$stmt = "UPDATE companyprofile
				SET companyname = \"$companyname\", website =  \"$website\", lineofbusiness = \"$lineofbusiness\", established = \"$established\",
				addlocation =  \"$addlocation\", addcity =  \"$addcity\", addstate =  \"$addstate\", hqlocation =  \"$hqlocation\",
				hqcity = \"$hqcity\", hqstate = \"$hqstate\", phone =  \"$phone\", logo =  \"$logonew[name]\", description = \"$description\"
				where username = \"$uname\" ";

			$conn -> exec($stmt);
			$status = true;

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

<div>
<span> * required </span>

<form method = "POST" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype = "multipart/form-data" >

<h2>Company Information</h2>

<img src = "upload/<?php 
if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {  
		echo $logonew["name"];
 }
else { 
	echo $logo;
 } ?>" style = "width : 250px; height : 250px; border-radius : 50%" />

<!--<button type = "button" onclick = remove() >Remove</button>--><br />

<label>Upload Logo</label><br />
<input type = "file" name = "logo"  value = "<?php echo $logo; ?>" /><span><?php echo $logoerr; ?></span><br /><br />

<label>Company Name</label><br />
<input type = "text" name = "companyname" placeholder = "Company Name" value = "<?php echo $companyname; ?>" required autocomplete = "off" /><span> *   </span><br /><br />

<label>Website</label><br />
<input type = "text" name = "website" placeholder = "Website" value = "<?php echo $website; ?>" required autocomplete = "off" /><span> *  <?php echo $websiteerr; ?></span><br /><br />

<label>Line of Business</label><span> * </span><br />
<input type = "text" name = "lineofbusiness" placeholder = "Line of Business" value = "<?php echo $lineofbusiness; ?>" required autocomplete = "off" /><span> *   </span><br /><br />

<label>Established</label><span> * </span><br />
<input type = "text" name = "established" placeholder = "Year" value = "<?php echo $established; ?>" pattern = "[0-9]{4}" title = "Proper Year Format" required autocomplete = "off" /><span> *   </span><br /><br />

<h2>Address</h2>

<label>Company Address</label><span> </span><br />
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

<label>Headquarters Address</label><span></span><br />
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

<h2>Contact Information</h2>

<label>Phone</label><br />
<input type = "text" name = "phone" value = "<?php echo $phone; ?>" placeholder = "Phone No." pattern = "[0-9]{10}" title = "Proper Mobile Number Format" required autocomplete = "off" /><span> *   </span><br /><br />

<label>Description</label><br />
<textarea rows = "5" cols = "50" name = "description" placeholder = "About Your Company"><?php echo $description; ?></textarea><br /><br />

<input type = "Submit" name = "Submit" />

</form>

</div>

<?php

//on Successful Registration 
if ( $status === true ) {
	echo "<script>window.location.href = \"companyprofile.php\"</script>";
 }

?>

</body>
</html>