<!doctype html>

<html>

<head>
<title>Create Tables</title>
<meta charset="utf-8" />
</head>

<body>
<?php
//Creating Tables

$servername = "localhost";
$username = "root";
$password = "";
$database = "webinternship";

//Connecting To Database

try {
	$conn = new PDO("mysql:host = $servername;dbname = $database", $username, $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	//Creating Tables using multiple execution statements
	$sql = "USE $database";
	$sql1 = "CREATE TABLE studentlogin (
		email VARCHAR(30) NOT NULL UNIQUE,
		username VARCHAR(20) NOT NULL UNIQUE,
		password VARCHAR(20) NOT NULL
		)";
	$sql2 = "CREATE TABLE companylogin (
		email VARCHAR(30) NOT NULL UNIQUE,
		username VARCHAR(20) NOT NULL UNIQUE,
		password VARCHAR(20) NOT NULL
		)";
	$sql3 = "CREATE TABLE studentprofile (
		username VARCHAR(20) NOT NULL UNIQUE,
		firstname VARCHAR(30) NOT NULL,
		lastname VARCHAR(30) NOT NULL,
		gender VARCHAR(10) NOT NULL,
		degree VARCHAR(50) NOT NULL,
		college VARCHAR(150) NOT NULL,
		fieldofstudy VARCHAR(50),
		fromyear YEAR,
		toyear YEAR,
		skills TEXT NOT NULL,
		photo VARCHAR(255) DEFAULT \"default.png\",
		state VARCHAR(25),
		city VARCHAR(25),
		phone DECIMAL(10,0)
		)";
	$sql4 = "CREATE TABLE companyprofile (
		username VARCHAR(20) NOT NULL UNIQUE,
		companyname VARCHAR(50) NOT NULL,
		website VARCHAR(50) NOT NULL,
		lineofbusiness VARCHAR(30) NOT NULL,
		established YEAR NOT NULL,
		addlocation VARCHAR(100) NOT NULL,
		addcity VARCHAR(25) NOT NULL,
		addstate VARCHAR(25) NOT NULL,
		hqlocation VARCHAR(100) NOT NULL,
		hqcity VARCHAR(25) NOT NULL,
		hqstate VARCHAR(25) NOT NULL,
		phone DECIMAL(10,0) NOT NULL,
		logo VARCHAR(255) NOT NULL,
		description TEXT
		)";
	$sql5 = "CREATE TABLE internships (
		internid INT PRIMARY KEY AUTO_INCREMENT,
		title VARCHAR(50) NOT NULL,
		username VARCHAR(20) NOT NULL,
		degree VARCHAR(50) NOT NULL,
		field VARCHAR(50),
		skills VARCHAR(255) NOT NULL,
		stipend INT,
		lastdate DATE NOT NULL,
		duration VARCHAR(10) NOT NULL,
		startdate DATE NOT NULL,
		description TEXT
		)";
	$sql6 = "CREATE TABLE appliedinternships (
		internid INT NOT NULL,
		title VARCHAR(50) NOT NULL,
		companyusername VARCHAR(20) NOT NULL,
		studentusername VARCHAR(20) NOT NULL,
		status VARCHAR(20) NOT NULL DEFAULT \"Pending\",
		FOREIGN KEY(internid) REFERENCES internships(internid)
		ON DELETE CASCADE
		)";
		
	//begin Transaction
	$conn->beginTransaction();
	$conn->exec($sql);
	$conn->exec($sql1);
	$conn->exec($sql2);
	$conn->exec($sql3);
	$conn->exec($sql4);
	$conn->exec($sql5);
	$conn->exec($sql6);

	//commit Transaction
	$conn->commit();
	echo "<h3>Tables Created Successfully</h3>";
}

catch(PDOException $e) {
	//rollback Transaction
	$conn->rollback();
	
	echo "<h3>ERROR : ".$e->getMessage()."</h3>";
}

?>

</body>
</html>