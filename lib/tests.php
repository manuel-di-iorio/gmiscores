<!-- AUTOMATED SCORE TESTS -->
<?php
require_once("./db.php");
require_once("./checkSession.php");

$isAdmin = isset($user["admin"]) && (int)$user["admin"] === 1;
if (!$isAdmin) {
  http_response_code(403);
  exit("Forbidden");
}

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once("../models/Game.php");
require_once("../models/Score.php");
require_once("../models/Leaderboard.php");

$gameId = 36;
$userId = 19;
$lbs = Leaderboard::listByGame($gameId);
$testLbId = !empty($lbs) ? $lbs[0]['leaderboard_id'] : 1;

function clearScores() {
  global $gameId, $userId, $testLbId;
  Score::clear($gameId, $userId, $testLbId);
}

function clearAllScores() {
  global $gameId, $userId;
  Score::clear($gameId, $userId);
}

function clearRateLimit($endpoint = null) {
  global $db;
  if ($endpoint) {
    $db->query("DELETE FROM api_rate_limits WHERE endpoint = '" . $endpoint . "'");
  } else {
    $db->query("DELETE FROM api_rate_limits");
  }
}

function rawRequest($method, $url, $data = null) {
  $content = null;
  if ($method === "POST" && $data) {
    $content = http_build_query($data);
  }
  $ctx = stream_context_create([
    'http' => [
      'method' => $method,
      'header'  => "Content-type: application/x-www-form-urlencoded",
      'content' => $content,
      'ignore_errors' => true
    ]
  ]);
  $response = file_get_contents($url, false, $ctx);
  return json_decode($response, true);
}

function addUrl($params = []) {
  global $config;
  $query = http_build_query($params);
  return $config["host"] . "/api/v1/add.php" . ($query ? "?$query" : "");
}

function listUrl($params = []) {
  global $config;
  $query = http_build_query($params);
  return $config["host"] . "/api/v1/list.php" . ($query ? "?$query" : "");
}

$player = base64_encode("test");
$player2 = base64_encode("test2");
$secretData = Game::getClientSecretById($gameId)->fetch_assoc();
if (!$secretData || !isset($secretData["client_secret"])) {
  die("Error: Could not retrieve client_secret for gameId $gameId");
}
$secret = $secretData["client_secret"];

function computeAddHash($overrides = [], $lbId = null, $tagVal = null) {
  global $gameId, $score, $player, $secret;
  $salt = "game=$gameId";
  if ($lbId !== null) {
    $salt .= "&leaderboard_id=$lbId";
  }
  if ($tagVal !== null) {
    $salt .= "&tags=$tagVal";
  }
  $p = $overrides["player"] ?? $player;
  $s = $overrides["score"] ?? $score;
  $salt .= "&score=$s&player=$p";
  return sha1($salt . $secret);
}

$score = 50;
$passed = 0;
$failed = 0;
$testName = "";

function ok() {
  global $passed, $testName;
  $passed++;
  echo "  OK: $testName\n";
}

function fail($detail = "") {
  global $failed, $testName;
  $failed++;
  echo "  FAIL: $testName" . ($detail ? " — $detail" : "") . "\n";
}

function addRequest($name, $data, $expectStatus = 200) {
  global $config, $passed, $failed;
  $testName = $name;
  clearScores();
  clearRateLimit();
  $resp = rawRequest("POST", addUrl(), $data);
  if ($expectStatus === 200) {
    if (isset($resp["status"]) && $resp["status"] === 200) {
      $passed++;
      echo "  OK: $testName\n";
    } else {
      $failed++;
      echo "  FAIL: $testName — expected 200, got " . json_encode($resp) . "\n";
    }
  } else {
    if (!isset($resp["status"]) || $resp["status"] !== 200) {
      $passed++;
      echo "  OK: $testName (expected error $expectStatus)\n";
    } else {
      $failed++;
      echo "  FAIL: $testName — expected error $expectStatus, got 200\n";
    }
  }
}

function listRequest($name, $params, $expectStatus = 200) {
  global $passed, $failed;
  $testName = $name;
  clearRateLimit("get_scores");
  $resp = rawRequest("GET", listUrl($params));
  if ($expectStatus === 200) {
    if (isset($resp["status"]) && $resp["status"] === 200) {
      $passed++;
      echo "  OK: $testName\n";
    } else {
      $failed++;
      echo "  FAIL: $testName — expected 200, got " . json_encode($resp) . "\n";
    }
  } else {
    if (!isset($resp["status"]) || $resp["status"] !== 200) {
      $passed++;
      echo "  OK: $testName (expected error $expectStatus)\n";
    } else {
      $failed++;
      echo "  FAIL: $testName — expected error $expectStatus, got 200\n";
    }
  }
}

