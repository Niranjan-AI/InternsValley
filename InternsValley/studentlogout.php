<?php
//Starting session
session_start();

//unset studentusername session variable
unset( $_SESSION["studentusername"] );
unset( $_SESSION["studentlogin"] );

//location change
header ( 'Location: studentlogin.php' );

?>
