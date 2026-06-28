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

function rawJsonRequest($method, $url, $data = null) {
  $content = $data ? json_encode($data) : null;
  $ctx = stream_context_create([
    'http' => [
      'method' => $method,
      'header'  => "Content-type: application/json",
      'content' => $content,
      'ignore_errors' => true
    ]
  ]);
  $response = file_get_contents($url, false, $ctx);
  return json_decode($response, true);
}

function rawRequestsParallel($requests) {
  $mh = curl_multi_init();
  $ch = [];
  foreach ($requests as $i => $req) {
    $method = $req['method'] ?? 'GET';
    $url = $req['url'];
    $body = $req['body'] ?? null;
    $headers = $req['headers'] ?? [];

    $c = curl_init($url);
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($c, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($c, CURLOPT_TIMEOUT, 30);
    if ($method === 'POST' && $body) {
      curl_setopt($c, CURLOPT_POSTFIELDS, $body);
    }
    if (!empty($headers)) {
      curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
    }
    curl_multi_add_handle($mh, $c);
    $ch[$i] = $c;
  }

  do {
    $status = curl_multi_exec($mh, $running);
    if ($running) {
      curl_multi_select($mh, 1);
    }
  } while ($running && $status === CURLM_OK);

  $results = [];
  foreach ($ch as $i => $c) {
    $results[$i] = json_decode(curl_multi_getcontent($c), true);
    curl_multi_remove_handle($mh, $c);
    curl_close($c);
  }
  curl_multi_close($mh);
  return $results;
}

function syncUrl() {
  global $config;
  return $config["host"] . "/api/v1/sync.php";
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

function playerAuthUrl($path, $params = []) {
  global $config;
  $query = http_build_query($params);
  return $config["host"] . "/player-auth/" . $path . ($query ? "?$query" : "");
}

function apiV1Url($path, $params = []) {
  global $config;
  $query = http_build_query($params);
  return $config["host"] . "/api/v1/" . $path . ($query ? "?$query" : "");
}

function clearLoginSessions() {
  global $db;
  $db->query("DELETE FROM player_login_sessions");
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
$testStartTime = microtime(true);
$passed = 0;
$failed = 0;
$testName = "";
$lastTestTime = 0;

function ok() {
  global $passed, $testName, $lastTestTime;
  $passed++;
  $time = $lastTestTime > 0 ? " <span class=\"detail\">(" . round($lastTestTime * 1000, 0) . "ms)</span>" : "";
  echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">" . htmlspecialchars($testName) . $time . "</span></li>\n";
}

function fail($detail = "") {
  global $failed, $testName, $lastTestTime;
  $failed++;
  $extra = $detail ? " <span class=\"detail\">- " . htmlspecialchars($detail) . "</span>" : "";
  $time = $lastTestTime > 0 ? " <span class=\"detail\">(" . round($lastTestTime * 1000, 0) . "ms)</span>" : "";
  echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">" . htmlspecialchars($testName) . $extra . $time . "</span></li>\n";
}

function reportPass($name, $detail = "") {
  global $passed, $lastTestTime;
  $passed++;
  $extra = $detail ? " <span class=\"detail\">" . htmlspecialchars($detail) . "</span>" : "";
  $time = $lastTestTime > 0 ? " <span class=\"detail\">(" . round($lastTestTime * 1000, 0) . "ms)</span>" : "";
  echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">" . htmlspecialchars($name) . $extra . $time . "</span></li>\n";
}

function reportFail($name, $detail = "") {
  global $failed, $lastTestTime;
  $failed++;
  $extra = $detail ? " <span class=\"detail\">- " . htmlspecialchars($detail) . "</span>" : "";
  $time = $lastTestTime > 0 ? " <span class=\"detail\">(" . round($lastTestTime * 1000, 0) . "ms)</span>" : "";
  echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">" . htmlspecialchars($name) . $extra . $time . "</span></li>\n";
}

function assertTest($name, $cond, $detail = "") {
  if ($cond) {
    reportPass($name);
  } else {
    reportFail($name, $detail);
  }
}

function addRequest($name, $data, $expectStatus = 200) {
  global $config, $passed, $failed, $lastTestTime;
  $testName = $name;
  clearScores();
  clearRateLimit();
  $t0 = microtime(true);
  $resp = rawRequest("POST", addUrl(), $data);
  $lastTestTime = microtime(true) - $t0;
  if ($expectStatus === 200) {
    if (isset($resp["status"]) && $resp["status"] === 200) {
      $passed++;
      echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">(" . round($lastTestTime * 1000, 0) . "ms)</span></span></li>\n";
    } else {
      $failed++;
      echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">- expected 200, got " . htmlspecialchars(json_encode($resp)) . " (" . round($lastTestTime * 1000, 0) . "ms)</span></span></li>\n";
    }
  } else {
    if (!isset($resp["status"]) || $resp["status"] !== 200) {
      $passed++;
      echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">(expected error $expectStatus) (" . round($lastTestTime * 1000, 0) . "ms)</span></span></li>\n";
    } else {
      $failed++;
      echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">- expected error $expectStatus, got 200 (" . round($lastTestTime * 1000, 0) . "ms)</span></span></li>\n";
    }
  }
}

function listRequest($name, $params, $expectStatus = 200) {
  global $passed, $failed, $lastTestTime;
  $testName = $name;
  clearRateLimit("get_scores");
  $resp = rawRequest("GET", listUrl($params));
  if ($expectStatus === 200) {
    if (isset($resp["status"]) && $resp["status"] === 200) {
      $passed++;
      echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">" . htmlspecialchars($testName) . "</span></li>\n";
    } else {
      $failed++;
      echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">- expected 200, got " . htmlspecialchars(json_encode($resp)) . "</span></span></li>\n";
    }
  } else {
    if (!isset($resp["status"]) || $resp["status"] !== 200) {
      $passed++;
      echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">(expected error $expectStatus)</span></span></li>\n";
    } else {
      $failed++;
      echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">- expected error $expectStatus, got 200</span></span></li>\n";
    }
  }
}

// =========================================================================
// ADD TESTS - Happy path
// =========================================================================
echo "<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n<meta charset=\"UTF-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n<title>Score API Test Report</title>\n<style>\n*,*::before,*::after{box-sizing:border-box}\nbody{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#0f172a;color:#cbd5e1;margin:0;padding:2rem 2.5rem;min-height:100vh}\nh1{color:#f1f5f9;font-size:1.6rem;font-weight:700;margin:0 0 .25rem;letter-spacing:-.02em}\n.subtitle{color:#64748b;font-size:.875rem;margin:0 0 2.5rem}\nh2{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#475569;margin:2rem 0 .4rem;padding-bottom:.4rem;border-bottom:1px solid #1e293b}\nul{list-style:none;padding:0;margin:0}\nli{display:flex;align-items:baseline;gap:.5rem;padding:.3rem .6rem;border-radius:5px;font-size:.84rem;line-height:1.5;margin-bottom:1px}\nli.ok{background:rgba(34,197,94,.07)}\nli.ok .icon{color:#22c55e;font-weight:700;flex-shrink:0;font-size:.85rem}\nli.fail{background:rgba(239,68,68,.1)}\nli.fail .icon{color:#ef4444;font-weight:700;flex-shrink:0;font-size:.85rem}\n.label{color:#e2e8f0}\n.detail{color:#64748b;font-size:.78rem;font-family:'Courier New',monospace}\n.summary{margin-top:2.5rem;padding:1.5rem 2rem;border-radius:10px;display:flex;gap:2.5rem;align-items:center;border:1px solid}\n.summary.all-pass{background:rgba(34,197,94,.07);border-color:rgba(34,197,94,.2)}\n.summary.has-fail{background:rgba(239,68,68,.07);border-color:rgba(239,68,68,.2)}\n.stat .num{font-size:2.8rem;font-weight:800;line-height:1}\n.stat .lbl{font-size:.7rem;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-top:.2rem}\n.num.pass{color:#22c55e}\n.num.fail{color:#ef4444}\n.divider{width:1px;background:#1e293b;align-self:stretch}\n.msg{font-size:1rem;font-weight:600}\n.msg.ok{color:#22c55e}\n.msg.fail{color:#ef4444}\n</style>\n</head>\n<body>\n<h1>Score API Test Report</h1>\n<p class=\"subtitle\">Generated " . date('Y-m-d H:i:s') . "</p>\n";
echo "<section>\n<h2>ADD API &mdash; Happy Path</h2>\n<ul>\n";

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
// ADD TESTS - Update behavior (insertMode)
// =========================================================================
echo "</ul></section>\n<section>\n<h2>ADD API &mdash; Update Behavior</h2>\n<ul>\n";

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
  $passed++; echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">insertMode=higher: lower score does not update</span></li>\n";
} else {
  $failed++; echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">insertMode=higher <span class=\"detail\">- expected 'nothing', got " . htmlspecialchars(json_encode($resp2)) . "</span></span></li>\n";
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
  $passed++; echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">insertMode=higher: higher score updates</span></li>\n";
} else {
  $failed++; echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">insertMode=higher <span class=\"detail\">- expected 'updated', got " . htmlspecialchars(json_encode($resp2)) . "</span></span></li>\n";
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
  $passed++; echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">insertMode=lower: lower score updates</span></li>\n";
} else {
  $failed++; echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">insertMode=lower <span class=\"detail\">- expected 'updated', got " . htmlspecialchars(json_encode($resp2)) . "</span></span></li>\n";
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
  $passed++; echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">insertMode=lower: higher score does not update</span></li>\n";
} else {
  $failed++; echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">insertMode=lower <span class=\"detail\">- expected 'nothing', got " . htmlspecialchars(json_encode($resp2)) . "</span></span></li>\n";
}

// =========================================================================
// ADD TESTS - Error paths
// =========================================================================
echo "</ul></section>\n<section>\n<h2>ADD API &mdash; Error Paths</h2>\n<ul>\n";

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
  $passed++; echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">(expected error)</span></span></li>\n";
} else {
  $failed++; echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">- expected error, got 200</span></span></li>\n";
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
  $passed++; echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">(expected error)</span></span></li>\n";
} else {
  $failed++; echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">- expected error, got 200</span></span></li>\n";
}

// =========================================================================
// LIST TESTS - Happy path
// =========================================================================
echo "</ul></section>\n<section>\n<h2>LIST API &mdash; Happy Path</h2>\n<ul>\n";

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
// LIST TESTS - Error paths
// =========================================================================
echo "</ul></section>\n<section>\n<h2>LIST API &mdash; Error Paths</h2>\n<ul>\n";

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
  $passed++; echo "<li class=\"ok\"><span class=\"icon\">&#10003;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">(expected error)</span></span></li>\n";
} else {
  $failed++; echo "<li class=\"fail\"><span class=\"icon\">&#10007;</span><span class=\"label\">" . htmlspecialchars($testName) . " <span class=\"detail\">- expected error, got 200</span></span></li>\n";
}

