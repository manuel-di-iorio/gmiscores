<?php
 require_once("lib/db.php");
 require_once("lib/checkSession.php");
 require_once("models/Game.php");
 
 // On form submit
 if ($_SERVER['REQUEST_METHOD'] === "POST") {
   $formError = false;

   /*if (!isset($_POST["g-recaptcha-response"])) {
     header("Location: add-games.php");
     exit;
   }
   // Verify the recaptcha challenge
   $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, stream_context_create([
     'http' => [
       'method' => 'POST',
       'header'  => "Content-type: application/x-www-form-urlencoded",
       'content' => http_build_query([
           'secret' => $config["recaptchaSecret"],
           'response' => $_POST["g-recaptcha-response"],
           'remoteip' => $_SERVER["REMOTE_ADDR"],
       ])
     ]
   ]));
   if ($response === FALSE) {
     $formError = '<div class="w3-panel w3-red"><h4>È avvenuto un errore del server durante l\'esecuzione della richiesta, riprovare nuovamente</h4></div>';
   }
   */
   if (!$formError) {
     /*$responseJson = json_decode($response, true);
     if (!$responseJson["success"]) {
       $formError = '<div class="w3-panel w3-red"><h4>Errore: non hai superato il controllo antispam</h4></div>';
     }*/
     // Validate the name
     if (!$formError) {
       $gameName = isset($_POST["name"]) ? trim($_POST["name"]) : "";
       if (empty($gameName)) {
         $formError = '<div class="w3-panel w3-red"><h4>Errore: nome del gioco richiesto</h4></div>';
       }
       if (!$formError) {
       $clientSecret = bin2hex(random_bytes(16));
         Game::create($user["id"], $gameName, $clientSecret);
         $gameId = $db->insert_id;
         header("Location: game.php?id=$gameId");
         exit;
       }
     }
   }
 }
 $view = "add-game";
 $pageName = "Aggiungi il tuo gioco";
 require_once("includes/layout.php");
