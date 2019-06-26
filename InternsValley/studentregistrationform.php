<?php
session_start();
//echo $_SESSION["studentusername"];
//var_dump( $_SESSION );

?>

<!doctype html>

<html>

<head>
	
<meta charset = "UTF-8" />
<meta name = "description" content = "Student Registration Form for InternsValley" />
<meta name = "keywords" content = "Student Registration Form, Interns Valley" />
<meta name = "viewport" content = "width=device-width, initial-scale = 1.0" />

<title>Student Registration Form</title>

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
$uname=$firstname=$lastname=$gender=$degree=$college=$fieldofstudy=$fromyear=$toyear=$skills=$state=$city=$phone=$photo="";
$unameerr = $photoerr=$status=$uploadok="";

if(! empty($_SESSION["studentusername"] ) ) {
	$uname = $_SESSION["studentusername"];
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

	$firstname = test_input($_POST["firstname"]);
	$lastname = test_input($_POST["lastname"]);
	$gender = test_input($_POST["gender"]);
	$degree = test_input($_POST["degree"]);
	$college = test_input($_POST["college"]);
	$fieldofstudy = test_input($_POST["fieldofstudy"]);
	$fromyear = test_input( (int) ($_POST["fromyear"]));
	$toyear = test_input( (int) ($_POST["toyear"]));
	$skills = test_input($_POST["skills"]);
	$state = test_input($_POST["state"]);
	$city = test_input($_POST["city"]);
	$phone = test_input( (float) ($_POST["phone"]));
	$photo = $_FILES["photo"];

	//var_dump ($photo);

	if( $photo["name"] != "") {
		//Setting Directory for file upload
		$target_dir = "upload/";
		//$target_dir = "studentprofile/";

		//Getting the name of file using basename() function
		$target_file = $target_dir.basename( $_FILES["photo"]["name"] );
		//Setting flag
		$uploadok = 1;

		$imagename = $_FILES["photo"]["name"];
	
		/*$imagetype = $_FILES["photo"]["type"];
		$imagesize = $_FILES["photo"]["size"];
		$imagetemp = $_FILES["photo"]["tmp_name"];
		$imagebasename = basename($_FILES["photo"]["name"]);
		$image = file_get_contents($_FILES["photo"]["name"]);*/
	

		//Getting file extension of choosen file
		$imagefiletype = strtolower(pathinfo($target_file, PATHINFO_EXTENSION ));
		//$imagefiletype = strtolower(pathinfo($imagename, PATHINFO_EXTENSION ));
	
		//Checking if image is actual or fake
		$check = getimagesize( $_FILES["photo"]["tmp_name"]);
		if( $check !== false ) {
			$uploadok = 1;
	 	 }
		else {
			$uploadok = 0;
			$photoerr = "File is not an Image";
		 }

		//Check If file already exists
		/*if ( file_exists ( $target_file) ) {
			$uploadok = 0;
			$photoerr = "File already Exists";
		 }*/

		//Checking File size
		if ( $_FILES["photo"]["size"] > 1048576 ) {
			$uploadok = 0;
			$photoerr = "File is too large";
		 }

		//Checking Extensions
		if ( $imagefiletype != "jpg" && $imagefiletype != "jpeg" && $imagefiletype != "png" && $imagefiletype != "gif" ) {
			$uploadok = 0;
			$photoerr = "File is not of Format jpg, jpeg, png or gif";
		 }

		//If image is fine then upload
		if ($uploadok == 1 ) {
			if ( ! move_uploaded_file( $_FILES["photo"]["tmp_name"], $target_file ) ) {
				$uploadok = 0;
				$photoerr = "Image Upload Failed";
			 }
		 }
	 }

	//Connecting To server
	if ( $unameerr == "" && $photoerr == "") {
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

			$stmt = $conn->prepare ( "SELECT username FROM studentprofile" );
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

				$stmt = "INSERT INTo studentprofile (username, firstname, lastname, gender, degree, college, fieldofstudy, fromyear, toyear, skills, photo, state, city, phone)
					values ( \"$uname\", \"$firstname\", \"$lastname\", \"$gender\", \"$degree\", \"$college\", \"$fieldofstudy\", \"$fromyear\", \"$toyear\", \"$skills\", \"$photo[name]\", \"$state\", \"$city\", \"$phone\" )";

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

<h2>STUDENT REGISTRATION FORM</h2>

<div>
<span> * required </span>

<form method = "POST" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" enctype = "multipart/form-data" >

<h4>Personal Information</h4>

<label>Username</label><br />
<input type = "text" name = "username" value = "<?php echo $uname; ?>"  disabled /><br /><br />

<label> Name</label><br />
<input type = "text" name = "firstname" placeholder = "First Name" value = "<?php echo $firstname; ?>" required autocomplete = "off" /><span> *   </span>

<input type = "text" name = "lastname" placeholder = "Last Name" value = "<?php echo $lastname; ?>" required autocomplete = "off" /><span> * </span><br /><br />

<label>Gender</label><span> * </span><br />
<select name = "gender" value = "<?php echo $gender; ?>" required >
<option value="">--Select--</option>
<option value = "Male" <?php if (isset($gender) && $gender == "Male" ) echo "selected"; ?> >Male</option>
<option value = "Female" <?php if (isset($gender) && $gender == "Female" ) echo "selected"; ?> >Female</option>
<option value = "Other" <?php if (isset($gender) && $gender == "Other" ) echo "selected"; ?> >Other</option>
<?php echo $gender; ?></select>

<h4>Education</h4>

<label>Degree</label><span> * </span><br />
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

<label>College</label><span> * </span><br />
<textarea rows = "3" cols = "50" name = "college" value = "<?php echo $college; ?>" placeholder = "College" required ><?php echo $college; ?></textarea><br /><br />

<label>Field Of Study</label><br />
<input type = "text" name = "fieldofstudy" value = "<?php echo $fieldofstudy; ?>" placeholder = "Field Of Study" autocomplete = "off" /><br /><br />

<label>Year of Study</label><br />
<input type = "text" name = "fromyear" value = "<?php echo $fromyear; ?>" placeholder = "From" pattern = "[0-9]{4}" title = "Proper Year Format" autocomplete = "off" />
      
<input type = "text" name = "toyear" value = "<?php echo $toyear; ?>" placeholder = "To" pattern = "[0-9]{4}" title = "Proper Year Format" autocomplete = "off" /><br /><br />

<label>Skills</label><span> * </span><br />
<textarea rows = "4" cols = "50" name = "skills" value = "<?php echo $skills; ?>" placeholder = "Enter your Skills" required ><?php echo $skills; ?></textarea><br />

<h4>Contact Details</h4>

<label>Where Do You Live?</label><br />
<select name = "state" value="<?php echo $state; ?>" >
<option value = "" >--State--</option>
<option value = "Andhra Pradesh" <?php if (isset($state) && $state == "Andhra Pradesh" ) echo "selected"; ?> >Andhra Pradesh</option>
<option value = "Arunachal Pradesh" <?php if (isset($state) && $state == "Arunachal Pradesh" ) echo "selected"; ?> >Arunachal Pradesh</option>
<option value = "Assam" <?php if (isset($state) && $state == "Assam" ) echo "selected"; ?> >Assam</option>
<option value = "Bihar" <?php if (isset($state) && $state == "Bihar" ) echo "selected"; ?> >Bihar</option>
<option value = "Chatisgarh" <?php if (isset($state) && $state == "Chatisgarh" ) echo "selected"; ?> >Chatisgarh</option>
<option value = "Goa" <?php if (isset($state) && $state == "Goa" ) echo "selected"; ?> >Goa</option>
<option value = "Gujarat" <?php if (isset($state) && $state == "Gujarat" ) echo "selected"; ?> >Gujarat</option>
<option value = "Haryana" <?php if (isset($state) && $state == "Haryana" ) echo "selected"; ?> >Haryana</option>
<option value = "Himachal Pradesh" <?php if (isset($state) && $state == "Himachal Pradesh" ) echo "selected"; ?> >Himachal Pradesh</option>
<option value = "Jammu Kashmir" <?php if (isset($state) && $state == "Jammu Kashmir" ) echo "selected"; ?> >Jammu Kashmir</option>
<option value = "Jharkand" <?php if (isset($state) && $state == "Jharkand" ) echo "selected"; ?> >Jharkand</option>
<option value = "Karnataka" <?php if (isset($state) && $state == "Karnataka" ) echo "selected"; ?> >Karnataka</option>
<option value = "Kerala" <?php if (isset($state) && $state == "Kerala" ) echo "selected"; ?> >Kerala</option>
<option value = "Madhya Pradesh" <?php if (isset($state) && $state == "Madhya Pradesh" ) echo "selected"; ?> >Madhya Pradesh</option>
<option value = "Maharastra" <?php if (isset($state) && $state == "Maharastra" ) echo "selected"; ?> >Maharastra</option>
<option value = "Manipur" <?php if (isset($state) && $state == "Manipur" ) echo "selected"; ?> >Manipur</option>
<option value = "Meghalaya" <?php if (isset($state) && $state == "Meghalaya" ) echo "selected"; ?> >Meghalaya</option>
<option value = "Mizoram" <?php if (isset($state) && $state == "Mizoram" ) echo "selected"; ?> >Mizoram</option>
<option value = "Nagaland" <?php if (isset($state) && $state == "Nagaland" ) echo "selected"; ?> >Nagaland</option>
<option value = "Orissa" <?php if (isset($state) && $state == "Orissa" ) echo "selected"; ?> >Orissa</option>
<option value = "Punjab" <?php if (isset($state) && $state == "Punjab" ) echo "selected"; ?> >Punjab</option>
<option value = "Rajasthan" <?php if (isset($state) && $state == "Rajasthan" ) echo "selected"; ?> >Rajasthan</option>
<option value = "Sikkim" <?php if (isset($state) && $state == "Sikkim" ) echo "selected"; ?> >Sikkim</option>
<option value = "Tamilnadu" <?php if (isset($state) && $state == "Tamilnadu" ) echo "selected"; ?> >Tamilnadu</option>
<option value = "Telangana" <?php if (isset($state) && $state == "Telangana" ) echo "selected"; ?> >Telangana</option>
<option value = "Tripura" <?php if (isset($state) && $state == "Tripura" ) echo "selected"; ?> >Tripura</option>
<option value = "Uttaranchal" <?php if (isset($state) && $state == "Uttaranchal" ) echo "selected"; ?> >Uttaranchal</option>
<option value = "Uttar Pradesh" <?php if (isset($state) && $state == "Uttar Pradesh" ) echo "selected"; ?> >Uttar Pradesh</option>
<option value = "West Bengal" <?php if (isset($state) && $state == "West Bengal" ) echo "selected"; ?> >West Bengal</option>
</select>
         
<input type = "text" name = "city" value = "<?php echo $city; ?>" placeholder = "City" autocomplete = "off" /><br /><br />

<label>Phone</label><br />
<input type = "text" name = "phone" value = "<?php echo $phone; ?>" placeholder = "Phone No." pattern = "[0-9]{10}" title = "Proper Mobile Number Format" autocomplete = "off" /><br /><br />

<label>Upload Photo</label><br />
<input type = "file" name = "photo"  value = "<?php echo $imagename; ?>" /><span><?php echo $photoerr; ?><br /><br />

<input type = "Submit" name = "Submit" />

</form>

<?php
//If username is Empty i.e who have directly entered without creating username and password
if ( $unameerr == "Empty" ) {
	echo "<script>window.location.href = \"studentregistration.php\" </script>";
 }

//If User has directly entered registration page though his username exists
if ( $unameerr == "User Already Exists" ) {
	echo "<script>window.location.href = \"studentlogin.php\" </script>" ;
 }

//on Successful Registration 
if ( $status == "Registered" ) {
	$_SESSION["studentlogin"] = "active";
	echo "<script>window.location.href = \"studentprofile.php\"</script>";
 }
?>

</div>

</body>
</html>