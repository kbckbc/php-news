<!DOCTYPE html>
<?php
// a page for my information

require 'comm.php';
require 'db.php';

// if not logged in, redirect to login page
$user = checkSession();


// handle 'logout' button event
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['logOut']))
{
	session_destroy();
	header("Location:".$LIST_PHP);
	exit;
}
?>
<html lang="en">
  <head>
    <title><?= htmlentities($TITLE_OF_THE_SITE)?> My page</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <link rel="stylesheet" href="fss.css">   
  </head>
  <body>
  	<h1><a href='<?=$LIST_PHP?>'><?= htmlentities($TITLE_OF_THE_SITE)?> My page</a></h1>

	<div class="grid">
		<div><button onclick="history.back()">Go Back</button></div>
		<div>
			<form action="<?= htmlentities($DELETE_ACCOUNT_PHP) ?>" method="post" onsubmit="return confirm('Are you sure you want to delete your account?\nAll posts and comments also be gone!');">
				
				<input type="submit" name="deleteaccount" value="Delete account"  >
			</form>
		</div>
		<div>
			<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" >
				<input type="submit" name="logOut" value="Logout" >
			</form>
		</div>
	</div>
	

	
	<?php

	/* using table */
	printf("
		<article>
		<h2>Username: %s</h2>
		<ul>
			<li>Nickname: %s</li>
			<li>Join date: %s</li>
			<li>Last login date: %s</li>
		</ul>

		</article>",
		htmlentities($_SESSION['username']),
		htmlentities($_SESSION['nickname']),
		htmlentities($_SESSION['ctime']),
		htmlentities($_SESSION['ltime'])
	);	
	?>
  </body>
</html>

