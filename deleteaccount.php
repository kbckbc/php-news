<?php
// a page for deleting an account 
require 'comm.php';
require 'db.php';

// if not logged in, redirect to login page
$user = checkSession();

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['deleteaccount']))
{
	// to prevent CSRF attack
	if(!hash_equals($_SESSION['token'], $_POST['token'])){
		die("Request forgery detected");
	}

	phpLog($DEBUG, sprintf("deleteaccount, user: [%s]",print_r($user,true)));
	
	if( dbDelAccount($user['userid']) ) {
		
		session_destroy();
		header("Location:".$LIST_PHP);
		exit;    
	}
}
?>
