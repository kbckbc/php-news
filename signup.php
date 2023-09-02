<!DOCTYPE html>
<?php
  // a page for a sign up
require 'comm.php';
require 'db.php';

$errorMsg = "";

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['signupUsername']))
{
  // 1. check username is in use
  $signupUsername = $_POST['signupUsername'];
  phpLog($DEBUG,'signup.php, signupUsername:'.$signupUsername);
  
  $errorMsg .= checkInputText($signupUsername);

  $user = dbGetUser($signupUsername);
  phpLog($DEBUG, sprintf("signup.php, user:[%s]",print_r($user,true)));
  // check the username is in use
  if($user['username'] != ""){
    $errorMsg .= sprintf('[%s] is in use! Try another name, please|', htmlentities($signupUsername));
  }
  else {
    $username = $_POST['signupUsername'];
    $password = password_hash($_POST['signupPassword'], PASSWORD_DEFAULT);
    $nickname = $_POST['signupNickname'];

    dbSetUser($username, $password, $nickname);

    $user = dbGetUser($username);

    if( $user['username'] ) {
      // set session and move to list page
      session_start();
      $_SESSION['userid'] = $user['userid'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['nickname'] = $user['nickname'];
      $_SESSION['ctime'] = $user['ctime'];
      $_SESSION['ltime'] = $user['ltime'];
      $_SESSION['token'] = bin2hex(random_bytes(32));

      header("Location:".$LIST_PHP);
      exit;           
    }
  }
}
?>
<html lang="en">
  <head>
    <title><?= htmlentities($TITLE_OF_THE_SITE)?> Sign up page</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <link rel="stylesheet" href="fss.css">    
  </head>

  <body>
    <h1><a href='<?=$LIST_PHP?>'><?= htmlentities($TITLE_OF_THE_SITE)?> Sign-up page</a></h1>
    <form name="myForm" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" >
      <label for="username">Username(Enter your email):</label>
      <input type="email" id="username" name="signupUsername" required>
      <label for="password">Password</label>
      <input type="password" id="password" name="signupPassword" required>
      <label for="nickname">Nickname</label>
      <input type="text" id="nickname" name="signupNickname" required>
      <div class="grid">
        <div><button onclick="location.href='<?= htmlentities($LOGIN_PHP)?>'" type="button">Back to Log in</button></div>
        <div><input type="submit" value="Sign up"></div>
      </div>
      <div class="grid">
        <div><button onclick="location.href='<?= htmlentities($LIST_PHP)?>'" type="button">Nah, just looking aroud</button></div>
      </div>      
    </form>
    <?php 
    if($errorMsg != "") {
      htmlErrorMsg($errorMsg);
    }
    ?>
  </body>
</html>
