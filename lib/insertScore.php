<?php

/**
 * Insert a score
 */
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
    "data" => $data,
    "minScore" => $minScore,
    "maxScore" => $maxScore
  ] = $params;

  // Create the player if not exists
  Player::create($playerName);
  $player = Player::getByName($playerName)->fetch_assoc();
  $playerId = $player["player_id"];

  switch ($insertMode) {
    case "all":
      $scoreId = Score::create($gameId, $playerId, $score, $ip, $country, NULL, $sign, $leaderboardId, $data);
      $scoreAction = "inserted";      
    break;

    default:
      // Find the current score
      $result = Score::findByGameLeaderboardAndPlayerId($gameId, $leaderboardId, $playerId);
      if (!$result->num_rows) {
        // Just insert the score if not exists
        $scoreId = Score::create($gameId, $playerId, $score, $ip, $country, NULL, $sign, $leaderboardId, $data);
        $scoreAction = "inserted";
      } else {
        // Update the previous score
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
            
            // Limit the new score to the specified range
            if (!is_null($minScore)) $score = max($score, $minScore);
            if (!is_null($maxScore)) $score = min($score, $maxScore);

            Score::update($scoreId, $score, $ip, $country, $sign, $data);        
            $scoreAction = "updated";
          break;        

          case "multiply":
            $score = $currentScore["score"] * $score;
            
            // Limit the new score to the specified range
            if (!is_null($minScore)) $score = max($score, $minScore);
            if (!is_null($maxScore)) $score = min($score, $maxScore);

            Score::update($scoreId, $score, $ip, $country, $sign, $data);        
            $scoreAction = "updated";
          break;       

          // case "divide":
          //   $score = $currentScore["score"] / $score;
            
          //   // Limit the new score to the specified range
          //   if (!is_null($minScore)) $score = max($score, $minScore);
          //   if (!is_null($maxScore)) $score = min($score, $maxScore);

          //   Score::update($scoreId, $score, $ip, $country, $sign, $data);        
          //   $scoreAction = "updated";
          // break;        

          case "replace":
            Score::update($scoreId, $currentScore["score"], $ip, $country, $currentScore["sign"], $data);        
            $scoreAction = "replaced";
          break;
        }
      }
  }

  // Get the score position 
  //$position = Score::getRankByScoreId($scoreId, $gameId);

  return [
    "scoreId" => $scoreId,
    "score" => $score,
    "scoreAction" => $scoreAction,
    "position" => 0//$position
  ];
}
