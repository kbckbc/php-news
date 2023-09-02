<?php
// a page for deleting a story including comments
require 'comm.php';
require 'db.php';

// if not logged in, redirect to login page
$user = checkSession();

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['deletestory']))
{
	// to prevent CSRF attack
	if(!hash_equals($_SESSION['token'], $_POST['token'])){
		die("Request forgery detected");
	}
		
	$id = $_POST['id'];
	phpLog($DEBUG, sprintf("deletestory, id: [%d]",$id));

	if( dbDelStory($id) ) {
		header("Location:".$LIST_PHP);
		exit;    
	}
}
?>
