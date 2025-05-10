<?php

class Score {
  /**
   * Insert a score entity
   */
  public static function create(string $gameId, int $playerId, float $score, string $ip=NULL, string $country=NULL, 
  string $createdAt=NULL, string $sign=NULL, string $leaderboard, string $data=NULL) {
    global $dbTableScores;
    global $db;
    $sql = "INSERT INTO $dbTableScores (game_id, player_id, score, ip, ip_country, created_at, sign, leaderboard_id, data) 
      VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
    exec_query($sql, [ "iidssssss", $gameId, $playerId, $score, $ip, $country, $createdAt, $sign, $leaderboard, $data ]);

    return $db->insert_id;
  }

  /**
   * Find a score record
   */
  public static function findByGameLeaderboardAndPlayerId(string $gameId, string $leaderboardId, int $playerId, int $limit = 1) {
    global $dbTableScores;
    $sql = "SELECT score_id, score, sign FROM $dbTableScores WHERE game_id=? AND leaderboard_id=? AND player_id=? LIMIT ?";
    return exec_query($sql, [ "isii", $gameId, $leaderboardId, $playerId, $limit]);
  }

  /**
   * Update a score entity
   */
  public static function update(int $scoreId, float $score, string $ip=NULL, string $country=NULL, string $sign=NULL, string $data=NULL) {
    global $dbTableScores;
    $sql = "UPDATE $dbTableScores SET score=?, ip=?, ip_country=?, sign=?, data=?, updated_at=NOW() WHERE score_id=?";

    exec_query($sql, [ "dssssi", $score, $ip, $country, $sign, $data, $scoreId]);
  }

  /**
   * Get the sorted scores by the game ID
   */
  public static function listSortedByGameId(int $gameId, string $leaderboardId, int $page, int $limit, string $order, $playerIdOrName = NULL,
  string $startTime = NULL, string $endTime = NULL) {
    global $dbTableScores;
    global $dbTablePlayers;

    $pageOffset = $page * $limit;

    $sql = "SELECT P.player_id, P.username, S.score_id, S.leaderboard_id, S.score, S.created_at, S.updated_at, S.sign, S.data
            FROM $dbTableScores AS S
            INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
            WHERE S.game_id=? AND S.leaderboard_id=?";

    $params = ["is", $gameId, $leaderboardId];
    
    // Filter by player if specificed
    if (!is_null($playerIdOrName)) {
      if (is_numeric($playerIdOrName)) {
        $sql .= " AND P.player_id=?";
        $params[0] .= "i";
        $params[] = (int)$playerIdOrName;
      } else {
        $sql .= " AND P.username=?";
        $params[0] .= "s";
        $params[] = base64_decode($playerIdOrName);
      }
    }

    // Filter by time if specified
    if (!is_null($startTime)) {
      $sql .= " AND S.updated_at>=?";
      $params[0] .= "s";
      $params[] = $startTime;
    }
    if (!is_null($endTime)) {
      $sql .= " AND S.updated_at<=?";
      $params[0] .= "s";
      $params[] = $endTime;
    }

    // Sorting filters
    $sql .= " ORDER BY S.score $order LIMIT ?,?";
    $params[0] .= "ii";
    $params[] = $pageOffset;
    $params[] = $limit;
    
    return exec_query($sql, $params);
  }

  /**
   * Get the scores list with pagination by the game
   */
  public static function listByGame(int $gameId, int $page, string $sort, string $sortOrder) {
    global $dbTableScores;
    global $dbTablePlayers;
    $pageOffset = $page * 100;
    $sort = preg_replace("/[^a-z_]/", '', $sort); // Sanitization

    $sql = "SELECT P.player_id, P.username, S.score_id, S.score, S.data, S.updated_at, S.ip_country, S.leaderboard_id
            FROM $dbTableScores AS S
            INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
            WHERE S.game_id=?
            ORDER BY S.$sort $sortOrder
            LIMIT $pageOffset,100";

    return exec_query($sql, [ "i", $gameId ]);
  }
  
  /**
   * Get the scores count by the game
   */
  public static function countByGame(int $gameId) {
    global $dbTableScores;
    $sql = "SELECT COUNT(score_id) as count FROM $dbTableScores WHERE game_id=?";
    return exec_query($sql, [ "i", $gameId ]);
  }

