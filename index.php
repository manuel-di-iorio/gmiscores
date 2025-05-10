<?php
require_once("lib/db.php");
require_once("models/User.php");
require_once("models/Game.php");
require_once("models/Player.php");
require_once("models/Score.php");

// Get the ordered stats
$statGameWithMores = Score::getGameWithMoreScores();
$stats = [
  "scores"=>    [ "label" => "Punteggi inviati", "count" => Score::count() ],  
  "players" =>  [ "label" => "Giocatori", "count" => Player::count() ],
  "games" =>    [ "label" => "Giochi", "count" => Game::count() ],  
  "active-games" => [ "label" => "Giochi attivi negli ultimi 3 mesi", "count" => Score::getActiveGames() ],
  "top-game" => [ "label" => "Gioco con più punteggi", "count" => $statGameWithMores["name"] ],
  "dev-with-more-games" =>    [ "label" => "Giocatore più attivo", "count" => Score::getPlayerWithMoreScores()["username"] ],  
  "unique-scores-countries" => [ "label" => "Paesi ", "count" => Score::getUniqueCountriesCount() ],
  "users" =>    [ "label" => "Sviluppatori", "count" => User::count() ],  
];

$view = "index";
$pageName = $config["platformTitle"];
require_once("includes/layout.php");
