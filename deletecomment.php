<?php
// a page for deleting a comment
require 'comm.php';
require 'db.php';

// if not logged in, redirect to login page
$user = checkSession();

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['commentid']))
{
	// to prevent CSRF attack
	if(!hash_equals($_SESSION['token'], $_POST['token'])){
		die("Request forgery detected");
	}
	
	$commentid = $_POST['commentid'];
	phpLog($DEBUG,sprintf("deletecomment.php, commentid: [%d]", $commentid));

	if(dbDelComment($commentid)) {
		phpLog($DEBUG,sprintf("deletecomment.php, succ"));
		header("Location: " . $_SERVER["HTTP_REFERER"]);
		exit; 
	}
}
?>