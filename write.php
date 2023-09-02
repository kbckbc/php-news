<!DOCTYPE html>
<?php
// a page for writing a story
require 'comm.php';
require 'db.php';

// if not logged in, redirect to login page
$user = checkSession();

// when returning back to a list page, this value should be handed.
$pagePos = 0;
if( isset($_GET['page'])) {
	$pagePos = $_GET['page'];
}

// 'logout' button event
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['logOut']))
{
	session_destroy();
	header("Location:".$LIST_PHP);
	exit;
}
	
// handle 'write' button event
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['writeNews']))
{
	// to prevent CSRF attack
	if(!hash_equals($_SESSION['token'], $_POST['token'])){
		die("Request forgery detected");
	}		

	$id = $_POST['id'];
	$title = $_POST['title'];
	$url = $_POST['url'];
	$body = $_POST['body'];

	$msg = "";
	$msg .= checkInputText($title);
	$msg .= checkInputText($body);
	phpLog($DEBUG,sprintf("write.php, msg: [%s]", $msg));
	phpLog($DEBUG,sprintf("write.php, id: [%d]", $id));
	phpLog($DEBUG,sprintf("write.php, title: [%s]", $title));
	phpLog($DEBUG,sprintf("write.php, url: [%s]", $url));
	phpLog($DEBUG,sprintf("write.php, body: [%s]", $body));

	if(dbSetStory($title, $url, $body, $id)) {
		header("Location: list.php");
		exit; 
	}
}

$writeOrEdit = "Write";
// handle when editing a post
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['edit']))
{
	phpLog($DEBUG,sprintf("write.php, editing mode"));
	$writeOrEdit = "Edit";
}
?>
<html lang="en">
  <head>
    <title><?= htmlentities($TITLE_OF_THE_SITE)?> <?=$writeOrEdit?> page</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <link rel="stylesheet" href="fss.css">   
  </head>
  <body>
	<h1><a href='<?=$LIST_PHP?>'><?= htmlentities($TITLE_OF_THE_SITE)?> <?=$writeOrEdit?> page</a></h1>
	
	<?php 
		if(!$user) {
			printf("<button onclick=\"location.href='%s'\" type='button'>Back to List</button>"
			, htmlentities($LIST_PHP));
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

    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" >
		<input type="hidden" name="token" value="<?= $_SESSION['token']?>"> 
		<input type="hidden" name="id" value="<?= $_POST['id'] ?>">
	<article>
	  <header>
		<h3><?=$writeOrEdit?> a Post</h3>
	  </header>
      <label for="title">Title</label>
      <input type="text" id="title" name="title" value="<?= $_POST['title'] ?>" required>
      <label for="url">URL</label>
      <input type="url" id="url" name="url" value="<?= $_POST['url'] ?>" required>
      <label for="content">Content</label>
      
	  <textarea id="content" name="body" required >
		<?=$_POST['body']?>
	  </textarea>

	  <footer>
	  <div class="grid">
		<div><input type="reset" value="Clear"></div>
		<input type="submit" name="writeNews" value="Done">
	  </div>
	  </footer>	  
	</article>
	</form>
	
  </body>
</html>

