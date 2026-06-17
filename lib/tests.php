<!-- AUTOMATED SCORE TESTS -->
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once("./db.php");
require_once("../models/Game.php");
require_once("../models/Score.php");
require_once("../models/Leaderboard.php");

$gameId = 36;
$userId = 19;
// Prendi la prima classifica del gioco
$lbs = Leaderboard::listByGame($gameId);
$testLbId = !empty($lbs) ? $lbs[0]['leaderboard_id'] : 1;

function clearScores() {
  global $gameId;
  global $userId;
  Score::clear($gameId, $userId);
}

function request($action = "add", $data) {
  global $host;
  global $config;
  $params = $action == "add" ? "" : "?" . http_build_query($data);
  $content = $action == "add" ? http_build_query($data) : NULL;

  if ($action == "add") {
    clearScores();
  }

  $ctx = stream_context_create([
    'http' => [
      'method' => $action == "add" ? "POST" : "GET",
      'header'  => "Content-type: application/x-www-form-urlencoded",
      'content' => $content,
      'ignore_errors' => true
    ]
  ]);
  $response = file_get_contents($config["host"] . "/api/v1/$action.php$params", false, $ctx);

  $responseJson = json_decode($response, true);

  if ($responseJson === null && json_last_error() !== JSON_ERROR_NONE) {
    die("Error in request $action: " . json_encode($data) . ". Failed to decode JSON. JSON error: " . json_last_error_msg() . ". Raw response: " . $response);
  }

  if (!isset($responseJson["status"]) || $responseJson["status"] != 200) {
    die("Error in request $action: " . json_encode($data) . ". API returned an error. Response: " . json_encode($responseJson));
  }
}

$player = base64_encode("test");
$secretData = Game::getClientSecretById(36)->fetch_assoc();

if (!$secretData || !isset($secretData["client_secret"])) {
  die("Error: Could not retrieve client_secret for gameId 36. Response: " . json_encode($secretData));
}
$secret = $secretData["client_secret"];

// Tests: ADD
$score = 50;

function addRequest($overrides = []) {
  global $gameId, $score, $player, $secret, $testLbId;
  $params = array_merge([
    "game" => $gameId,
    "leaderboard_id" => $testLbId,
    "score" => $score,
    "player" => $player,
  ], $overrides);
  $hashData = "game=$gameId&leaderboard_id=$testLbId";
  if (isset($params["tags"])) $hashData .= "&tags=" . $params["tags"];
  $hashData .= "&score=$score&player=$player";
  $params["hash"] = sha1($hashData . $secret);
  return request("add", $params);
}

addRequest([]);
addRequest(["sign" => "sign"]);
addRequest(["data" => "data"]);
addRequest(["insertMode" => "lower"]);
addRequest(["insertMode" => "higher"]);
addRequest(["tags" => "secondary"]);

// Tests: LIST
function listRequest($overrides = []) {
  global $gameId, $testLbId;
  $params = array_merge([
    "game" => $gameId,
    "leaderboard_id" => $testLbId,
  ], $overrides);
  return request("list", $params);
}

listRequest([]);
listRequest(["tags" => "secondary"]);
listRequest(["page" => 0]);
listRequest(["page" => 0, "limit" => 20]);
listRequest(["order" => "ASC"]);
listRequest(["player" => base64_encode("test")]);
listRequest(["includePlayer" => base64_encode("test")]);
listRequest(["startTime" => "2020-05-04", "endTime" => "2020-05-06"]);

echo "Tests OK";
