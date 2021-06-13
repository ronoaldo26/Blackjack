<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Let's play BlackJack</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="blackjack_style.css">
    </head>
    <body>
<?php
        if(!isset($_SESSION['money']) && empty($_SESSION['money']))
        {
            $_SESSION['money'] = 5000;
            $_SESSION['game'] = "start";
        } ?>

        <div class="container">
            <div class="rules_table">
                <h4>Basic Blackjack Rules:</h4>
                <ul>
                    <li>The goal of blackjack is to beat the dealer's hand without going over 21.</li>
                    <li>Face cards are worth 10. Aces are worth 1 or 11, whichever makes a better hand.</li>
                    <li>Each player starts with two cards, one of the dealer's cards is hidden until the end.</li>
                    <li>To 'Hit' is to ask for another card. To 'Stand' is to hold your total and end your turn.</li>
                    <li>If you go over 21 you bust, and the dealer wins regardless of the dealer's hand.</li>
                    <li>If you are dealt 21 from the start (Ace & 10), you got a blackjack.
                        (Blackjack usually means you win x1.5 the amount of your bet.)</li>
                    <li>Dealer will hit until cards total is 17 or higher.</li>
                    <li>Doubling is like a hit, only the bet is doubled and you only get one more card.</li>
                    <li>Split can be done when you have two of the same card - the pair is split into two hands.</li>
                    <li>Splitting also doubles the bet, because each new hand is worth the original bet.</li>
                    <li>You can only double/split on the first move, or first move of a hand created by a split.</li>
                    <li>You cannot play on two aces after they are split.</li>
                    <li>You can double on a hand resulting from a split, tripling or quadrupling your bet.</li>
                </ul>
            </div>
            <div class="play_table">
                <!-- DEALER -->
                <div class="dealer">
                    <h2>Dealer</h2>
                    <div class="dealer_hand">
<?php               if(isset($_SESSION['dealer_value']) && !empty($_SESSION['dealer_value']))
                    {
                        echo "<p>" . $_SESSION['dealer_value'] . "</p>";
                    }

                    if(isset($_SESSION['dealer_hand']) && !empty($_SESSION['dealer_hand']))
                    {
                        for($y = 0; $y < count($_SESSION['dealer_hand']); $y++)
                        {
                            if($y == 1)
                            {
                                if($_SESSION['game'] == "Hit" || $_SESSION['game'] == "first_deal" || 
                                $_SESSION['game'] == "black_jack" || $_SESSION['game'] == "bust" || 
                                $_SESSION['game'] == "Split" || $_SESSION['game'] == "Surrender")
                                { ?>
                                    <img src="deck/b1fv.png" alt="Dealer Card" style="margin-left: 55px;">
<?php                           }
                                else
                                { ?>
                                    <img src="deck/<?= $_SESSION['dealer_hand'][$y] ?>.png" alt="Dealer Card">
<?php                           }
                            }
                            else
                            { ?>
                                <img src="deck/<?= $_SESSION['dealer_hand'][$y] ?>.png" alt="Dealer Card">
<?php                       }
                        }
                    } ?>
                    </div>
                </div>
                <!-- GAME MESSAGE -->
                <div class="game_message">
<?php               if(isset($_SESSION['win_check_message']) && !empty($_SESSION['win_check_message']))
                    {
                        echo "<h1>" . $_SESSION['win_check_message'] . "</h1>";
                    }
                    if(isset($_SESSION['game']) && !empty($_SESSION['game']))
                    {
                        if($_SESSION['game'] == "Surrender" || $_SESSION['game'] == "Stand" || 
                        $_SESSION['game'] == "black_jack" || $_SESSION['game'] == "bust")
                        { ?>
                            <form action="process.php" method="post" class="play_again">
                                <input type="submit" name="game" value="Deal Again">
                            </form>
<?php                   }
                    }
                    if(isset($_SESSION['game_split']) && !empty($_SESSION['game_split']))
                    {
                        if($_SESSION['game_split'] == "stand")
                        { ?>
                            <form action="process.php" method="post" class="play_again">
                                <input type="submit" name="game" value="Deal Again">
                            </form>
<?php                   }
                    } ?>
                </div>
                <!-- PLAYER -->
                <div class="player">
