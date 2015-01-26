<?php 

 session_start();

 require('new_connection.php');
 $messages_query = "SELECT messages.id, concat_ws(' ', first_name, last_name) AS name,
                    date_format(messages.created_at, '%M %D %Y | %I:%i %p') AS date,
                    message FROM messages 
                    JOIN users ON users.id = messages.user_id 
                    ORDER BY messages.created_at DESC";
 // echo $messages_query;
 // die();
 $messages = fetch_all($messages_query);
 // var_dump($_SESSION);

?>
<html>
  <head>
    <meta charset="utf-8">
    <title>The Wall</title>
    <style>
      * 
      {
       vertical-align: baseline;
       font-weight: inherit;
       font-family: inherit;
       font-size: 100%;
       padding: 0;
       margin: 0;
       border: 0 none;
       outline: 0;
       font-family: sans-serif;
      }

      .container
      {
        padding-bottom: 20px;
      }

      #top-bar
      {
        width: 780px;
        height: 20px;
        margin-bottom: 20px;
        padding: 14px 0px 10px 20px;
        background-color: #2f8aa8;
        color: white;
      }

      #top-bar *
      {
        display: inline-block;
      }

      h2
      {
        margin-right: 420px;
      }

      h5 
      {
        font-size: 12px;
      }

      a 
      {
        color: #00465B;
        font-size: 10px;
        font-weight: bold;
      }

      #content
      {
        width: 798px;
        height: 1000px;
        padding-left: 75px;
      }

      #post-form 
      {
        margin-bottom: 20px;
      }

      .error {
        color: red;
        margin: 5px 0px 5px 0px;
        font-size: 10px;
      }

      .success {
        color: green;
        margin: 5px 0px 5px 0px;
        font-size: 10px;
      }

      textarea 
      {
        overflow: scroll;
        border: 3px solid black;
        display: block;
        font-size: 12px;
        padding: 5px 0px 0px 5px;
      }

      #post-form {
        margin-bottom: 10px;
        padding: 5px 0px 0px 5px;
      }

      .post-button, .comment-button
      {
        height: 30px;
        padding: 10px 0px;
        margin: 20px 0px 0px 375px;
        border: none;
        border-radius: 3px;
        color: white;
        margin-bottom: 20px;
      }

      .post-button      
      {
        background-color: #2f8aa8;
        width: 200px;
        font-size: 14px;
      }
      .comment-button      
      {
        background-color: #61B571;
        width: 155px;
        font-size: 11px;
      }

      #messages h5
      {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 15px;
      }
      
      .post {
        font-size: 12px;
        margin-bottom: 20px;
        display: block;
        width: 580px;
        padding-left: 20px;
      }

      h6
      {
        font-size: 10px;
        font-weight: bold;
        margin: 0px 0px 10px 19px;
      }

      .comment-form 
      {
        margin-left: 30px;
      }

      .comment {
        display: block;
        width: 500px;
        margin: 5px 0px 10px 10px;
        padding-left: 20px;
        font-size: 10px;
      }


    </style>
  </head>
  <body>
    <div id="top-bar">
      <h2>CodingDojo Wall</h2>
      <h5>Welcome, <?= $_SESSION['first_name']; ?>!</h5>
      <a href="process.php">(Logout)</a>
    </div>
    <div id="content">
      <h4>Post A Message</h4>
    <?php 
        if(isset($_SESSION['message-errors']))
        {
          foreach($_SESSION['message-errors'] as $error)
          {
            echo "<p class='error'>{$error}</p>";
          }
            unset($_SESSION['message-errors']);
        }
        else if (isset($_SESSION['success']))
        {
          echo "<p class='success'>{$_SESSION['success']}</p>";
          unset($_SESSION['success']);
        }
     ?>
      <form id="post-form" action="process.php" method="post">
        <textarea name="message" cols="87" rows="3" placeholder="Your message goes here..."></textarea>
        <input class="post-button" type="submit" value="Post a message">
        <input type="hidden" name="action" value="post">
      </form>
      <div id="messages">
      <?php 
            foreach ($messages as $message)
            {
              $message_id = intval($message["id"]);
              echo "<h5>{$message['name']} - {$message['date']}</h5>";
              echo "<p class='post'>{$message['message']}</p>";
              $comment_query = "SELECT concat_ws(' ', users.first_name, users.last_name) AS name,
                                date_format(comments.created_at, '%M %D %Y | %I:%i %p') AS date,
                                comments.comment FROM comments
                                LEFT JOIN users ON users.id = comments.user_id
                                LEFT JOIN messages ON messages.id = comments.message_id
                                WHERE message_id = {$message_id}
                                ORDER BY comments.created_at ASC";
              $comments = fetch_all($comment_query);
              foreach($comments as $comment)
              {
                echo "<h6>{$comment['name']} - {$comment['date']}</h5>";
                echo "<p class='comment'>{$comment['comment']}</p>";
              }
            ?>
        <form id="comment-form" class="comment-form" action="process.php" method="post">
          <textarea name="comment" cols="80" rows="3" placeholder="Your comment goes here..."></textarea>
          <input class="comment-button" type="submit" value="Post a comment">
          <input type="hidden" name="action" value="comment">
          <input type="hidden" name="message_id" value="<?= $message_id;?>">
        </form>
            
      <?php } ?>
      </div>
    </div>
  </body>
</html>