  /**
   * Delete a score by the score ID and user
   */
  public static function delete(int $scoreId, int $userId) {
    global $dbTableScores;
    global $dbTableGames;
    
    $sql = "DELETE S FROM $dbTableScores AS S
            LEFT JOIN $dbTableGames AS G
            ON S.game_id = G.game_id AND G.user_id = ?
            WHERE S.score_id = ?";

    exec_query($sql, [ "ii", $userId, $scoreId ]);
  }  

  /**
   * Clear the scores of a game
   */
  public static function clear(int $gameId, int $userId) {
    global $dbTableScores;
    global $dbTableGames;
    
    $sql = "DELETE S FROM $dbTableScores AS S
            LEFT JOIN $dbTableGames AS G
            ON S.game_id = G.game_id AND G.user_id = ?
            WHERE S.game_id = ?";

    exec_query($sql, [ "ii", $userId, $gameId ]);
  }

  /**
   * Get all the scores of a game
   */
  public static function getAll(int $gameId, int $userId) {
    global $dbTableScores;
    global $dbTablePlayers;
    global $dbTableUsers;
    
    $sql = "SELECT P.player_id, P.username, S.score, S.ip, S.ip_country, S.created_at, S.sign, S.leaderboard_id, S.data
    FROM $dbTableScores AS S
    INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
    INNER JOIN $dbTableUsers AS U ON S.game_id = S.game_id AND U.id=?
    WHERE S.game_id=?";

    return exec_query($sql, [ "ii", $userId, $gameId ]);
  }

  /**
   * Get by ID
   */
  public static function getById(int $scoreId) {
    global $dbTableScores;
    global $dbTablePlayers;
    
    $sql = "SELECT S.ip, P.player_id, P.username
            FROM $dbTableScores AS S
            INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
            WHERE S.score_id=?";

    return exec_query($sql, [ "i", $scoreId ]);
  }

  /**
   * Delete by player ID and game ID
   */
  public static function deleteByPlayerAndGame(int $playerId, int $gameId) {
    global $dbTableScores;
    $sql = "DELETE FROM $dbTableScores WHERE player_id=? AND game_id=?";
    return exec_query($sql, [ "ii", $playerId, $gameId ]);
  }

  /** Get the count of all scores */
  public static function count() {
    global $dbTableScores;
    $sql = "SELECT COUNT(score_id) AS count FROM $dbTableScores";
    $result = exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  }

  /**
   * Get the precise position of a score
   */
  public static function getRankByScoreId($scoreId, $gameId) {
    global $dbTableScores;
    $sql = "SELECT rank FROM (SELECT @r:=@r+1 AS rank, score_id FROM $dbTableScores, (SELECT @r:=0) a WHERE game_id=$gameId ORDER BY score DESC) result WHERE score_id=$scoreId";
    $result = exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["rank"] : -1;
  }
  
  /**
   * Get the count of actives games
   */
  public static function getActiveGames() {
    global $dbTableScores;
    $prevTime = date('Y-m-d', strtotime('-3 MONTH'));
    $sql = "SELECT game_id FROM $dbTableScores WHERE updated_at >= '" . $prevTime . "' GROUP BY game_id";
    $result = exec_query($sql);    
    return $result->num_rows;
  }
  
  /**
   * Get the game with most scores
   */
  public static function getGameWithMoreScores() {
    global $dbTableScores;    
    global $dbTableGames;
    $sql = "SELECT count(S.score_id) count, G.name FROM $dbTableScores S INNER JOIN $dbTableGames G ON G.game_id = S.game_id GROUP BY G.game_id ORDER BY count DESC LIMIT 1";
    $result = exec_query($sql);    
    return $result->fetch_assoc();
  }
  
  /**
   * Get the player with more scores
   */
  public static function getPlayerWithMoreScores() {
    global $dbTableScores;    
    global $dbTablePlayers;
    $sql = "SELECT count(S.player_id) count, P.username FROM $dbTableScores S INNER JOIN $dbTablePlayers P ON P.player_id = S.player_id GROUP BY P.player_id LIMIT 1";
    $result = exec_query($sql);    
    return $result->fetch_assoc();
  }  
  
  /**
   * Get the count of unique countries collected from the scores 
   */
  public static function getUniqueCountriesCount() {
    global $dbTableScores;  
    $sql = "SELECT DISTINCT ip_country FROM $dbTableScores";
    $result = exec_query($sql);    
    return $result->num_rows;
  }
}