// =========================================================================
// PLAYER LOGIN TESTS - OAuth flow (browser step mocked)
// =========================================================================
// The real flow is: SDK calls login-start -> opens a browser -> Discord OAuth2
// -> discord/callback.php links the authenticated user to the login session.
// OAuth2 cannot be replayed in an automated test, so we mock the browser+Discord
// step by writing user_id directly into player_login_sessions, exactly like
// callback.php does after a successful Discord authentication.
echo "</ul></section>\n<section>\n<h2>PLAYER LOGIN API &mdash; OAuth Flow (browser mocked)</h2>\n<ul>\n";

// --- login-start ---------------------------------------------------------

// 1. POST login-start returns a valid 64-hex session token
clearLoginSessions();
$resp = rawRequest("POST", playerAuthUrl("login-start.php"));
$sessionToken = $resp["session_token"] ?? "";
assertTest(
  "login-start: POST returns a 64-hex session_token",
  isset($resp["status"]) && $resp["status"] === 200 && (bool)preg_match('/^[a-f0-9]{64}$/', $sessionToken),
  "got " . json_encode($resp)
);

// 2. login-start GET not allowed
$resp = rawRequest("GET", playerAuthUrl("login-start.php"));
assertTest(
  "login-start error: GET method not allowed",
  isset($resp["status"]) && $resp["status"] === 405,
  "got " . json_encode($resp)
);