// =========================================================================
// ADD TESTS — Happy path
// =========================================================================
echo "=== ADD API — Happy Path ===\n";

// 1. Minimal: game + score + player + hash (no leaderboard_id)
addRequest("add: minimal (game, score, player, hash)", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "hash" => computeAddHash(),
]);

// 2. With leaderboard_id (numeric)
addRequest("add: + leaderboard_id (numeric)", [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => $score,
  "player" => $player,
  "hash" => computeAddHash([], $testLbId),
]);

// 3. With leaderboard_id as tag string (old client compat)
$tagLeaderboardId = "secondary";
addRequest("add: + leaderboard_id as tag string", [
  "game" => $gameId,
  "leaderboard_id" => $tagLeaderboardId,
  "score" => $score,
  "player" => $player,
  "hash" => computeAddHash([], $tagLeaderboardId),
]);

// 4. With tags
addRequest("add: + tags", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "tags" => "secondary",
  "hash" => computeAddHash([], null, "secondary"),
]);

// 5. With leaderboard_id (numeric) + tags
addRequest("add: + leaderboard_id (numeric) + tags", [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => $score,
  "player" => $player,
  "tags" => "secondary",
  "hash" => computeAddHash([], $testLbId, "secondary"),
]);

// 6. With sign
addRequest("add: + sign", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "sign" => "my_signature",
  "hash" => computeAddHash(),
]);

// 7. With data
addRequest("add: + data", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "data" => json_encode(["level" => 5, "time" => 120]),
  "hash" => computeAddHash(),
]);

// 8. With insertMode = higher (explicit default)
addRequest("add: + insertMode=higher (explicit default)", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "insertMode" => "higher",
  "hash" => computeAddHash(),
]);

// 9. With insertMode = lower
addRequest("add: + insertMode=lower", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "insertMode" => "lower",
  "hash" => computeAddHash(),
]);

// 10. With insertMode = all (should be treated as "higher")
addRequest("add: + insertMode=all (treated as higher)", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "insertMode" => "all",
  "hash" => computeAddHash(),
]);

// 11. With minScore
addRequest("add: + minScore", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "minScore" => 10,
  "hash" => computeAddHash(),
]);

// 12. With maxScore
addRequest("add: + maxScore", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "maxScore" => 100,
  "hash" => computeAddHash(),
]);

// 13. With minScore + maxScore
addRequest("add: + minScore + maxScore", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "minScore" => 10,
  "maxScore" => 100,
  "hash" => computeAddHash(),
]);

// 14. With env = test
addRequest("add: + env=test", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "env" => "test",
  "hash" => computeAddHash(),
]);

// 15. Score = 0
addRequest("add: score=0", [
  "game" => $gameId,
  "score" => 0,
  "player" => $player,
  "hash" => computeAddHash(["score" => 0]),
], 200);

// 16. Negative score
addRequest("add: negative score", [
  "game" => $gameId,
  "score" => -100,
  "player" => $player,
  "hash" => computeAddHash(["score" => -100]),
], 200);

// 17. Float score
addRequest("add: float score (99.5)", [
  "game" => $gameId,
  "score" => 99.5,
  "player" => $player,
  "hash" => computeAddHash(["score" => 99.5]),
], 200);

// 18. Large score
addRequest("add: large score (999999999)", [
  "game" => $gameId,
  "score" => 999999999,
  "player" => $player,
  "hash" => computeAddHash(["score" => 999999999]),
], 200);

// 19. All optional params combined
addRequest("add: all optional params combined", [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => $score,
  "player" => $player,
  "tags" => "secondary",
  "sign" => "my_sign",
  "data" => '{"key":"value"}',
  "insertMode" => "higher",
  "minScore" => 10,
  "maxScore" => 200,
  "env" => "test",
  "hash" => computeAddHash([], $testLbId, "secondary"),
]);

// 20. Different player
addRequest("add: different player", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player2,
  "hash" => computeAddHash(["player" => $player2]),
]);

// 21. Empty tags (API keeps &tags= in hash because isset($_POST["tags"]) is true)
$emptyTagsHash = sha1("game=$gameId&tags=&score=$score&player=$player" . $secret);
addRequest("add: empty tags", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "tags" => "",
  "hash" => $emptyTagsHash,
]);

