<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Let's Play Black Jack</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="login_style.css">
    </head>
    <body>
<?php
    if(isset($_POST['player_name']) && !empty($_POST['player_name']))
    {
        $_SESSION['player_name'] = $_POST['player_name'];
        header("Location: blackjack.php");
    }
?>
        
        <div class="container">
            <img src="images/blackjack_header.jpg" alt="black jack background">
            <form method="post" id="login">
                <h2>Player name:</h2>
                <input type="text" name="player_name">
                <input type="submit" value="Enter">
                <h3>Please input your name.</h3>
            </form>
        </div>
    </body>
</html>