// --- check-session (polling, before OAuth completes) ---------------------

// 3. Pending session → logged=false
$resp = rawRequest("GET", playerAuthUrl("check-session.php", ["session" => $sessionToken]));
assertTest(
  "check-session: pending session returns logged=false",
  isset($resp["logged"]) && $resp["logged"] === false,
  "got " . json_encode($resp)
);

// 4. Missing session param → 400
$resp = rawRequest("GET", playerAuthUrl("check-session.php"));
assertTest(
  "check-session error: missing session param",
  isset($resp["status"]) && $resp["status"] === 400,
  "got " . json_encode($resp)
);

// 5. Invalid session token length → 400
$resp = rawRequest("GET", playerAuthUrl("check-session.php", ["session" => "abc123"]));
assertTest(
  "check-session error: invalid session token length",
  isset($resp["status"]) && $resp["status"] === 400,
  "got " . json_encode($resp)
);

// 6. POST not allowed → 405
$resp = rawRequest("POST", playerAuthUrl("check-session.php", ["session" => $sessionToken]));
assertTest(
  "check-session error: POST method not allowed",
  isset($resp["status"]) && $resp["status"] === 405,
  "got " . json_encode($resp)
);

// --- MOCK: simulate the browser + Discord OAuth callback -----------------

// 7. Link the user to the session (mock of discord/callback.php), then poll
exec_query(
  "UPDATE player_login_sessions SET user_id = ? WHERE session_token = ?",
  ["is", $userId, $sessionToken]
);
$resp = rawRequest("GET", playerAuthUrl("check-session.php", ["session" => $sessionToken]));
$loginToken = $resp["token"] ?? "";
assertTest(
  "check-session: after mocked OAuth returns logged=true + token + username",
  isset($resp["logged"]) && $resp["logged"] === true
    && isset($resp["user_id"]) && (int)$resp["user_id"] === $userId
    && !empty($resp["token"]) && isset($resp["username"]),
  "got " . json_encode($resp)
);

