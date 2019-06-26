<?php

/*Creating Databases*/

/*Establishing Connection*/

$servername='localhost';
$username='root';
$password="";
$database="demowebinternship";

try {
	$conn=new PDO("mysql:host=$servername",$username,$password);
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	echo "<h3>Successfully Connected.</h3>";

	//Creating Database
	$sql = "CREATE DATABASE $database";
	$conn->exec($sql);
	echo "<h3>Database $database Created Successfully.</h3>";
}

catch(PDOException $e) {
	echo "<h3>$sql <br />".$e->getMessage()."</h3>";
}
?>

