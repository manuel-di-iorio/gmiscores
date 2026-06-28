<?php

function insert_score($params) {
  [
    "insertMode" => $insertMode,
    "playerName" => $playerName,
    "gameId" => $gameId,
    "score" => $score,
    "ip" => $ip,
    "country" => $country,
    "sign" => $sign,
    "leaderboardId" => $leaderboardId,
    "tags" => $tags,
    "data" => $data,
    "minScore" => $minScore,
    "maxScore" => $maxScore,
    "env" => $env,
    "userId" => $userId
  ] = $params;

  $userId = $userId ?? null;

  if ($userId) {
    $player = Player::getOrCreateForUser($userId, $playerName, $gameId);
  } else {
    Player::create($playerName, $gameId);
    $player = Player::getByName($playerName)->fetch_assoc();
  }
  $playerId = $player["player_id"];

  $result = Score::findByGameLeaderboardAndPlayerId($gameId, $leaderboardId, $playerId);
  if (!$result->num_rows) {
    $scoreId = Score::create($gameId, $playerId, $score, $ip, $country, NULL, $sign, $leaderboardId, $tags, $data, $env, $userId);
    $scoreAction = "inserted";
  } else {
    $scoreAction = "nothing";
    
    $currentScore = $result->fetch_assoc();
    $scoreId = $currentScore["score_id"];
    
    if (
      ($insertMode === "higher" && $score > $currentScore["score"]) ||
      ($insertMode === "lower" && $score < $currentScore["score"]) 
    ) {        
      Score::update($scoreId, $score, $ip, $country, $sign, $data);        
      $scoreAction = "updated";
    }
  }

  $position = Score::getRankByScoreId($scoreId, $gameId);

  return [
    "scoreId" => $scoreId,
    "score" => $score,
    "scoreAction" => $scoreAction,
    "position" => $position
  ];
}

