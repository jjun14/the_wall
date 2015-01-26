<?php 

session_start();

require('new_connection.php');

if(isset($_POST['action']) && $_POST['action'] == "register")
{
  register_user($_POST);
}
else if(isset($_POST['action']) && $_POST['action'] == "login")
{
  login_user($_POST);
}
else if(isset($_POST['action']) && $_POST['action'] == "post")
{
  post_message($_POST);
}
else if(isset($_POST['action']) && $_POST['action'] == "comment")
{
  post_comment($_POST);
}
else if(isset($_POST['action']) && $_POST['action'] == "delete-message")
{
  delete_message($_POST);
}
else if(isset($_POST['action']) && $_POST['action'] == "delete-comment")
{
  delete_comment($_POST);
}
else 
{
  session_destroy();
  header("location: index.php");
}


function register_user($post)
{
  ///-----------------start register validations---------------------///
  $errors = array();
  if(empty($post['first_name']))
  {
    $errors[] = "First name cannot be blank";
  }
  if(!ctype_alpha($post['first_name']))
  {
    $errors[] = "First name must only contain letters";
  }
  if(empty($post['last_name']))
  {
    $errors[] = "Last name cannot be blank";
  }
  if(!ctype_alpha($post['last_name']))
  {
    $errors[] = "Last name must only contain letters";
  }
  if(empty($post['email']))
  {
    $errors[] = "Email cannot be blank";

  }
  if(!filter_var($post['email'], FILTER_VALIDATE_EMAIL))
  {
    $errors[] = "Email format is not valid";
  }
  if(empty($post['password']))
  {
    $errors[] = "Password cannot be blank";
  }
  if(strlen($post['password']) < 6)
  {
    $errors[] = "Password must be at least 6 characters";
  }
  if($post['password'] != $post['confirm_password'])
  {
    $errors[] = "Password and password confirmation don't match";
  } 
  $escape_email = escape_this_string($post['email']);
  $query = "SELECT  * FROM users WHERE email = '{$escape_email}'";
  $emails = fetch_all($query);
  if(count($emails) > 0)
  {
    $errors[] = "That email already exists, please choose a unique one!";
  }
  ///-----------------end register validations---------------------///
  if(count($errors) > 0)
  {
    $_SESSION['regis-errors'] = $errors;
    header("location: index.php");
    die();
  }
  else
  {
    $escape_firstname = $post['first_name'];
    $escape_lastname = $post['last_name'];
    $escape_email = $post['email'];
    $escape_password = $post['password'];
    $query = "INSERT INTO users(first_name, last_name, email, password, created_at, updated_at)
             VALUES ('{$escape_firstname}', '{$post['last_name']}',
            '{$escape_email}', '{$escape_password}', NOW(), NOW())";
    run_mysql_query($query);
    $user_info_query = "SELECT * FROM users WHERE users.email = '{$escape_email}'
           AND users.password = '{$escape_password}'";
    $user = fetch_record($user_info_query);
    $_SESSION['success'] = "User created succesfully";
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['first_name'] = $post['first_name'];
    $_SESSION['logged_in'] = TRUE;
    header("location: wall.php");
    die();
  }
}

function login_user($post)
{
  $errors = array();
  $escape_email = escape_this_string($post['email']);
  $escape_password = escape_this_string($post['password']);
  $query = "SELECT * FROM users WHERE users.email = '{$escape_email}'
           AND users.password = '{$escape_password}'";
  // echo $query;
  // die();
  $user = fetch_all($query);
  // var_dump($user);
  // die();
  ///-----------------start register validations---------------------///
  if(count($user) > 0)
  {
    $_SESSION['success'] = "Succesfully logged";
    $_SESSION['user_id'] = $user[0]['id'];
    $_SESSION['first_name'] = $user[0]['first_name'];
    $_SESSION['logged_in'] = TRUE;
    header("location: wall.php");
  }
  else 
  {
    $errors[] = "That password and combination did not match";
    $_SESSION['login_errors'] = $errors;
    header("location: index.php");
    die();
  }
  ///-----------------end register validations---------------------///
}

function post_message($post)
{ 
  $errors = array();
  if(empty($post['message']))
  {
    $errors[] = "Your post can't be empty!";
  }
  else
  {
    $escape_userid = escape_this_string($_SESSION['user_id']);
    $escape_post = escape_this_string($_POST['message']);
    $query = "INSERT INTO messages(user_id, message, created_at, updated_at)
             VALUES({$escape_userid}, '{$escape_post}', NOW(), NOW())";
    run_mysql_query($query);
    $_SESSION['success'] = "Successfully posted a message!";
    header("location: wall.php");
  }
  if(count($errors) > 0)
  {
    $_SESSION['message-errors'] = $errors;
    header("location: wall.php");
    die();
  }
}

function delete_message($post)
{
  $escape_messageid = escape_this_string($post['message_id']);
  $query = "DELETE FROM comments WHERE message_id = {$escape_messageid}";
  run_mysql_query($query);
  $escape_userid = $_SESSION['user_id'];
  $escape_messageid = $_POST['message_id'];
  $query = "DELETE FROM messages WHERE user_id = {$escape_userid} AND messages.id = {$escape_messageid}";
  run_mysql_query($query);
  header("location: wall.php");
  die();
}

function post_comment($post)
{
  $errors = array();
  if(empty($post['comment']))
  {
    $errors[] = "Your comment can't be empty!";
  } else {
    $escape_messageid = $post['message_id'];
    $escape_userid = $_SESSION['user_id'];
    $escape_comment = $post['comment'];
    $query = "INSERT INTO comments(message_id, user_id, comment, created_at, updated_at)
             VALUES('{$escape_messageid}',{$escape_userid}, '{$escape_comment}', NOW(), NOW())";
    run_mysql_query($query);
    header("location: wall.php");
  }
  if(count($errors) > 0)
  {
    header("location: wall.php");
    die();
  }
}

function delete_comment($post)
{
  $escape_commentid = $post['comment_id'];
  $escape_userid = $_SESSION['user_id'];
  $query = "DELETE FROM comments WHERE id = {$escape_commentid} AND user_id = {$escape_userid}";
  run_mysql_query($query);
  header('location: wall.php');
  die();
}

?>