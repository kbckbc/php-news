<?php
// a page for managing all queries on db
require '../../dev/m3g/connect.php';


// get user info from db
// input: username 
// return: if user is in db, return the user info array, or return null
function dbGetUser($name) {
    global $DEBUG;

    $result = array();
    $sql = connDb();

    // Use a prepared statement
    $query = "SELECT id as userid, username, nickname, password, ctime, ltime FROM users WHERE username=?";
    $stmt = $sql->prepare($query);
	if(!$stmt){
        phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
        exit;
	}

    // Bind the parameter and execute
    $stmt->bind_param('s', $name);
    $stmt->execute();
	$res = $stmt->get_result();
	$stmt->close();

    if($res->num_rows) {
        $row = $res->fetch_assoc();
        $result['userid'] = $row['userid'];
        $result['username'] = $row['username'];
        $result['nickname'] = $row['nickname'];
        $result['password'] = $row['password'];
        $result['ctime'] = $row['ctime'];
        $result['ltime'] = $row['ltime'];
    }

    phpLog($DEBUG, sprintf("dbGetUser(), count:[%d]", $res->num_rows));
    phpLog($DEBUG, sprintf("dbGetUser(), result:[%s]", print_r($result,true)));
        
    return $result;
}


// insert a new user into a db. 
// input: user info
// return: if succ return true, or exit
function dbSetUser($username, $password, $nickname) {
    global $DEBUG;

    $sql = connDb();

    $stmt = $sql->prepare("insert into users (username, password, nickname, active, ctime, ltime)  values (?, ?, ?, 1, now(), now())");
    if(!$stmt){
        phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
        exit;
    }

    $stmt->bind_param('sss', $username, $password, $nickname);
    $stmt->execute();
    $stmt->close();

    phpLog($DEBUG, sprintf("dbSetUsername(), succ:[%s][%s][%s]",$username, $password, $nickname));

    return true;
}


