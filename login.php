<!DOCTYPE html>
<?php
// a page for a login 
require 'comm.php';
require 'db.php';

$loginUsername = "";
$loginSucc = 0;  

// handle form submit event
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['loginUsername']))
{
  $loginUsername = $_POST['loginUsername'];
  $loginPassword = $_POST['loginPassword'];

  $user = dbGetUser($loginUsername);

  // if succeed to retreive user information, then check password
  if( $user['username'] ) {
    $loginSucc = 1;
    $password = $user['password'];
    phpLog($DEBUG, 'login.php, pass1:'.$loginPassword);
    phpLog($DEBUG, 'login.php, pass2:'.$password);

    // when comparing a password with salted hash value, use password_verify function
    if($password != "" && password_verify($loginPassword, $password)){
      $loginSucc = 2;

      session_start();
      $_SESSION['userid'] = $user['userid'];
      $_SESSION['username'] = $user['username'];
      $_SESSION['nickname'] = $user['nickname'];
      $_SESSION['ctime'] = $user['ctime'];
      $_SESSION['ltime'] = $user['ltime'];
      $_SESSION['token'] = bin2hex(random_bytes(32));
      
      dbSetUserLoginTime($user['username']);
      
      header("Location:".$LIST_PHP);
      exit;            
    } 
  }

}    
phpLog($DEBUG, 'login.php page! loginUsername:'.$loginUsername);
?>   

<html lang="en">
  <head>
    <title><?= htmlentities($TITLE_OF_THE_SITE)?> Login page</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <link rel="stylesheet" href="fss.css">
  </head>
  <body >
    <h1><a href='<?=$LIST_PHP?>'><?= htmlentities($TITLE_OF_THE_SITE)?> Login page</a></h1>
    <form name="myForm" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST" >
      <label for="username">Username(Enter your email):</label>
      <input type="email" id="username" name="loginUsername" required>
      <label for="password">Password</label>
      <input type="password" id="password" name="loginPassword" required>
      <div class="grid">
        <div><input type="submit" value="Login"></div>
        <div><button onclick="location.href='<?= htmlentities($SIGNUP_PHP)?>'" type="button">Wanna join? Click here</button></div>
      </div>
      <div class="grid">
        <div><button onclick="location.href='<?= htmlentities($LIST_PHP)?>'" type="button">Nah, just looking aroud</button></div>
      </div>
      
    </form>
    <?php
      if($loginUsername == "") {
        printf('Please login.<br>');
      }
      else {
        if($loginSucc == 0) {
          printf("<b>There's no such user's email. Username: [%s]</b><br>", htmlentities($loginUsername));
        }
        else if($loginSucc == 1) {
          printf("<b>Password doesn't match. Username: [%s]</b><br>", htmlentities($loginUsername));
        }
        else {
          // Save loginUsername in SESSION
          // branch to a proper situation
          session_start();
          $_SESSION['loginUsername'] = $loginUsername;
          header("Location:".$LIST_PHP);
          exit;       
        }
      }
      ?>
  </body>
</html>