// 8. Session is single-use: consumed (deleted) after a successful poll
$resp = rawRequest("GET", playerAuthUrl("check-session.php", ["session" => $sessionToken]));
assertTest(
  "check-session: session consumed (single-use) after success",
  isset($resp["logged"]) && $resp["logged"] === false,
  "got " . json_encode($resp)
);

// --- check-token ---------------------------------------------------------

// Build a valid encrypted token exactly like the server issues it
$validToken = aes_encrypt(json_encode(["id" => $userId]), true);

// 9. Valid token + game → valid=true
$resp = rawRequest("GET", playerAuthUrl("check-token.php", ["token" => $validToken, "game" => $gameId]));
assertTest(
  "check-token: valid token returns valid=true",
  isset($resp["valid"]) && $resp["valid"] === true
    && isset($resp["user_id"]) && (int)$resp["user_id"] === $userId,
  "got " . json_encode($resp)
);

// 10. Token issued by check-session also validates
if (!empty($loginToken)) {
  $resp = rawRequest("GET", playerAuthUrl("check-token.php", ["token" => $loginToken, "game" => $gameId]));
  assertTest(
    "check-token: token issued by check-session is valid",
    isset($resp["valid"]) && $resp["valid"] === true,
    "got " . json_encode($resp)
  );
} else {
  reportFail("check-token: token issued by check-session is valid", "no login token captured");
}

