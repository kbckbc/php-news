<?php
// a page for constant values and common functions


// global constant variables
$DEBUG = false;
$TITLE_OF_THE_SITE = 'World News Archive';

$VIEW_PHP = 'view.php';
$LOGIN_PHP = 'login.php';
$LIST_PHP = 'list.php';
$SIGNUP_PHP = 'signup.php';
$SIGNOUT_PHP = 'signout.php';
$MYPAGE_PHP = 'mypage.php';
$DELETE_ACCOUNT_PHP = 'deleteaccount.php';
$WRITE_PHP = 'write.php';
$DELETE_STORY_PHP = 'deletestory.php';
$WRITE_COMMENT_PHP = 'writecomment.php';
$DELETE_COMMENT_PHP = 'deletecomment.php';
$EDIT_COMMENT_PHP = 'editcomment.php';


// check whether user logged in or not using session
// input: autoexit is automatically redirect to login page or not
// return: 
// If user already login, return the user information
// or redirect to login.php page or just do nothing
function checkSession($autoexit = true) {
    session_start();
    
    if(!isset($_SESSION['userid'])) {
        if($autoexit) {
            header("Location: login.php");
            exit;  
        }
        else {
            return null;
        }
    }
    else {
        $user = array();
        $user['userid'] = $_SESSION['userid'];
        $user['username'] = $_SESSION['username'];
        $user['nickname'] = $_SESSION['nickname'];
        $user['ctime'] = $_SESSION['ctime'];
        $user['ltime'] = $_SESSION['ltime'];
        return $user;
    }
}

// check input user name validation
function checkInputText($username) {
    // Get the username and make sure that it is alphanumeric with limited other characters.
    // You shouldn't allow username with unusual characters anyway, but it's always best to perform a sanity check
    // since we will be concatenating the string to load files from the filesystem.
    $msg = "";
    if( !preg_match('/^[\w_\-@]+$/', $username) ){
        $msg = "Invalid input value|";
    }  
    return $msg;
}

// print out log in a php log file
function phpLog($debug, $log) {
    if($debug == true) {
        error_log('====================> '.$log, 0);
    }
}

// print out text in html
function htmlErrorMsg($msg) {
    $pieces = explode("|", $msg);
    foreach($pieces as $err) {
        if($err != null) {
            printf('<b>Error: %s</b><br>', htmlentities($err));
        }
    }
}


