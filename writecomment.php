<?php
// a page for adding a comment

require 'comm.php';
require 'db.php';

// if not logged in, redirect to login page
$username = checkSession();

// handle 'write' button event
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['comment']))
{
	// to prevent CSRF attack
	if(!hash_equals($_SESSION['token'], $_POST['token'])){
		die("Request forgery detected");
	}
			
	$storyid = $_POST['storyid'];
	$commentid = $_POST['commentid'];
	$comment = $_POST['comment'];
	$msg .= checkInputText($comment);

	phpLog($DEBUG, sprintf("writecomment.php: storyid, commentid, msg [%d][%d][%s]",$storyid, $commentid, $msg));

	if(dbSetComment($storyid, $comment)) {
		phpLog($DEBUG,sprintf("writecomment.php, succ"));
		header("Location: " . $_SERVER["HTTP_REFERER"]);
		// header("Location: list.php");
		exit; 
	}
}
?>