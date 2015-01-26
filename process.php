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
  $query = escape_this_string("SELECT  * FROM users WHERE email = '{$post['email']}'");
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
    $query = escape_this_string("INSERT INTO users(first_name, last_name, email, password, created_at, updated_at)
             VALUES ('{$post['first_name']}', '{$post['last_name']}',
            '{$post['email']}', '{$post['password']}', NOW(), NOW())");
    run_mysql_query($query);
    $user_info_query = escape_this_string("SELECT * FROM users WHERE users.email = '{$post['email']}'
           AND users.password = '{$post['password']}'");
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
  $query = escape_this_string("SELECT * FROM users WHERE users.email = '{$post['email']}'
           AND users.password = '{$post['password']}'");
  $user = fetch_all($query);
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
    $query = escape_this_string("INSERT INTO messages(user_id, message, created_at, updated_at)
             VALUES({$_SESSION['user_id']}, '{$_POST['message']}', NOW(), NOW())");
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
  $query = escape_this_string("DELETE FROM comments WHERE message_id = {$post['message_id']}");
  run_mysql_query($query);
  $query = escape_this_string("DELETE FROM messages WHERE user_id = {$_SESSION['user_id']} AND messages.id = {$_POST['message_id']}");
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
    $query = escape_this_string("INSERT INTO comments(message_id, user_id, comment, created_at, updated_at)
             VALUES('{$post['message_id']}',{$_SESSION['user_id']}, '{$post['comment']}', NOW(), NOW())");
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
  $query = escape_this_string("DELETE FROM comments WHERE id = {$post['comment_id']} AND user_id = {$_SESSION['user_id']}");
  run_mysql_query($query);
  header('location: wall.php');
  die();
}

?>