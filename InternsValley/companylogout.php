<?php
//Starting session
session_start();

//unset studentusername session variable
unset( $_SESSION["companyusername"] );
unset( $_SESSION["companylogin"] );

//location change
header ( 'Location: companylogin.php' );

?>
