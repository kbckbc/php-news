<!DOCTYPE html>
<?php
// a page for a listing news page

require 'comm.php';
require 'db.php';

// if not logged in, redirect to login page
$user = checkSession(false);


// 'logout' button event
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['logOut']))
{
	session_destroy();
	header("Location:".$LIST_PHP);
	exit;
}


// for pagination, calculate related variables
$pagePos = 0;
if($_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['page']))
{
	$pagePos = $_GET['page'];
}

$storiesCnt = dbGetStoriesCount();
phpLog($DEBUG,sprintf("list.php, count: [%d]", $storiesCnt));

$totalCnt = $storiesCnt;
$pageSize = 5;
$pageCnt = ceil($totalCnt / $pageSize);

if( $pagePos < 0 ) {
	$pagePos = 0;
}
else if( $pagePos != 0 && $pagePos >= $pageCnt ) {
	$pagePos = $pageCnt - 1;
}


phpLog($DEBUG,sprintf("list.php, totalCnt: [%d]", $totalCnt));
phpLog($DEBUG,sprintf("list.php, pageSize: [%d]", $pageSize));
phpLog($DEBUG,sprintf("list.php, pageCnt: [%d]", $pageCnt));
phpLog($DEBUG,sprintf("list.php, pagePos: [%d]", $pagePos));

$blockSize = 5;
$blockCnt = ceil($pageCnt / $blockSize);
$blockPos = floor($pagePos/$blockSize);

phpLog($DEBUG,sprintf("list.php, blockSize: [%d]", $blockSize));
phpLog($DEBUG,sprintf("list.php, blockCnt: [%d]", $blockCnt));
phpLog($DEBUG,sprintf("list.php, blockPos: [%d]", $blockPos));


$blockStart = ($blockPos * $blockSize);
$blockEnd = ($blockPos * $blockSize) + $blockSize;
phpLog($DEBUG,sprintf("list.php, blockStart: [%d]", $blockStart));
phpLog($DEBUG,sprintf("list.php, blockEnd: [%d]", $blockEnd));

if( $blockEnd >= $pageCnt ) {
	$blockEnd = $pageCnt;
}

$stories = dbGetStories($pagePos*$pageSize, $pageSize);


?>
<html lang="en">
  <head>
    <title><?= htmlentities($TITLE_OF_THE_SITE)?> List page</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <link rel="stylesheet" href="fss.css">   
  </head>
  <body>
    <h1><a href='<?=$LIST_PHP?>'><?= htmlentities($TITLE_OF_THE_SITE)?> List page</a></h1>
	<?php 
		if(!$user) {
			printf("<button onclick=\"location.href='%s'\" type='button'>Go to login page to write a post or comment!</button>", htmlentities($LOGIN_PHP));
		}
		else {
			printf("
			<div class='grid'>
			<div><button onclick=\"location.href='%s?page=%d'\" type=\"button\">Write a news</button></div>
			<div><button onclick=\"location.href='%s'\" type=\"button\">Hi, %s(%s)</button></div>
			<div>
				<form action=\"%s\" method='post' >
					<input type='submit' name='logOut' value='Logout' >
				</form>
			</div>
			</div>			
			",
			htmlentities($WRITE_PHP),
			$pagePos,
			htmlentities($MYPAGE_PHP),
			htmlentities($user['nickname']),
			htmlentities($user['username']),
			htmlentities($_SERVER['PHP_SELF']));

		}
	?>

	
	<?php

	// show file list
	if(count($stories) == 0) {
		printf('<blockquote>');
		printf('No news has been uploaded yet!<br>');
		printf('You can upload a news by clicking Upload button!');
		printf('</blockquote>');
	}
	else {
		// for pagination
		printf("<div class='center'>");
		if( $blockPos == 0) {
			printf(" <a href='#' role='button' class='outline'>prev</a> ");
		}
		else if( $blockPos > 0) {
			printf(" <a href='list.php?page=%d' role='button'>prev</a> ", $blockStart - 1);
		}

		for($i=$blockStart; $i<$blockEnd; $i++) {
			if( $i == $pagePos) {
				printf("<a href='#' role='button' >%d</a> ", ($i + 1));
			}
			else{
				printf("<a href='list.php?page=%d' role='button' class='outline'>%d</a> ", $i, ($i + 1));
			}
		}
		
		if( $blockPos < $blockCnt -1) {
			printf("<a href='list.php?page=%d' role='button' >next</a>", $blockEnd);
		}
		else {
			printf("<a href='#' role='button' class='outline' >next</a>");
		}
		printf("</div>");

		/* using table */
		printf("
			<br>
			<table>
			<tr>
				<th>Title(Click to view)</th>
				<th>Link</th>
				<th>Author</th>
				<th>Post time</th>
				<th>Content</th>
				
			</tr>
			");
		foreach($stories as $story) {
			printf("
			<tr>
				<td><a href='%s?id=%s&page=%d'><b>%s</b></a></td>
				<td><a href='%s' target='_blank'><b>Go</b></a></td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
			</tr>",
			htmlentities($VIEW_PHP),
			htmlentities($story['id']),
			htmlentities($pagePos),
			htmlentities($story['title']),
			htmlentities($story['url']),
			htmlentities($story['username']),
			htmlentities($story['ctime']),
			htmlentities($story['body'])
			);		
		}
		printf("</table>");
	}		
	?>
  </body>
</html>

