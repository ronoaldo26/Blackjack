<?php
session_start();

class Game
{
    public $deck = array();
    public $dealer = array();
    public $player = array();
    public $cards = array("A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "j", "q", "k");
    public $suits = array("c", "h", "s", "d");
    public $player_value = 0;
    public $dealer_value = 0;
    public $win_check = 0;
    public $player_split = array();
    public $player_value_split = 0;

    public function shuffle_deck()
    {
        for($x = 0; $x <= 3; $x++)
        {
            shuffle($this->deck);
        }
    }
    public function create_deck()
    {
        for($x = 0; $x < 13; $x++)
        {
            for($y = 0; $y < 4; $y++)
            {
                array_push($this->deck, $this->suits[$y] . "" . $this->cards[$x]);
            }
        }
    }
    public function deal_card()
    {
        return array_pop($this->deck);
    }
    public function get_first_value($card)
    {
        $value = $this->get_card_value($card[0]);
        return $value;
    }
    public function get_hand_value($cards)
    {
        $value = 0;
        $total_value = 0;
        $ace_count = 0;

        foreach($cards as $values)
        {
            $value = $this->get_card_value($values);
            $total_value += $value;

            if($value == 11)
            {
                $ace_count++;
            }
        }

        for($x = 0; $x < $ace_count; $x++)
        {
            if($total_value >= 22)
            {
                $total_value -= 10;
            }
        }

        return $total_value;
    }
    public function get_card_value($card)
    {
        $face = substr($card, 1);
        $num_pattern = '/[0-9]/';
        $face_pattern = '/[jqk]/';
        if(preg_match($num_pattern, $face))
        {
            return $face;
        }
        elseif(preg_match($face_pattern, $face))
        {
            return 10;
        }
        else
        {
            return 11;
        }
    }
    public function check_duplicate($cards)
    {
        $value = array();
        $x = 0;

        foreach($cards as $values)
        {
            $value[$x] = substr($values, 1);
            $x++;
        }

        if($value[0] == $value[1])
        {
            $_SESSION['game_split'] = "duplicate";
        }
    }
    public function win_check($player_value, $dealer_value, $game)
    {
        if($player_value > 21 && $game && $game != "Split")
        {
            $_SESSION['win_check_message'] = "Bust! You lose";
            $_SESSION['money'] -= $_SESSION['bet'];
            if($game == "Double")
            {
                $_SESSION['money'] -= $_SESSION['bet'];
                return 2;
            }
            return 1;
        }
        elseif($dealer_value > 21 && $game != "Split")
        {
            $_SESSION['win_check_message'] = "You win! Dealer lose";
            $_SESSION['money'] += $_SESSION['bet'];
            if($game == "Double")
            {
                $_SESSION['money'] += $_SESSION['bet'];
            }
            return 1;
        }
        elseif($game == "DEAL" || $game == "Deal Again")
        {
            if($player_value == 21)
            {
                $_SESSION['win_check_message'] = "BLACK JACK!";
                $_SESSION['money'] += ($_SESSION['bet'] * 1.5);
                return 1;
            }
            else
            {
                return 0;
            }
        }
        elseif($game == "Stand")
        {
            if($player_value > $dealer_value)
            {
                $_SESSION['win_check_message'] = "You win! Dealer lose";
                $_SESSION['money'] += $_SESSION['bet'];
                return 1;
            }
            elseif($player_value == $dealer_value)
            {
                $_SESSION['win_check_message'] = "Draw!";
                return 1;
            }
            else
            {
                $_SESSION['win_check_message'] = "You lose! Dealer Wins";
                $_SESSION['money'] -= $_SESSION['bet'];
                return 1;
            }
        }
        elseif($game == "Split")
        {
            if($player_value > 21)
            {
                $_SESSION['money'] -= $_SESSION['bet'];
                return 0;
            }
            elseif($dealer_value > 21)
            {
                $_SESSION['money'] += $_SESSION['bet'];
                return 1;
            }
            elseif($_SESSION['game'] == "Split_Stand")
            {
                if($player_value > $dealer_value)
                {
                    $_SESSION['money'] += $_SESSION['bet'];
                    return 1;
                }
                elseif($player_value == $dealer_value)
                {
                    return 2;
                }
                else
                {
                    $_SESSION['money'] -= $_SESSION['bet'];
                    return 0;
                }
            }
            else
            {
                return 1;
            }
        }
        elseif($game == "Double")
        {
            if($player_value > $dealer_value)
            {
                $_SESSION['win_check_message'] = "You win! Dealer lose";
                $_SESSION['money'] += ($_SESSION['bet'] * 2);
                return 1;
            }
            elseif($player_value == $dealer_value)
            {
                $_SESSION['win_check_message'] = "Draw!";
                return 1;
            }
            else
            {
                $_SESSION['win_check_message'] = "You lose! Dealer Wins";
                $_SESSION['money'] -= ($_SESSION['bet'] * 2);
                return 1;
            }
        }
        else
        {
            return 0;
        }
    }
    public function dealer_gameplay($dealer_value, $player_value, $dealer_hand)
    {
        if($dealer_value <= 16)
        {
            do {
                if($dealer_value <= $player_value)
                {
                    array_push($dealer_hand, $this->deal_card());
                    $dealer_value = $this->get_hand_value($dealer_hand);
                }
                else
                {
                    break;
                }
            } while($dealer_value <= 16);
        }
        return $dealer_hand;
    }
}

if(isset($_POST['bet']) && !empty($_POST['bet']))
{
    $_SESSION['bet'] += $_POST['bet'];
    $_SESSION['money'] -= $_POST['bet'];
    $_SESSION['game'] = "start";
    header("Location: blackjack.php");
}

if(isset($_POST['game']) && !empty($_POST['game']))
{
    if($_POST['game'] == "DEAL" || $_POST['game'] == "Deal Again")
    {
        $_SESSION['blackjack'] = new Game();
        $_SESSION['blackjack']->create_deck();
        $_SESSION['blackjack']->shuffle_deck();
        $_SESSION['player_value'] = 0;
        $_SESSION['dealer_value'] = 0;
        $_SESSION['win_check_message'] = "";
        $_SESSION['game_split'] = "";
        array_push($_SESSION['blackjack']->player, $_SESSION['blackjack']->deal_card());
        array_push($_SESSION['blackjack']->dealer, $_SESSION['blackjack']->deal_card());
        array_push($_SESSION['blackjack']->player, $_SESSION['blackjack']->deal_card());
        array_push($_SESSION['blackjack']->dealer, $_SESSION['blackjack']->deal_card());
        $_SESSION['blackjack']->check_duplicate($_SESSION['blackjack']->player);
        $_SESSION['player_hand'] = $_SESSION['blackjack']->player;
        $_SESSION['dealer_hand'] = $_SESSION['blackjack']->dealer;
        $_SESSION['blackjack']->player_value = $_SESSION['blackjack']->get_hand_value($_SESSION['player_hand']);
        $_SESSION['player_value'] = $_SESSION['blackjack']->player_value;
        $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_first_value($_SESSION['dealer_hand']);
        $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;
        $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value, 
        $_SESSION['blackjack']->dealer_value, $_POST['game']);
        if($_SESSION['blackjack']->win_check == 0)
        {
            $_SESSION['game'] = "first_deal";
        }
        else
        {
            $_SESSION['game'] = "black_jack";
        }
        header("Location: blackjack.php");
    }
    elseif($_POST['game'] == "Hit")
    {
        array_push($_SESSION['blackjack']->player, $_SESSION['blackjack']->deal_card());
        $_SESSION['player_hand'] = $_SESSION['blackjack']->player;
        $_SESSION['blackjack']->player_value = $_SESSION['blackjack']->get_hand_value($_SESSION['player_hand']);
        $_SESSION['player_value'] = $_SESSION['blackjack']->player_value;
        $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value, 
        $_SESSION['blackjack']->dealer_value, $_POST['game']);
        if($_SESSION['blackjack']->win_check == 0)
        {
            $_SESSION['game'] = "Hit";
        }
        elseif($_SESSION['blackjack']->win_check == 1)
        {
            $_SESSION['game'] = "bust";
        }
        header("Location: blackjack.php");
    }
    elseif($_POST['game'] == "Stand")
    {
        $_SESSION['game'] = "Stand";
        $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);
        $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;

        $_SESSION['blackjack']->dealer = $_SESSION['blackjack']->dealer_gameplay($_SESSION['dealer_value'], 
        $_SESSION['player_value'], $_SESSION['dealer_hand']);
        $_SESSION['dealer_hand'] = $_SESSION['blackjack']->dealer;
        $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);

        $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value, $_SESSION['blackjack']->dealer_value, 
        $_POST['game']);
        $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;
        header("Location: blackjack.php");
    }
    elseif($_POST['game'] == "Surrender")
    {
        $_SESSION['win_check_message'] = "Surrendered, You Lose!";
        $_SESSION['money'] -= $_SESSION['bet'];
        $_SESSION['game'] = $_POST['game'];
        header("Location: blackjack.php");
    }
    elseif($_POST['game'] == "Double")
    {
        array_push($_SESSION['blackjack']->player, $_SESSION['blackjack']->deal_card());
        $_SESSION['player_hand'] = $_SESSION['blackjack']->player;
        $_SESSION['blackjack']->player_value = $_SESSION['blackjack']->get_hand_value($_SESSION['player_hand']);
        $_SESSION['player_value'] = $_SESSION['blackjack']->player_value;
        $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);

        $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value, 
        $_SESSION['blackjack']->dealer_value, $_POST['game']);
        if($_SESSION['blackjack']->win_check == 2)
        {
            $_SESSION['game'] = "bust";
        }
        else
        {
            $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;
            $_SESSION['blackjack']->dealer = $_SESSION['blackjack']->dealer_gameplay($_SESSION['dealer_value'], $_SESSION['player_value'], $_SESSION['dealer_hand']);
            $_SESSION['dealer_hand'] = $_SESSION['blackjack']->dealer;
            $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);
            $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value, $_SESSION['blackjack']->dealer_value, $_POST['game']);
            $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;
            $_SESSION['game'] = "Stand";
        }

        header("Location: blackjack.php");
    }
    elseif($_POST['game'] == "Split")
    {
        if($_SESSION['game_split'] == "duplicate")
        {
            array_push($_SESSION['blackjack']->player_split, $_SESSION['player_hand'][1]);
            array_pop($_SESSION['blackjack']->player);
            $_SESSION['player_hand'] = "";
            $_SESSION['player_hand'] = $_SESSION['blackjack']->player;
            $_SESSION['player_hand_split'] = $_SESSION['blackjack']->player_split;
            $_SESSION['blackjack']->player_value = $_SESSION['blackjack']->get_hand_value($_SESSION['player_hand']);
            $_SESSION['blackjack']->player_value_split = $_SESSION['blackjack']->get_hand_value($_SESSION['player_hand_split']);
            $_SESSION['player_value'] = $_SESSION['blackjack']->player_value;
            $_SESSION['player_value_split'] = $_SESSION['blackjack']->player_value_split;
            $_SESSION['game'] = "Split";
            $_SESSION['game_split'] = "first_hand";
            header("Location: blackjack.php");
        }
        elseif($_SESSION['game_split'] == "first_hand")
        {
            if($_POST['game_split_first'] == "Hit")
            {
                $_SESSION['game_split'] = "first_hand";
                array_push($_SESSION['blackjack']->player, $_SESSION['blackjack']->deal_card());
                $_SESSION['player_hand'] = $_SESSION['blackjack']->player;
                $_SESSION['blackjack']->player_value = $_SESSION['blackjack']->get_hand_value($_SESSION['player_hand']);
                $_SESSION['player_value'] = $_SESSION['blackjack']->player_value;
                $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value, 
                $_SESSION['blackjack']->dealer_value, $_POST['game']);
                if($_SESSION['blackjack']->win_check == 0)
                {
                    $_SESSION['game_first'] = "bust";
                    $_SESSION['win_check_message'] = "Bust! your first hand Lose!";
                    $_SESSION['game_split'] = "second_hand";
                }
            }
            elseif($_POST['game_split_first'] == "Stand")
            {
                $_SESSION['game_first'] = "stand";
                $_SESSION['win_check_message'] = "Your first hand card, Stand!";
                $_SESSION['game_split'] = "second_hand";
            }
            elseif($_POST['game_split_first'] == "Surrender")
            {
                $_SESSION['game_first'] = "surrender";
                $_SESSION['game_split'] = "second_hand";
                $_SESSION['win_check_message'] = "First hand, Surrender!";
                $_SESSION['money'] -= $_SESSION['bet'];
            }
            header("Location: blackjack.php");
        }
        elseif($_SESSION['game_split'] == "second_hand")
        {
            if($_POST['game_split_second'] == "Hit")
            {
                $_SESSION['win_check_message'] = "";
                array_push($_SESSION['blackjack']->player_split, $_SESSION['blackjack']->deal_card());
                $_SESSION['player_hand_split'] = $_SESSION['blackjack']->player_split;
                $_SESSION['blackjack']->player_value_split = $_SESSION['blackjack']->get_hand_value($_SESSION['player_hand_split']);
                $_SESSION['player_value_split'] = $_SESSION['blackjack']->player_value_split;
                $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value_split, 
                $_SESSION['blackjack']->dealer_value, $_POST['game']);
                if($_SESSION['blackjack']->win_check == 0)
                {
                    $_SESSION['game'] = "Split";
                    $_SESSION['win_check_message'] = "Second hand, Bust!";
                    $_SESSION['game_split'] = "stand";

                    if($_SESSION['game_first'] == "stand")
                    {
                        $_SESSION['game'] = "Split_Stand";
                        $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);
                        $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;

                        $_SESSION['blackjack']->dealer = $_SESSION['blackjack']->dealer_gameplay($_SESSION['dealer_value'], 
                        $_SESSION['player_value'], $_SESSION['dealer_hand']);
                        $_SESSION['dealer_hand'] = $_SESSION['blackjack']->dealer;
                        $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);

                        $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value, 
                        $_SESSION['blackjack']->dealer_value, $_POST['game']);
                        $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;
                        if($_SESSION['blackjack']->win_check == 1)
                        {
                            $_SESSION['win_check_message'] = "First hand, Win!";
                        }
                        elseif($_SESSION['blackjack']->win_check == 0)
                        {
                            $_SESSION['win_check_message'] = "First hand, Lose!";
                        }
                        else
                        {
                            $_SESSION['win_check_message'] = "First hand, Draw!";
                        }
                        $_SESSION['win_check_message'] = $_SESSION['win_check_message'] . " | Second hand, Bust!";
                    }
                }
            }
            elseif($_POST['game_split_second'] == "Stand")
            {
                $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);
                $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;
                $_SESSION['game'] = "Split_Stand";
                
                $_SESSION['blackjack']->dealer = $_SESSION['blackjack']->dealer_gameplay($_SESSION['dealer_value'], 
                $_SESSION['player_value'], $_SESSION['dealer_hand']);
                $_SESSION['dealer_hand'] = $_SESSION['blackjack']->dealer;
                $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);

                $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value_split, 
                $_SESSION['blackjack']->dealer_value, $_POST['game']);
                $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;
                if($_SESSION['blackjack']->win_check == 1)
                {
                    $_SESSION['win_check_message'] = "Second hand, Win!";
                }
                elseif($_SESSION['blackjack']->win_check == 0)
                {
                    $_SESSION['win_check_message'] = "Second hand, Lose!";
                }
                else
                {
                    $_SESSION['win_check_message'] = "Second hand, Draw!";
                }

                if($_SESSION['game_first'] == "stand")
                {
                    $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);
                    $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;

                    $_SESSION['blackjack']->dealer = $_SESSION['blackjack']->dealer_gameplay($_SESSION['dealer_value'], 
                    $_SESSION['player_value'], $_SESSION['dealer_hand']);
                    $_SESSION['dealer_hand'] = $_SESSION['blackjack']->dealer;
                    $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);

                    $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value, 
                    $_SESSION['blackjack']->dealer_value, $_POST['game']);
                    $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;
                    if($_SESSION['blackjack']->win_check == 1)
                    {
                        $_SESSION['win_check_message'] = "First hand, Win! | " . $_SESSION['win_check_message'];
                    }
                    elseif($_SESSION['blackjack']->win_check == 0)
                    {
                        $_SESSION['win_check_message'] = "First hand, Lose! | " . $_SESSION['win_check_message'];
                    }
                    else
                    {
                        $_SESSION['win_check_message'] = "First hand, Draw! | " . $_SESSION['win_check_message'];
                    }
                }

                $_SESSION['game_split'] = "stand";
            }
            elseif($_POST['game_split_second'] == "Surrender")
            {
                $_SESSION['game_split'] = "stand";
                $_SESSION['money'] -= $_SESSION['bet'];
                $_SESSION['win_check_message'] = "Second hand, Surrendered!";

                if($_SESSION['game_first'] == "stand")
                {
                    $_SESSION['game'] = "Split_Stand";
                    $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);
                    $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;

                    $_SESSION['blackjack']->dealer = $_SESSION['blackjack']->dealer_gameplay($_SESSION['dealer_value'], 
                    $_SESSION['player_value'], $_SESSION['dealer_hand']);
                    $_SESSION['dealer_hand'] = $_SESSION['blackjack']->dealer;
                    $_SESSION['blackjack']->dealer_value = $_SESSION['blackjack']->get_hand_value($_SESSION['dealer_hand']);

                    $_SESSION['blackjack']->win_check = $_SESSION['blackjack']->win_check($_SESSION['blackjack']->player_value, 
                    $_SESSION['blackjack']->dealer_value, $_POST['game']);
                    $_SESSION['dealer_value'] = $_SESSION['blackjack']->dealer_value;
                    if($_SESSION['blackjack']->win_check == 1)
                    {
                        $_SESSION['win_check_message'] = "First hand, Win!";
                    }
                    elseif($_SESSION['blackjack']->win_check == 0)
                    {
                        $_SESSION['win_check_message'] = "First hand, Lose!";
                    }
                    else
                    {
                        $_SESSION['win_check_message'] = "First hand, Draw!";
                    }
                    $_SESSION['win_check_message'] = $_SESSION['win_check_message'] . " | Second hand, Surrender!";
                }
            }
        }
        header("Location: blackjack.php");
    }
    elseif($_POST['game'] == "CHANGE")
    {
        $_SESSION['money'] += $_SESSION['bet'];
        unset($_SESSION['bet']);
        $_SESSION['game_split'] = "";
        $_SESSION['game'] = "start";
        $_SESSION['win_check_message'] = "";
        $_SESSION['player_hand'] = array();
        $_SESSION['player_hand_split'] = array();
        $_SESSION['dealer_hand'] = array();
        $_SESSION['player_value'] = 0;
        $_SESSION['dealer_value'] = 0;
        header("Location: blackjack.php");
    }
    elseif($_POST['game'] == "RESET")
    {
        $_SESSION['win_check_message'] = "";
        unset($_SESSION['bet']);
        unset($_SESSION['money']);
        $_SESSION['game_split'] = "";
        $_SESSION['game'] = "start";
        $_SESSION['player_hand'] = array();
        $_SESSION['player_hand_split'] = array();
        $_SESSION['dealer_hand'] = array();
        $_SESSION['player_value'] = 0;
        $_SESSION['dealer_value'] = 0;
        header("Location: blackjack.php");
    }
    elseif($_POST['game'] == "LOG-OUT")
    {
        session_destroy();
        header("Location: index.php");
    }
}
?>