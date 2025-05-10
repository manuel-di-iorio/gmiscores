<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Score.php");

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}
$gameId = (int)$_GET["id"];

// Get the scores
$result = Score::getAll($gameId, $user["id"]);

// Write the CSV in memory
$f = fopen('php://memory', 'w'); 
fputcsv($f, ["username", "score", "ip_encrypted", "country", "created_at", "sign", "leaderboard_id", "data"], ","); 

while ($record = $result->fetch_assoc()) {
  $line = [
    base64_encode($record["username"]), $record["score"], isset($record["ip"]) ? aes_encrypt($record["ip"]) : "", 
    $record["ip_country"], $record["created_at"], $record["sign"], $record["leaderboard_id"], 
    $record["data"]
  ];
  fputcsv($f, $line, ","); 
}

fseek($f, 0);
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="gmiscores-' . $gameId . '.csv";');
fpassthru($f);
