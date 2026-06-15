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
    "env" => $env
  ] = $params;

  Player::create($playerName);
  $player = Player::getByName($playerName)->fetch_assoc();
  $playerId = $player["player_id"];

  switch ($insertMode) {
    case "all":
      $scoreId = Score::create($gameId, $playerId, $score, $ip, $country, NULL, $sign, $leaderboardId, $tags, $data, $env);
      $scoreAction = "inserted";      
    break;

    default:
      $result = Score::findByGameLeaderboardAndPlayerId($gameId, $leaderboardId, $playerId);
      if (!$result->num_rows) {
        $scoreId = Score::create($gameId, $playerId, $score, $ip, $country, NULL, $sign, $leaderboardId, $tags, $data, $env);
        $scoreAction = "inserted";
      } else {
        $scoreAction = "nothing";
        
        $currentScore = $result->fetch_assoc();
        $scoreId = $currentScore["score_id"];
        
        switch ($insertMode) {
          case "higher":
          case "lower":
            if ( 
              ($insertMode === "higher" && $score > $currentScore["score"]) ||
              ($insertMode === "lower" && $score < $currentScore["score"]) 
            ) {        
              Score::update($scoreId, $score, $ip, $country, $sign, $data);        
              $scoreAction = "updated";
            }
          break;

          case "sum":
            $score = $currentScore["score"] + $score;
            
            if (!is_null($minScore)) $score = max($score, $minScore);
            if (!is_null($maxScore)) $score = min($score, $maxScore);

            Score::update($scoreId, $score, $ip, $country, $sign, $data);        
            $scoreAction = "updated";
          break;        

          case "multiply":
            $score = $currentScore["score"] * $score;
            
            if (!is_null($minScore)) $score = max($score, $minScore);
            if (!is_null($maxScore)) $score = min($score, $maxScore);

            Score::update($scoreId, $score, $ip, $country, $sign, $data);        
            $scoreAction = "updated";
          break;       

          case "replace":
            Score::update($scoreId, $currentScore["score"], $ip, $country, $currentScore["sign"], $data);        
            $scoreAction = "replaced";
          break;
        }
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