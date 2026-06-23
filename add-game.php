<?php
 require_once("lib/db.php");
 require_once("lib/checkSession.php");
 require_once("lib/maintenance.php"); check_maintenance();
 require_once("lib/csrf.php");
 require_once("models/Game.php");

 $selectedTeamId = isset($_COOKIE['selected_team_id']) && $_COOKIE['selected_team_id'] !== '' ? (int)$_COOKIE['selected_team_id'] : null;
 if ($selectedTeamId !== null) {
   require_once("models/Team.php");
   if (!Team::isMember($selectedTeamId, $user["id"]) || !Team::isAdmin($selectedTeamId, $user["id"])) {
     $selectedTeamId = null;
   }
 }

 // On form submit
 if ($_SERVER['REQUEST_METHOD'] === "POST") {
   csrf_validate_request();
   $formError = false;

   if (!$formError) {
     if (!$formError) {
       $gameName = isset($_POST["name"]) ? trim($_POST["name"]) : "";
       if (empty($gameName)) {
         $formError = '<div style="background:#f44336;color:#fff;padding:8px 16px;border-radius:4px;margin-bottom:16px"><h4>Errore: nome del gioco richiesto</h4></div>';
       }
        if (!$formError && strlen($gameName) > 100) {
          $formError = '<div style="background:#f44336;color:#fff;padding:8px 16px;border-radius:4px;margin-bottom:16px"><h4>Errore: nome del gioco troppo lungo (max 100 caratteri)</h4></div>';
        }
        if (!$formError) {
        $clientSecret = bin2hex(random_bytes(16));
        $requirePlayerAuth = isset($_POST["require_player_auth"]) && $_POST["require_player_auth"] === "1";
          Game::create($user["id"], $gameName, $clientSecret, $selectedTeamId, $requirePlayerAuth);
          $gameId = $db->insert_id;
         header("Location: game.php?id=$gameId");
         exit;
       }
     }
   }
 }
 $view = "add-game";
 $pageName = __('add_game_title');
 require_once("includes/layout.php");