// 22. score as string "100"
addRequest("add: score as string '100'", [
  "game" => $gameId,
  "score" => "100",
  "player" => $player,
  "hash" => computeAddHash(["score" => "100"]),
]);

// =========================================================================
// ADD TESTS — Update behavior (insertMode)
// =========================================================================
echo "\n=== ADD API — Update Behavior ===\n";

// 23. insertMode=higher: first insert high, then try lower (should NOT update)
clearScores();
clearRateLimit();
$highScore = 200;
$resp1 = rawRequest("POST", addUrl(), [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => $highScore,
  "player" => $player,
  "hash" => computeAddHash(["score" => $highScore], $testLbId),
]);
clearRateLimit();
$resp2 = rawRequest("POST", addUrl(), [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => 100,
  "player" => $player,
  "insertMode" => "higher",
  "hash" => computeAddHash(["score" => 100], $testLbId),
]);
if (isset($resp2["scoreAction"]) && $resp2["scoreAction"] === "nothing") {
  $passed++; echo "  OK: insertMode=higher: lower score does not update\n";
} else {
  $failed++; echo "  FAIL: insertMode=higher: expected 'nothing', got " . json_encode($resp2) . "\n";
}

// 24. insertMode=higher: first insert low, then try higher (should update)
clearScores();
clearRateLimit();
$resp1 = rawRequest("POST", addUrl(), [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => 50,
  "player" => $player,
  "hash" => computeAddHash(["score" => 50], $testLbId),
]);
clearRateLimit();
$resp2 = rawRequest("POST", addUrl(), [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => 100,
  "player" => $player,
  "insertMode" => "higher",
  "hash" => computeAddHash(["score" => 100], $testLbId),
]);
if (isset($resp2["scoreAction"]) && $resp2["scoreAction"] === "updated") {
  $passed++; echo "  OK: insertMode=higher: higher score updates\n";
} else {
  $failed++; echo "  FAIL: insertMode=higher: expected 'updated', got " . json_encode($resp2) . "\n";
}

// 25. insertMode=lower: first insert high, then try lower (should update)
clearScores();
clearRateLimit();
$resp1 = rawRequest("POST", addUrl(), [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => 200,
  "player" => $player,
  "hash" => computeAddHash(["score" => 200], $testLbId),
]);
clearRateLimit();
$resp2 = rawRequest("POST", addUrl(), [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => 100,
  "player" => $player,
  "insertMode" => "lower",
  "hash" => computeAddHash(["score" => 100], $testLbId),
]);
if (isset($resp2["scoreAction"]) && $resp2["scoreAction"] === "updated") {
  $passed++; echo "  OK: insertMode=lower: lower score updates\n";
} else {
  $failed++; echo "  FAIL: insertMode=lower: expected 'updated', got " . json_encode($resp2) . "\n";
}

// 26. insertMode=lower: first insert low, then try higher (should NOT update)
clearScores();
clearRateLimit();
$resp1 = rawRequest("POST", addUrl(), [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => 50,
  "player" => $player,
  "hash" => computeAddHash(["score" => 50], $testLbId),
]);
clearRateLimit();
$resp2 = rawRequest("POST", addUrl(), [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
  "score" => 100,
  "player" => $player,
  "insertMode" => "lower",
  "hash" => computeAddHash(["score" => 100], $testLbId),
]);
if (isset($resp2["scoreAction"]) && $resp2["scoreAction"] === "nothing") {
  $passed++; echo "  OK: insertMode=lower: higher score does not update\n";
} else {
  $failed++; echo "  FAIL: insertMode=lower: expected 'nothing', got " . json_encode($resp2) . "\n";
}

// =========================================================================
// ADD TESTS — Error paths
// =========================================================================
echo "\n=== ADD API — Error Paths ===\n";

// 27. Missing game
addRequest("add error: missing game", [
  "score" => $score,
  "player" => $player,
  "hash" => "dummy",
], 400);

// 28. Missing score
addRequest("add error: missing score", [
  "game" => $gameId,
  "player" => $player,
  "hash" => "dummy",
], 400);

// 29. Missing hash
addRequest("add error: missing hash", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
], 400);

// 30. Invalid hash
addRequest("add error: invalid hash", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "hash" => "invalidhash123",
], 401);