function process_score_submission(array $params): array {
  if (!isset($params["game"]) || !isset($params["score"]) || !isset($params["hash"])) {
    return ['ok' => false, 'error' => 'Missing parameters', 'code' => 'ValidationError', 'status' => 400];
  }

  $gameId = (int)$params["game"];
  $score = (string)$params["score"];
  $clientHash = $params["hash"];
  $sign = isset($params["sign"]) ? $params["sign"] : NULL;
  $tags = isset($params["tags"]) && $params["tags"] !== '' ? (string)$params["tags"] : NULL;
  $insertMode = isset($params["insertMode"]) ? $params["insertMode"] : "higher";
  if ($insertMode === "all") $insertMode = "higher";
  $data = isset($params["data"]) ? (string)$params["data"] : NULL;
  $minScore = isset($params["minScore"]) ? (float)$params["minScore"] : NULL;
  $maxScore = isset($params["maxScore"]) ? (float)$params["maxScore"] : NULL;
  $env = isset($params["env"]) && $params["env"] === "test" ? "test" : "production";
  $token = isset($params["token"]) ? $params["token"] : NULL;

  $requiresAuth = Game::requiresPlayerAuth($gameId);

  $userId = null;
  $playerName = null;

  if ($token) {
    try {
      $tokenData = json_decode(aes_decrypt($token, true), true);
      if (isset($tokenData["id"])) {
        $userResult = User::getById($tokenData["id"]);
        if ($userResult->num_rows) {
          $loggedUser = $userResult->fetch_assoc();
          $userId = (int)$loggedUser["id"];
          $playerName = $loggedUser["username"];
        }
      }
    } catch (Exception $e) {
      $userId = null;
      $playerName = null;
    }
  }

  if ($requiresAuth) {
    if (!$userId) {
      return ['ok' => false, 'error' => 'Player authentication required', 'code' => 'AuthenticationRequired', 'status' => 401];
    }
  } else {
    if (!$playerName && isset($params["player"])) {
      $playerName = trim(base64_decode($params["player"]));
    }
  }

  if (empty($playerName) || strlen($playerName) > 64) {
    return ['ok' => false, 'error' => 'Invalid player name', 'code' => 'ValidationError', 'status' => 400];
  }

  $playerNameEncoded = base64_encode($playerName);

  $leaderboardId = NULL;

  if (isset($params["leaderboard_id"])) {
    if (is_numeric($params["leaderboard_id"])) {
      $leaderboardId = (int)$params["leaderboard_id"];
      $lb = Leaderboard::getById($leaderboardId);
      if (!$lb || $lb['game_id'] != $gameId) {
        return ['ok' => false, 'error' => 'Invalid leaderboard_id', 'code' => 'ValidationError', 'status' => 400];
      }
    } else {
      $tags = (string)$params["leaderboard_id"];
    }
  }

  if (!$leaderboardId) {
    $allLbs = Leaderboard::listByGame($gameId);
    if (empty($allLbs)) {
      return ['ok' => false, 'error' => 'No leaderboard found for this game', 'code' => 'NotFoundError', 'status' => 404];
    }
    $leaderboardId = $allLbs[0]['leaderboard_id'];
  }

  if (!is_null($insertMode) && $insertMode !== "higher" && $insertMode !== "lower") {
    return ['ok' => false, 'error' => "Invalid parameter 'insertMode'", 'code' => 'ValidationError', 'status' => 400];
  }

  if (!is_numeric($score)) {
    return ['ok' => false, 'error' => "Invalid parameter 'score'", 'code' => 'ValidationError', 'status' => 400];
  }

  $result = Game::getClientSecretById($gameId);
  if (!$result->num_rows) {
    return ['ok' => false, 'error' => "Game #$gameId does not exists", 'code' => 'NotFoundError', 'status' => 404];
  }
  $clientSecret = $result->fetch_assoc()["client_secret"];

  $salt = "game=$gameId";
  if (isset($params["leaderboard_id"])) {
    if (is_numeric($params["leaderboard_id"])) {
      $salt .= "&leaderboard_id=$leaderboardId";
    } else {
      $salt .= "&leaderboard_id=" . $params["leaderboard_id"];
    }
  }
  if (isset($params["tags"])) $salt .= "&tags=$tags";
  $salt .= "&score=$score&player=$playerNameEncoded&hash=$clientHash";
  $saltRaw = preg_replace("/&hash=([a-z0-9]+)+/i", "", $salt);
  $serverHash = sha1($saltRaw . $clientSecret);

  if (!hash_equals($clientHash, $serverHash)) {
    return ['ok' => false, 'error' => 'Invalid hash provided', 'code' => 'InvalidHashError', 'status' => 401];
  }

  $score = floatval($score);

  $ip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : "N/A";
  $country = isset($_SERVER["HTTP_CF_IPCOUNTRY"]) ?
             Locale::getDisplayRegion('-' . $_SERVER["HTTP_CF_IPCOUNTRY"], 'it') : "N/A";

  $result = Ban::isBanned($gameId, $playerName, $ip);
  if ($result->num_rows) {
    return ['ok' => false, 'error' => 'Not authorized to send scores on this game', 'code' => 'AuthorizationError', 'status' => 403];
  }

  [
    "scoreId" => $scoreId,
    "score" => $score,
    "scoreAction" => $scoreAction,
    "position" => $position,
  ] = insert_score([
    "insertMode" => $insertMode,
    "playerName" => $playerName,
    "gameId" => $gameId,
    "score" => $score,
    "ip" => $ip,
    "country" => $country,
    "sign" => $sign,
    "leaderboardId" => $leaderboardId,
    "tags" => $tags,
    "data" => $data,
    "minScore" => $minScore,
    "maxScore" => $maxScore,
    "env" => $env,
    "userId" => $userId
  ]);

  return [
    'ok' => true,
    'data' => [
      "score" => $score,
      "scoreAction" => $scoreAction,
      "position" => intval($position)
    ]
  ];
}