// 11. Missing token → 400
$resp = rawRequest("GET", playerAuthUrl("check-token.php", ["game" => $gameId]));
assertTest(
  "check-token error: missing token",
  isset($resp["status"]) && $resp["status"] === 400,
  "got " . json_encode($resp)
);

// 12. Missing game → 400
$resp = rawRequest("GET", playerAuthUrl("check-token.php", ["token" => $validToken]));
assertTest(
  "check-token error: missing game",
  isset($resp["status"]) && $resp["status"] === 400,
  "got " . json_encode($resp)
);

// 13. Garbage token → valid=false (status 200)
// Must be a base64 string that decodes to ≥49 bytes (16 IV + 32 HMAC + 1 payload) so
// openssl_decrypt() receives a full-length IV and emits no E_WARNING that would
// corrupt the JSON output on servers with display_errors=On (e.g. XAMPP dev).
// The HMAC check still fails → Exception caught → valid=false.
$garbageToken = base64_encode(str_repeat("\x42", 64));
$resp = rawRequest("GET", playerAuthUrl("check-token.php", ["token" => $garbageToken, "game" => $gameId]));
assertTest(
  "check-token: garbage token returns valid=false",
  isset($resp["valid"]) && $resp["valid"] === false,
  "got " . json_encode($resp)
);

// 14. Token for a non-existent user → valid=false
$ghostToken = aes_encrypt(json_encode(["id" => 99999999]), true);
$resp = rawRequest("GET", playerAuthUrl("check-token.php", ["token" => $ghostToken, "game" => $gameId]));
assertTest(
  "check-token: non-existent user returns valid=false",
  isset($resp["valid"]) && $resp["valid"] === false,
  "got " . json_encode($resp)
);

// 15. POST not allowed → 405
$resp = rawRequest("POST", playerAuthUrl("check-token.php", ["token" => $validToken, "game" => $gameId]));
assertTest(
  "check-token error: POST method not allowed",
  isset($resp["status"]) && $resp["status"] === 405,
  "got " . json_encode($resp)
);

// --- player-logout -------------------------------------------------------

// 16. player-logout clears the cookie and returns success
$resp = rawRequest("POST", apiV1Url("player-logout.php"));
assertTest(
  "player-logout: returns status 200",
  isset($resp["status"]) && $resp["status"] === 200,
  "got " . json_encode($resp)
);

// =========================================================================
// SYNC API TESTS
// =========================================================================
echo "<section>\n<h2>SYNC API</h2><ul>\n";

function syncOpId() {
  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
  );
}

function syncHash($overrides = []) {
  global $gameId, $player, $secret, $testLbId;
  $s = $overrides["score"] ?? 100;
  $p = $overrides["player"] ?? $player;
  $lb = $overrides["leaderboard_id"] ?? $testLbId;
  $salt = "game=$gameId";
  if ($lb !== null) $salt .= "&leaderboard_id=$lb";
  $salt .= "&score=$s&player=$p";
  return sha1($salt . $secret);
}

// 1. Single op → applied (sequential — needed for test 2)
clearScores();
$opId1 = syncOpId();
$hash1 = syncHash(["score" => 200]);
$resp = rawJsonRequest("POST", syncUrl(), [
  "operations" => [[
    "op_id" => $opId1,
    "type" => "score.submit",
    "payload" => [
      "game" => $gameId, "score" => 200, "player" => $player,
      "hash" => $hash1, "leaderboard_id" => $testLbId
    ]
  ]]
]);
assertTest(
  "sync: single op → applied",
  isset($resp["status"]) && $resp["status"] === 200
  && isset($resp["results"][0]) && $resp["results"][0]["status"] === "applied",
  "got " . json_encode($resp)
);

// 2. Same op again → duplicate (depends on test 1)
$resp = rawJsonRequest("POST", syncUrl(), [
  "operations" => [[
    "op_id" => $opId1,
    "type" => "score.submit",
    "payload" => [
      "game" => $gameId, "score" => 200, "player" => $player,
      "hash" => $hash1, "leaderboard_id" => $testLbId
    ]
  ]]
]);
assertTest(
  "sync: duplicate op → duplicate status",
  isset($resp["results"][0]) && $resp["results"][0]["status"] === "duplicate",
  "got " . json_encode($resp)
);