// 31. Invalid insertMode
addRequest("add error: invalid insertMode", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "insertMode" => "invalid",
  "hash" => computeAddHash(),
], 400);

// 32. Non-numeric score
addRequest("add error: non-numeric score", [
  "game" => $gameId,
  "score" => "abc",
  "player" => $player,
  "hash" => computeAddHash(["score" => "abc"]),
], 400);

// 33. Player too long (>64 chars)
$longPlayer = base64_encode(str_repeat("a", 65));
addRequest("add error: player name too long", [
  "game" => $gameId,
  "score" => $score,
  "player" => $longPlayer,
  "hash" => computeAddHash(["player" => $longPlayer]),
], 400);

// 34. Invalid leaderboard_id (numeric but wrong game)
addRequest("add error: invalid leaderboard_id (wrong game)", [
  "game" => $gameId,
  "leaderboard_id" => 99999,
  "score" => $score,
  "player" => $player,
  "hash" => computeAddHash([], 99999),
], 400);

// 35. Invalid game id (non-existent)
addRequest("add error: non-existent game", [
  "game" => 99999,
  "score" => $score,
  "player" => $player,
  "hash" => "dummy",
], 404);

// 36. GET method not allowed
$testName = "add error: GET method not allowed";
$resp = rawRequest("GET", addUrl(["game" => $gameId, "score" => $score]));
if (!isset($resp["status"]) || $resp["status"] !== 200) {
  $passed++; echo "  OK: $testName (expected error)\n";
} else {
  $failed++; echo "  FAIL: $testName — expected error, got 200\n";
}

// 37. Hash with wrong secret
addRequest("add error: hash with wrong secret", [
  "game" => $gameId,
  "score" => $score,
  "player" => $player,
  "hash" => sha1("game=$gameId&score=$score&player=$player" . "wrong_secret"),
], 401);

// 38. Empty body (POST with no data)
$testName = "add error: empty POST body";
$resp = rawRequest("POST", addUrl(), []);
if (!isset($resp["status"]) || $resp["status"] !== 200) {
  $passed++; echo "  OK: $testName (expected error)\n";
} else {
  $failed++; echo "  FAIL: $testName — expected error, got 200\n";
}

// =========================================================================
// LIST TESTS — Happy path
// =========================================================================
echo "\n=== LIST API — Happy Path ===\n";

// 1. Minimal: game only
listRequest("list: minimal (game only)", [
  "game" => $gameId,
]);

// 2. With leaderboard_id (numeric)
listRequest("list: + leaderboard_id (numeric)", [
  "game" => $gameId,
  "leaderboard_id" => $testLbId,
]);

// 3. With leaderboard_id as tag string
listRequest("list: + leaderboard_id as tag string", [
  "game" => $gameId,
  "leaderboard_id" => "secondary",
]);

// 4. With tags
listRequest("list: + tags", [
  "game" => $gameId,
  "tags" => "secondary",
]);

// 5. With page = 0
listRequest("list: + page=0", [
  "game" => $gameId,
  "page" => 0,
]);

// 6. With page = 1
listRequest("list: + page=1", [
  "game" => $gameId,
  "page" => 1,
]);

// 7. With limit = 1
listRequest("list: + limit=1", [
  "game" => $gameId,
  "limit" => 1,
]);

// 8. With limit = 50
listRequest("list: + limit=50", [
  "game" => $gameId,
  "limit" => 50,
]);

// 9. With limit = 1000 (max allowed)
listRequest("list: + limit=1000", [
  "game" => $gameId,
  "limit" => 1000,
]);

// 10. With page + limit
listRequest("list: + page=2 + limit=5", [
  "game" => $gameId,
  "page" => 2,
  "limit" => 5,
]);

// 11. With order = ASC
listRequest("list: + order=ASC", [
  "game" => $gameId,
  "order" => "ASC",
]);

// 12. With order = DESC
listRequest("list: + order=DESC", [
  "game" => $gameId,
  "order" => "DESC",
]);

// 13. With player (base64 encoded)
listRequest("list: + player (base64)", [
  "game" => $gameId,
  "player" => $player,
]);

// 14. With includePlayer (base64)
listRequest("list: + includePlayer", [
  "game" => $gameId,
  "includePlayer" => $player,
]);

// 15. With startTime
listRequest("list: + startTime", [
  "game" => $gameId,
  "startTime" => "2024-01-01",
]);

// 16. With endTime
listRequest("list: + endTime", [
  "game" => $gameId,
  "endTime" => "2025-12-31",
]);

