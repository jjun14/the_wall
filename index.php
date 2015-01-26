<?php 

session_start();
session_destroy();

?>

<html>
  <head>
    <meta charset="utf-8">
    <title>The Wall: Login</title>
    <style>
      .container 
      {
        width: 500px;
        padding-top: 30px;
        margin-left: 30px;
      }
      .error
      {
        color: red;
        margin: 0px 0px 5px 0px
      }

      .success
      {
        color: green;
      }

      .button
      {
        background-color: #2f8aa8;
        width: 100px;
        font-size: 12px;
        height: 35px;
        padding: 10px 0px;
        margin: 20px 0px 0px 0px;
        border: none;
        border-radius: 3px;
        color: white;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h1>Welcome to the Wall</h1>
      <h2>Register</h2>
    <?php if(isset($_SESSION['regis-errors']))
          {
            foreach($_SESSION['regis-errors'] as $error)
            {
              echo "<p class='error'>{$error}</p>";
            }
            unset($_SESSION['regis-errors']);
          } 
          else if(isset($_SESSION['success']))
          {
            echo "<p class='success'>{$_SESSION['success']}</p>";
            unset($_SESSION['success']);
          }
          ?>
      <form action="process.php" method="post">
        First Name: <input type="text" name="first_name"><br>
        Last Name: <input type="text" name="last_name"><br>
        Email: <input type="text" name="email"><br>
        Password: <input type="password" name="password"><br>
        Confirm Password: <input type="password" name="confirm_password"><br>
        <input class="button" type="submit" value="register">
        <input type="hidden" name="action" value="register"><br>
      </form>
      <h2>Login</h2>
    <?php if(isset($_SESSION['login_errors']))
      {
        foreach($_SESSION['login_errors'] as $error)
        {
          echo "<p class='error'>{$error}</p>";
        }
        unset($_SESSION['login_errors']);
      } ?>
      <form action="process.php" method="post"><br>
        Email: <input type="text" name="email"><br>
        Password: <input type="password" name="password"><br>
        <input class="button" type="submit" value="login">
        <input type="hidden" name="action" value="login"><br>
      </form>
    </div>
  </body>
</html>

<?php 