// 3-10. Independent tests → parallel batch
clearScores();
clearRateLimit('sync_batch');

$opId2a = syncOpId();
$opId2b = syncOpId();
$opId3a = syncOpId();
$opId3b = syncOpId();
$opId4 = syncOpId();
$opId5 = syncOpId();
$ops21 = [];
for ($i = 0; $i < 21; $i++) {
  $ops21[] = ["op_id" => syncOpId(), "type" => "score.submit", "payload" => ["game" => $gameId]];
}

$syncTests = [
  ['name' => 'batch of 2', 'body' => ["operations" => [
    ["op_id" => $opId2a, "type" => "score.submit", "payload" => ["game" => $gameId, "score" => 300, "player" => $player, "hash" => syncHash(["score" => 300]), "leaderboard_id" => $testLbId]],
    ["op_id" => $opId2b, "type" => "score.submit", "payload" => ["game" => $gameId, "score" => 400, "player" => $player, "hash" => syncHash(["score" => 400]), "leaderboard_id" => $testLbId]]
  ]]],
  ['name' => 'wrong hash', 'body' => ["operations" => [
    ["op_id" => $opId3a, "type" => "score.submit", "payload" => ["game" => $gameId, "score" => 500, "player" => $player, "hash" => "invalidhash", "leaderboard_id" => $testLbId]],
    ["op_id" => $opId3b, "type" => "score.submit", "payload" => ["game" => $gameId, "score" => 600, "player" => $player, "hash" => syncHash(["score" => 600]), "leaderboard_id" => $testLbId]]
  ]]],
  ['name' => 'unknown type', 'body' => ["operations" => [
    ["op_id" => $opId4, "type" => "cloud_save.set", "payload" => ["game" => $gameId]]
  ]]],
  ['name' => 'missing op_id', 'body' => ["operations" => [
    ["type" => "score.submit", "payload" => ["game" => $gameId]]
  ]]],
  ['name' => 'invalid game_id', 'body' => ["operations" => [
    ["op_id" => $opId5, "type" => "score.submit", "payload" => ["game" => 0]]
  ]]],
  ['name' => 'empty body', 'body' => []],
  ['name' => '>20 ops', 'body' => ["operations" => $ops21]],
];

$reqs = [];
foreach ($syncTests as $t) {
  $reqs[] = [
    'method' => 'POST', 'url' => syncUrl(),
    'body' => json_encode($t['body']),
    'headers' => ['Content-type: application/json']
  ];
}
$reqs[] = ['method' => 'GET', 'url' => syncUrl()];

$results = rawRequestsParallel($reqs);

// Batch of 2
$resp = $results[0];
assertTest(
  "sync: batch of 2 → both applied",
  isset($resp["results"][0]) && $resp["results"][0]["status"] === "applied"
  && isset($resp["results"][1]) && $resp["results"][1]["status"] === "applied",
  "got " . json_encode($resp)
);

// Wrong hash
$resp = $results[1];
assertTest(
  "sync: wrong hash → failed",
  isset($resp["results"][0]) && $resp["results"][0]["status"] === "failed",
  "got " . json_encode($resp)
);
assertTest(
  "sync: wrong hash → other op still applied",
  isset($resp["results"][1]) && $resp["results"][1]["status"] === "applied",
  "got " . json_encode($resp)
);

// Unknown type
$resp = $results[2];
assertTest(
  "sync: unknown type → failed with UnknownOperationType",
  isset($resp["results"][0]) && $resp["results"][0]["status"] === "failed"
  && isset($resp["results"][0]["error"]) && $resp["results"][0]["error"] === "UnknownOperationType",
  "got " . json_encode($resp)
);

// Missing op_id
$resp = $results[3];
assertTest(
  "sync: missing op_id → failed",
  isset($resp["results"][0]) && $resp["results"][0]["status"] === "failed",
  "got " . json_encode($resp)
);