// 17. With startTime + endTime
listRequest("list: + startTime + endTime", [
  "game" => $gameId,
  "startTime" => "2024-01-01",
  "endTime" => "2025-12-31",
]);

// 18. With env = test
listRequest("list: + env=test", [
  "game" => $gameId,
  "env" => "test",
]);

// 19. With env = production
listRequest("list: + env=production", [
  "game" => $gameId,
  "env" => "production",
]);

// 20. With env = all
listRequest("list: + env=all", [
  "game" => $gameId,
  "env" => "all",
]);

// 21. Multiple params combined
listRequest("list: combined (page + limit + order + tags)", [
  "game" => $gameId,
  "page" => 0,
  "limit" => 10,
  "order" => "ASC",
  "tags" => "secondary",
]);

// 22. limit = 0
listRequest("list: limit=0 (edge case)", [
  "game" => $gameId,
  "limit" => 0,
]);

// 23. limit > 1000 (should be clamped to 1000)
listRequest("list: limit=5000 (clamped to 1000)", [
  "game" => $gameId,
  "limit" => 5000,
]);

// 24. Negative page (should be clamped to 0)
listRequest("list: page=-1 (clamped to 0)", [
  "game" => $gameId,
  "page" => -1,
]);

// 25. Negative limit (should be clamped to 0)
listRequest("list: limit=-5 (clamped to 0)", [
  "game" => $gameId,
  "limit" => -5,
]);

// 26. With startTime + endTime + player + order
listRequest("list: combined (startTime + endTime + player + order)", [
  "game" => $gameId,
  "startTime" => "2024-01-01",
  "endTime" => "2025-12-31",
  "player" => $player,
  "order" => "ASC",
]);

// 27. order case-insensitive ("asc" should work)
listRequest("list: order=asc (lowercase)", [
  "game" => $gameId,
  "order" => "asc",
]);

// 28. With env + page + limit + order
listRequest("list: combined (env + page + limit + order)", [
  "game" => $gameId,
  "env" => "production",
  "page" => 0,
  "limit" => 20,
  "order" => "DESC",
]);

// 29. startTime with datetime format
listRequest("list: startTime with datetime", [
  "game" => $gameId,
  "startTime" => "2024-06-15 10:30:00",
]);

// 30. endTime with datetime format
listRequest("list: endTime with datetime", [
  "game" => $gameId,
  "endTime" => "2025-06-15 23:59:59",
]);

// 31. includePlayer + player together
listRequest("list: includePlayer + player together", [
  "game" => $gameId,
  "player" => $player,
  "includePlayer" => $player,
]);

// 32. leaderboard_id as tag + tags together (tag overrides)
listRequest("list: leaderboard_id tag + tags (tag overrides)", [
  "game" => $gameId,
  "leaderboard_id" => "secondary",
  "tags" => "primary",
]);

// =========================================================================
// LIST TESTS — Error paths
// =========================================================================
echo "\n=== LIST API — Error Paths ===\n";

// 33. Missing game
listRequest("list error: missing game", [
], 400);

// 34. Invalid leaderboard_id (numeric, wrong game)
listRequest("list error: invalid leaderboard_id (wrong game)", [
  "game" => $gameId,
  "leaderboard_id" => 99999,
], 400);

// 35. Invalid startTime (not a valid date)
listRequest("list error: invalid startTime", [
  "game" => $gameId,
  "startTime" => "not-a-date",
], 400);

// 36. Invalid endTime (not a valid date)
listRequest("list error: invalid endTime", [
  "game" => $gameId,
  "endTime" => "not-a-date",
], 400);

// 37. Non-existent game
listRequest("list error: non-existent game", [
  "game" => 99999,
], 404);

// 38. POST method not allowed
$testName = "list error: POST method not allowed";
$resp = rawRequest("POST", listUrl(["game" => $gameId]));
if (!isset($resp["status"]) || $resp["status"] !== 200) {
  $passed++; echo "  OK: $testName (expected error)\n";
} else {
  $failed++; echo "  FAIL: $testName — expected error, got 200\n";
}

// =========================================================================
// Cleanup & Summary
// =========================================================================
clearAllScores();

echo "\n========================================\n";
echo "Results: $passed passed, $failed failed\n";
echo "========================================\n";

if ($failed > 0) {
  http_response_code(500);
  exit("TESTS FAILED");
}

echo "Tests OK";