<?php               if($_SESSION['game'] == "Split" || $_SESSION['game'] == "Split_Stand")
                    { ?>
                        <div class="player_hand_split">
<?php                       if(isset($_SESSION['player_value']) && !empty($_SESSION['player_value']))
                            {
                                echo "<p>" . $_SESSION['player_value'] . "</p>";
                            }

                            if(isset($_SESSION['player_hand']) && !empty($_SESSION['player_hand']))
                            {
                                for($x = 0; $x < count($_SESSION['player_hand']); $x++)
                                { ?>
                                    <img src="deck/<?= $_SESSION['player_hand'][$x] ?>.png" alt="Player Card">
<?php                           }
                            }?>
                        </div>
                        <div class="player_hand_split">
<?php                       if(isset($_SESSION['player_value_split']) && !empty($_SESSION['player_value_split']))
                            {
                                echo "<p>" . $_SESSION['player_value_split'] . "</p>";
                            }

                            if(isset($_SESSION['player_hand_split']) && !empty($_SESSION['player_hand_split']))
                            {
                                for($x = 0; $x < count($_SESSION['player_hand_split']); $x++)
                                { ?>
                                    <img src="deck/<?= $_SESSION['player_hand_split'][$x] ?>.png" alt="Player Card">
<?php                           }
                            }
                        echo "</div>";
                    }
                    else
                    { ?>
                        <div class="player_hand">
<?php                   if(isset($_SESSION['player_value']) && !empty($_SESSION['player_value']))
                        {
                            echo "<p>" . $_SESSION['player_value'] . "</p>";
                        }

                        if(isset($_SESSION['player_hand']) && !empty($_SESSION['player_hand']))
                        {
                            for($x = 0; $x < count($_SESSION['player_hand']); $x++)
                            { ?>
                                <img src="deck/<?= $_SESSION['player_hand'][$x] ?>.png" alt="Player Card">
<?php                       }
                        }
                        echo "</div>";
                    } ?>

                    <h2>Player: <?= $_SESSION['player_name'] ?></h2>
                </div>
            </div>
            
            <!-- BETTING CHIPS -->
            <div class="bet_table">
                <form action="process.php" method="post" class="reset_game">
                    <input type="submit" name="game" value="RESET">
                    <input type="submit" name="game" value="LOG-OUT">
                </form>
                
                <div class="bet">
<?php               if(isset($_SESSION['bet']) && !empty($_SESSION['bet']))
                    {
                        echo "<p>Bet Amount:</p>";
                        echo "<h3>₱ " . number_format($_SESSION['bet'], 2) . "</h3>";
                    }
                    else
                    {
                        echo "<p>Bet Amount:</p>";
                        echo "<h3>₱ 0.00</h3>";
                    } 

                    if(isset($_SESSION['game']) && !empty($_SESSION['game']))
                    {
                        if($_SESSION['game'] == "Surrender" || $_SESSION['game'] == "Stand" || $_SESSION['game'] == "start")
                        { ?>
                            <form action="process.php" method="post" class="change_bet">
                                <input type="submit" name="game" value="CHANGE">
                            </form>
<?php                   }
                    }
                echo "</div>";
                
                if($_SESSION['game'] == "start")
                { ?>
                    <form action="process.php" method="post" class="chips">
                        <h3>Place your bet:</h3>
                        <input type="submit" name="bet" value="1" class="one">
                        <input type="submit" name="bet" value="5" class="five">
                        <input type="submit" name="bet" value="10" class="ten">
                        <input type="submit" name="bet" value="50" class="fifty">
                        <input type="submit" name="bet" value="100" class="one_hundred">
                        <input type="submit" name="bet" value="500" class="five_hundred">
                    </form>
<?php           } ?>

                <div class="bet">
                    <p>Available Balance:</p>
                    <h3>₱ <?= number_format($_SESSION['money'], 2) ?></h3>
                </div>
            </div>

            <!-- GAMEPLAY -->
            <div class="game_table">
<?php
                if(isset($_SESSION['bet']) && !empty($_SESSION['bet']))
                {
                    if($_SESSION['game'] == "start")
                    { ?>
                        <form action="process.php" method="post" class="start">
                            <input type="submit" name="game" value="DEAL">
                        </form>
<?php           }
                    if($_SESSION['game'] == "first_deal")
                    { ?>
                        
                            <form action="process.php" method="post" class="first_deal">
                                <input type="submit" name="game" value="Hit">
                                <input type="submit" name="game" value="Stand">
                                <input type="submit" name="game" value="Double">
<?php                           if(isset($_SESSION['game_split']) && !empty($_SESSION['game_split']) && 
                                $_SESSION['game_split'] == "duplicate")
                                { ?>
                                    <input type="submit" name="game" value="Split">
<?php                           } ?>
                                <input type="submit" name="game" value="Surrender">
                            </form>
<?php               }
                    elseif($_SESSION['game'] == "Hit")
                    { ?>
                        <form action="process.php" method="post" class="hit">
                            <input type="submit" name="game" value="Hit">
                            <input type="submit" name="game" value="Stand">
                            <input type="submit" name="game" value="Surrender">
                        </form>
<?php               }
                    elseif($_SESSION['game'] == "Split")
                    {
                        if($_SESSION['game_split'] == "first_hand")
                        {   ?>
                            <form action="process.php" method="post" class="split_first">
                                <input type="hidden" name="game" value="Split">
                                <input type="submit" name="game_split_first" value="Hit">
                                <input type="submit" name="game_split_first" value="Stand">
                                <input type="submit" name="game_split_first" value="Surrender">
                                <p>1st Hand</p>
                            </form>
<?php                   }
                        elseif($_SESSION['game_split'] == "second_hand")
                        { ?>
                            <form action="process.php" method="post" class="split_second">
                                <p>2nd Hand</p>
                                <input type="hidden" name="game" value="Split">
                                <input type="submit" name="game_split_second" value="Hit">
                                <input type="submit" name="game_split_second" value="Stand">
                                <input type="submit" name="game_split_second" value="Surrender">
                            </form>
<?php                   }
                    }
                } ?>
            </div>
        </div>
    </body>
</html>