// Invalid game_id
$resp = $results[4];
assertTest(
  "sync: invalid game_id → failed",
  isset($resp["results"][0]) && $resp["results"][0]["status"] === "failed",
  "got " . json_encode($resp)
);

// Empty body
$resp = $results[5];
assertTest(
  "sync: empty body → 400",
  isset($resp["status"]) && $resp["status"] === 400,
  "got " . json_encode($resp)
);

// >20 ops
$resp = $results[6];
assertTest(
  "sync: >20 ops → 400",
  isset($resp["status"]) && $resp["status"] === 400,
  "got " . json_encode($resp)
);

// GET not allowed
$resp = $results[7];
assertTest(
  "sync: GET not allowed → 405",
  isset($resp["status"]) && $resp["status"] === 405,
  "got " . json_encode($resp)
);

clearScores();
clearRateLimit('sync_batch');

// =========================================================================
// RATE LIMIT TESTS
// =========================================================================
echo "<section>\n<h2>Rate Limit</h2><ul>\n";

clearRateLimit('add_score');

// Send 10 requests sequentially (rate limit requires sequential processing)
$limitOk = true;
for ($i = 0; $i < 10; $i++) {
  clearScores();
  $hash = computeAddHash(["score" => 100 + $i], $testLbId);
  $resp = rawRequest("POST", addUrl(), [
    "game" => $gameId, "score" => 100 + $i, "player" => $player,
    "hash" => $hash, "leaderboard_id" => $testLbId
  ]);
  if (!isset($resp["status"]) || $resp["status"] !== 200) { $limitOk = false; break; }
}

assertTest("rate limit: 10 requests within limit → all 200", $limitOk);

// 11th request should be rate limited
clearScores();
$hash = computeAddHash(["score" => 999], $testLbId);
$resp = rawRequest("POST", addUrl(), [
  "game" => $gameId, "score" => 999, "player" => $player,
  "hash" => $hash, "leaderboard_id" => $testLbId
]);
assertTest(
  "rate limit: 11th request returns 429",
  isset($resp["status"]) && $resp["status"] === 429,
  "got " . json_encode($resp)
);

assertTest(
  "rate limit: error code is RateLimitExceeded",
  isset($resp["code"]) && $resp["code"] === "RateLimitExceeded",
  "got " . json_encode($resp)
);

clearRateLimit('add_score');
clearScores();

// =========================================================================
// Cleanup & Summary
// =========================================================================
clearAllScores();
clearLoginSessions();

$summaryClass = $failed > 0 ? "has-fail" : "all-pass";
$total = $passed + $failed;
$elapsed = round(microtime(true) - $testStartTime, 2);
echo "</ul></section>\n";
echo "<div class=\"summary $summaryClass\">\n";
echo "  <div class=\"stat\"><div class=\"num pass\">$passed</div><div class=\"lbl\">Passed</div></div>\n";
if ($failed > 0) {
  echo "  <div class=\"divider\"></div>\n";
  echo "  <div class=\"stat\"><div class=\"num fail\">$failed</div><div class=\"lbl\">Failed</div></div>\n";
}
echo "  <div class=\"divider\"></div>\n";
echo "  <div class=\"stat\"><div class=\"num\" style=\"color:#94a3b8\">$total</div><div class=\"lbl\">Total</div></div>\n";
echo "  <div class=\"divider\"></div>\n";
echo "  <div class=\"stat\"><div class=\"num\" style=\"color:#94a3b8\">{$elapsed}s</div><div class=\"lbl\">Time</div></div>\n";
$msgClass = $failed > 0 ? "fail" : "ok";
$msgText = $failed > 0 ? "$failed test" . ($failed > 1 ? "s" : "") . " failed" : "All tests passed!";
echo "  <div class=\"divider\"></div>\n";
echo "  <div class=\"msg $msgClass\">$msgText</div>\n";
echo "</div>\n</body>\n</html>\n";

if ($failed > 0) {
  http_response_code(500);
  exit();
}
