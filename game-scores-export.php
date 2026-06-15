<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");
require_once("models/Score.php");

if (!isset($_GET["id"])) {
  header("Location: games.php");
  exit;
}
$gameId = (int)$_GET["id"];
$leaderboardId = isset($_GET["leaderboard_id"]) ? (int)$_GET["leaderboard_id"] : null;
$envFilter = isset($_GET["env"]) ? $_GET["env"] : null;

$result = Score::getAll($gameId, $user["id"], $envFilter);

$f = fopen('php://memory', 'w'); 
fputcsv($f, ["username", "score", "ip_encrypted", "country", "created_at", "sign", "tags", "leaderboard_id", "data", "env"], ","); 

while ($record = $result->fetch_assoc()) {
  $line = [
    base64_encode($record["username"]), $record["score"], isset($record["ip"]) ? aes_encrypt($record["ip"]) : "", 
    $record["ip_country"], $record["created_at"], $record["sign"], $record["tags"], $record["leaderboard_id"],
    $record["data"], $record["env"]
  ];
  fputcsv($f, $line, ","); 
}

fseek($f, 0);
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="gmiscores-' . $gameId . '.csv";');
fpassthru($f);