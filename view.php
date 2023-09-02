<!DOCTYPE html>
<?php
// a page for viewing a story
// it can have edit or delete button and viewing comments as well
require 'comm.php';
require 'db.php';

// if not logged in, redirect to login page
$user = checkSession(false);

$storyid = null;
$story = null;
$comments = null;
$pagePos = null;
// handle when recieve story id by GET
if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['id']))
{
	$storyid = $_GET['id'];
	phpLog($DEBUG, sprintf("view, storyid: [%d]",$storyid));

	$story = dbGetStory($storyid);
	phpLog($DEBUG,sprintf("view.php, story: [%s]", print_r($story,true)));

	$comments = dbGetComments($storyid);
	phpLog($DEBUG,sprintf("view.php, comments: [%s]", print_r($comments,true)));
	
	$pagePos = 0;
	if( isset($_GET['page'])) {
		$pagePos = $_GET['page'];
	}
}
?>
<html lang="en">
  <head>
    <title><?= htmlentities($TITLE_OF_THE_SITE)?> View page</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <link rel="stylesheet" href="fss.css">   
  </head>
  <body>
	  <h1><a href='<?=$LIST_PHP?>'><?= htmlentities($TITLE_OF_THE_SITE)?> View page</a></h1>

	<?php 
		if(!$user) {
			printf("<button onclick=\"location.href='%s?page=%d'\" type='button'>Back to List</button>"
			, htmlentities($LIST_PHP)
			, $pagePos);
		}
		else {
			printf("
			<div class='grid'>
			<div><button onclick=\"location.href='%s?page=%d'\" type='button'>Back to List</button></div>
			<div><button onclick=\"location.href='%s'\" type=\"button\">Hi, %s(%s)</button></div>
			<div>
				<form action=\"%s\" method='post' >
					<input type='submit' name='logOut' value='Logout' >
				</form>
			</div>
			</div>			
			",
			htmlentities($LIST_PHP),
			$pagePos,
			htmlentities($MYPAGE_PHP),
			htmlentities($user['nickname']),
			htmlentities($user['username']),
			htmlentities($WRITE_PHP),
			htmlentities($_SERVER['PHP_SELF']));

		}
	?>	

	<article>
	<?php
	// show file list
	if($story == null) {
		printf('<header>');
		printf('No news has been posted yet!<br>');
		printf('You can post a news by clicking Write button!');
		printf('</header>');
	}
	else {
		
		/* using table */
		printf("
			<header>
			<a target='_blank' href='%s'><h2>%s (click to the page)</h2></a>
			%s(%s), created:%s, modified:%s<br>
			</header>
			<textarea rows='10' readonly>
			%s
			</textarea>
			",
			htmlentities($story['url']),
			htmlentities($story['title']),
			htmlentities($story['nickname']),
			htmlentities($story['username']),
			htmlentities($story['ctime']),
			htmlentities($story['mtime']),
			htmlentities($story['body'])
		);

	}	
	?>
	
	<?php
		// show edit and delete button when author watch this page
		if( isset($story['user_id']) && isset($_SESSION['userid']) && $story['user_id'] == $_SESSION['userid']) {
			printf("
			<footer>
			<div class='grid'>
			<div>
				<form action='%s' method='post' >
				<input type='submit' name='edit' value='Edit' >
				<input type='hidden' name='id' value='%d'>
				<input type='hidden' name='title' value='%s'>
				<input type='hidden' name='url' value='%s'>
				<input type='hidden' name='body' value='%s'>
				</form>	
			</div>
			<div>
				<form action='%s' method='post' onsubmit=\"return confirm('Are you sure you want to delete?\\nAll comments also be gone!');\">
				<input type='hidden' name='token' value='%s'> 				
				<input type='submit' name='deletestory' value='Delete' >
				<input type='hidden' name='id' value='%d'>
				</form>	
			</div>
			</div>
			</footer>", 
				$WRITE_PHP, 
				$story['id'], 
				$story['title'], 
				$story['url'], 
				$story['body'], 
				$DELETE_STORY_PHP, 
				$_SESSION['token'],
				$story['id']);
		}	
	?>		
	</article>

	<article>
		<header>	
			<h3>Comments</h3>
		</header>
	<?php
	
	// show comments
	if(count($comments) == 0) {
		printf('No comment has been posted yet!<br>');
	}
	else {
		/* using table */
		printf("
			<table>
			<tr>
				<th>When</th>
				<th>Nickname</th>
				<th>Comment</th>
				<th>Action</th>
			</tr>
			");
		foreach($comments as $comment) {
		// show buttons only if login user is the same with the comment author.
		if( $comment['user_id'] == $user['userid']) {
			printf("
			<tr>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>
				<a href='#' role='button' onclick=\"document.getElementById('%s').submit();\">Edit</a>
				<a href='#' role='button' onclick=\"document.getElementById('%s').submit();\">Delete</a>					
				
		
				</td>
			</tr>
			<tr hidden>
				<td>
				<form id='%s' action=\"%s\" method='POST' >
				<input type='hidden' name='storyid' value='%d'>
				<input type='hidden' name='commentid' value='%d'>
				<input type='hidden' name='body' value='%s'>
				</form>
				
				<form id='%s' action=\"%s\" method='POST'>
				<input type='hidden' name='token' value='%s'> 
				<input type='hidden' name='commentid' value='%d'>
				</form>					
				</td>
				<td>
				</td>
				<td>
				</td>
				<td>
				</td>
			</tr>
			",
			htmlentities($comment['ctime']),
			htmlentities($comment['nickname'].'('.$comment['username'].')'),
			htmlentities($comment['body']),
			htmlentities('formEdit'.$comment['comment_id']),
			htmlentities('formDelete'.$comment['comment_id']),
			htmlentities('formEdit'.$comment['comment_id']),
			$EDIT_COMMENT_PHP,
			$comment['story_id'],
			$comment['comment_id'],
			$comment['body'],
			htmlentities('formDelete'.$comment['comment_id']),
			$DELETE_COMMENT_PHP,
			$_SESSION['token'],
			$comment['comment_id']
			);	
		}
		else {
			printf("
			<tr>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td></td>
			</tr>",
			htmlentities($comment['ctime']),
			htmlentities($comment['nickname'].'('.$comment['username'].')'),
			htmlentities($comment['body'])
			);	
		}
	
		}
		printf("</table>");
	}		
	?>

	<?php
	phpLog($DEBUG,sprintf("view.php, user: [%s]", print_r($user,true)));

		if($user) {
			printf("	
			<footer>
			<form action=\"%s\" method='POST' >
			    <input type='hidden' name='token' value='%s'> 			
				<input type='hidden' name='storyid' value=\"%d\">
				<label for='comment'>Leave your comment</label>
				<input type='text' name='comment' id='comment' required>
				<div class='grid'>
					<div><input type='submit' value='Comment'></div>
					<div><input type='reset' value='Clear'></div>
				</div>
			</form>
			</footer>"
			, htmlentities($WRITE_COMMENT_PHP)
			, $_SESSION['token']
			, $storyid);
		}
	?>

	</article>

	<?php 
		if(!$user) {
			printf("<button onclick=\"location.href='%s?page=%d'\" type='button'>Back to List</button>"
			, htmlentities($LIST_PHP)
			, $pagePos);
		}
		else {
			printf("
			<div class='grid'>
			<div><button onclick=\"location.href='%s?page=%d'\" type='button'>Back to List</button></div>
			<div><button onclick=\"location.href='%s'\" type=\"button\">Hi, %s(%s)</button></div>
			<div>
				<form action=\"%s\" method='post' >
					<input type='submit' name='logOut' value='Logout' >
				</form>
			</div>
			</div>			
			",
			htmlentities($LIST_PHP),
			$pagePos,
			htmlentities($MYPAGE_PHP),
			htmlentities($user['nickname']),
			htmlentities($user['username']),
			htmlentities($WRITE_PHP),
			htmlentities($_SERVER['PHP_SELF']));

		}
	?>	
  </body>
</html>