// update the user's login time
// input: username
// return: if succ return true, or exit
function dbSetUserLoginTime($username) {
    global $DEBUG;

    $sql = connDb();

    $stmt = $sql->prepare("update users set ltime = now() where username = ?");
    if(!$stmt){
        phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
        exit;
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->close();

    phpLog($DEBUG, sprintf("dbSetUserLoginTime(), succ:[%s]",$username));

    return true;
}


// delete a comment of a story
// input: comment's id
// return: if succ return true, or exit
function dbDelComment($id) {
    global $DEBUG;

    $sql = connDb();

    if(!$id) {
        phpLog($DEBUG, sprintf("dbDelComment, id param needed"));
        return false;
    }
    else {
        // delete comments which is child of the story
        $stmt = $sql->prepare("delete from comments where id = ?");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
        $stmt->bind_param('d', $id);
        $stmt->execute();
    }

    $stmt->close();

    phpLog($DEBUG, sprintf("dbDelComment(), succ, id:[%d]",$id));

    return true;
}


// add or update a comment 
// input
// storyid: save or edit a comment. storyid is a parent of the comment
// body: is a content
// commentid: if there is a commentid, then update comment, if it's not, then add a comment
// return true if succeed
function dbSetComment($storyid, $body, $commentid=null) {
    global $DEBUG;

    $sql = connDb();

    if($commentid) {
        $stmt = $sql->prepare("update comments set body=?, mtime=now() where id=?");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
    
        $stmt->bind_param('sd', $body, $commentid);
    }
    else {
        $stmt = $sql->prepare("insert into comments (story_id, user_id, body, ctime, mtime) values (?, ?, ?, now(), now())");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
    
        $stmt->bind_param('dds', $storyid, $_SESSION['userid'],$body);
    }

    $stmt->execute();
    $stmt->close();

    phpLog($DEBUG, sprintf("dbSetComment(), succ:[%d][%d][%s]",$storyid, $_SESSION['userid'], $body));

    return true;
}


// get all comment's of a story
// input: story's id
// return: comments array
function dbGetComments($id) {
    global $DEBUG;

    $result = array();
	$sql = connDb();

	$query = "
        select a.id as comment_id
             , a.story_id
             , a.user_id
             , b.username
             , b.nickname
             , a.body
             , a.ctime
             , a.mtime 
        from comments a
           , users b 
       where a.user_id = b.id 
         and a.story_id = ?
       order by a.ctime desc
	";
	$stmt = $sql->prepare($query);
	if(!$stmt){
        phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
        exit;
	}

    // Bind the parameter and execute
    $stmt->bind_param('d', $id);
	$stmt->execute();
	$res = $stmt->get_result();
	$stmt->close();

	while ($res->num_rows && $row = $res->fetch_assoc()) {
        $result[] = $row;
	}
    phpLog($DEBUG, sprintf("dbGetComments(), count:[%d]", $res->num_rows));
    phpLog($DEBUG, sprintf("dbGetComments(), result:[%s]", print_r($result,true)));
    
    return $result;
}


// get all stories from a db
// return: how many stories are in db
function dbGetStoriesCount() {
    global $DEBUG;

    $result = 0;
	$sql = connDb();

	$query = "
		select count(*) as cnt
		from stories
	";
	$stmt = $sql->prepare($query);
	if(!$stmt){
        phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
        exit;
	}

    // Bind the parameter and execute
	$stmt->execute();
	$res = $stmt->get_result();
	$stmt->close();

    if($res->num_rows) {
        $row = $res->fetch_assoc();
        $result = $row['cnt'];
    }    

    phpLog($DEBUG, sprintf("dbGetStoriesCount(), count:[%d]", $res->num_rows));
    phpLog($DEBUG, sprintf("dbGetStoriesCount(), result:[%d]", $result));
    
    return $result;
}


// get stories from a db
// params for limit. from 0 to 5 will return 0,1,2,3,4 rows from stories
// since capacity of stories table is unsigned small int, so the limit number is 65535
// input
// from: the point where the stories are searched
// howmany: how many stories does this function get
// return: stories array
function dbGetStories($from=0, $howmany=65535) {
    global $DEBUG;

    $result = array();
	$sql = connDb();

	$query = "
		select a.id, a.title, a.body, a.url, a.ctime, a.mtime, b.username, b.nickname
		from stories a, users b
		where a.user_id = b.id
		order by a.ctime desc limit ". $from ."," . $howmany;
    phpLog($DEBUG, sprintf("dbGetStories(), query:[%s]", $query));

	$stmt = $sql->prepare($query);
	if(!$stmt){
        phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
        exit;
	}

    // Bind the parameter and execute
	$stmt->execute();
	$res = $stmt->get_result();
	$stmt->close();

	// while ($res->num_rows && $row = $res->fetch_assoc()) {
    while ($row = $res->fetch_assoc()) {
        $result[] = $row;
	}
    phpLog($DEBUG, sprintf("dbGetStories(), count:[%d]", $res->num_rows));
    phpLog($DEBUG, sprintf("dbGetStories(), result:[%s]", print_r($result,true)));
    
    return $result;
}


// get a story by id. it is called by a view page
// input: story id
// return: a story
function dbGetStory($id) {
    global $DEBUG;

    $result = null;
	$sql = connDb();

	$query = "
		select a.id, a.title, a.body, a.url, a.ctime, a.mtime, a.user_id, b.username, b.nickname
		from stories a, users b
		where a.user_id = b.id
          and a.id = ?
		order by a.ctime desc	
	";
	$stmt = $sql->prepare($query);
	if(!$stmt){
        phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
        exit;
	}

    // Bind the parameter and execute
    $stmt->bind_param('d', $id);
	$stmt->execute();
	$res = $stmt->get_result();
	$stmt->close();

    if($res->num_rows) {
        $row = $res->fetch_assoc();
        $result = $row;
    }

    phpLog($DEBUG, sprintf("dbGetStory(), count:[%d]", $res->num_rows));
    phpLog($DEBUG, sprintf("dbGetStory(), result:[%s]", print_r($result,true)));
    
    return $result;
}


// save a story
// input
// title, url, body: content of a story
// id: if set, then update a story, if null, save a new story
// return: true if succeed
function dbSetStory($title, $url, $body, $id = null) {
    global $DEBUG;

    $sql = connDb();

    if($id) {
        $stmt = $sql->prepare("update stories set title=?, body=?, url=?, mtime=now() where id = ?");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
    
        $stmt->bind_param('sssd', $title, $body, $url, $id);
    }
    else {
        $stmt = $sql->prepare("insert into stories (user_id, title, body, url, ctime, mtime)  values (?, ?, ?, ?, now(), now())");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
    
        $stmt->bind_param('dsss', $_SESSION['userid'], $title, $body, $url);
    }

    $stmt->execute();
    $stmt->close();

    phpLog($DEBUG, sprintf("dbSetStory(), succ:[%d][%d][%s][%s][%s]",$id, $_SESSION['userid'], $title, $body, $url));

    return true;
}


// delete a story including comments
// input: story id
// return: true if succeed
function dbDelStory($id) {
    global $DEBUG;

    $sql = connDb();

    if(!$id) {
        phpLog($DEBUG, sprintf("dbDelStory, id param needed"));
        return false;
    }
    else {
        // delete comments which are children of the story
        $stmt = $sql->prepare("delete from comments where story_id = ?");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
        $stmt->bind_param('d', $id);
        $stmt->execute();

        // delete story
        $stmt = $sql->prepare("delete from stories where id = ?");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
        $stmt->bind_param('d', $id);
        $stmt->execute();
    }

    $stmt->close();

    phpLog($DEBUG, sprintf("dbDelStory(), succ, id:[%d]",$id));

    return true;
}


// delete a account including all stories and comments
// input: user id
// return: true if succeed
function dbDelAccount($id) {
    global $DEBUG;

    $sql = connDb();

    if(!$id) {
        phpLog($DEBUG, sprintf("dbDelAccount, id param needed"));
        return false;
    }
    else {
        // delete comments which are children of the story
        $stmt = $sql->prepare("delete from comments where user_id = ?");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
        $stmt->bind_param('d', $id);
        $stmt->execute();

        // delete story 
        $stmt = $sql->prepare("delete from stories where user_id = ?");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
        $stmt->bind_param('d', $id);
        $stmt->execute();

        // delete user
        $stmt = $sql->prepare("delete from users where id = ?");
        if(!$stmt){
            phpLog($DEBUG, sprintf("Query Prep Failed: [%s]\n", $sql->error));
            exit;
        }
        $stmt->bind_param('d', $id);
        $stmt->execute();        
    }

    $stmt->close();

    phpLog($DEBUG, sprintf("dbDelAccount(), succ, id:[%d]",$id));

    return true;
}
?>
