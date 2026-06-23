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
    $player = Player::getOrCreateForUser($userId);
  } else {
    Player::create($playerName);
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
    
    if ($insertMode === "all") {
      Score::update($scoreId, $score, $ip, $country, $sign, $data);
      $scoreAction = "updated";
    } elseif (
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