<!DOCTYPE html>
<?php
// a page for editing a comment

require 'comm.php';
require 'db.php';

// if not logged in, redirect to login page
$user = checkSession();


if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['commentid'])) {
	// handle 'done' button event
	if(isset($_POST['editDone']))
	{
		// to prevent CSRF attack
		if(!hash_equals($_SESSION['token'], $_POST['token'])){
			die("Request forgery detected");
		}

		$storyid = $_POST['storyid'];
		$commentid = $_POST['commentid'];
		$body = $_POST['body'];
		$msg .= checkInputText($body);
		phpLog($DEBUG, sprintf("editcomment.php: storyid, commentid, msg [%d][%d][%s]",$storyid, $commentid, $msg));

		if(dbSetComment(0, $body, $commentid)) {
			phpLog($DEBUG,sprintf("editcomment.php, succ"));
			header("Location: ".$VIEW_PHP."?id=".$storyid );
			exit; 
		}
	}	
}
else {
	phpLog($DEBUG,sprintf("editcomment.php, post call fail, no param"));
	header("Location: " . $_SERVER["HTTP_REFERER"]);
	exit; 
}

?>
<html lang="en">
  <head>
    <title><?= htmlentities($TITLE_OF_THE_SITE)?> Comment Edit page</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <link rel="stylesheet" href="fss.css">   
  </head>
  <body>
  	<h1><a href='<?=$LIST_PHP?>'><?= htmlentities($TITLE_OF_THE_SITE)?> Comment Edit page</a></h1>

	<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
		<div class="grid">
			<div><button onclick="history.back()">Back to List</button></div>
		</div>
	</form>

    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" >
	<article>
	  <header><h3>Edit a comment</h3></header>
	  <input type="hidden" name="token" value="<?= $_SESSION['token']?>" > 
	  <input type="hidden" name="storyid" value="<?= $_POST['storyid'] ?>">
	  <input type="hidden" name="commentid" value="<?= $_POST['commentid'] ?>">
      <label for="content">Comment</label>
      <input type="text" id="content" name="body" value="<?= $_POST['body'] ?>" required>
	  <footer>
	  <div class="grid">
		<div><input type="reset" value="Clear"></div>
		<div><input type="submit" name="editDone" value="Done"></div>
	  </div>
	  </footer>
	</article>
    </form>
	
  </body>
</html>

