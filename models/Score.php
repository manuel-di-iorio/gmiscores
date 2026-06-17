<?php

class Score {
  public static function create(int $gameId, int $playerId, float $score, ?string $ip = NULL, ?string $country = NULL,
  ?string $createdAt = NULL, ?string $sign = NULL, ?int $leaderboardId = NULL, string $tags = 'default', ?string $data = NULL,
  string $env = 'production') {
    global $dbTableScores;
    global $db;
    $now = date('Y-m-d H:i:s');
    if ($createdAt === NULL) $createdAt = $now;
    $sql = "INSERT INTO $dbTableScores (game_id, leaderboard_id, player_id, score, ip, ip_country, created_at, updated_at, sign, tags, data, env) 
      VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    exec_query($sql, [ "iiidssssssss", $gameId, $leaderboardId, $playerId, $score, $ip, $country, $createdAt, $now, $sign, $tags, $data, $env ]);

    return $db->insert_id;
  }

  public static function findByGameLeaderboardAndPlayerId(int $gameId, int $leaderboardId, int $playerId, int $limit = 1) {
    global $dbTableScores;
    $sql = "SELECT score_id, score, sign FROM $dbTableScores WHERE game_id=? AND leaderboard_id=? AND player_id=? LIMIT ?";
    return exec_query($sql, ["iiii", $gameId, $leaderboardId, $playerId, $limit]);
  }

  public static function update(int $scoreId, float $score, ?string $ip = NULL, ?string $country = NULL, ?string $sign = NULL, ?string $data = NULL) {
    global $dbTableScores;
    $sql = "UPDATE $dbTableScores SET score=?, ip=?, ip_country=?, sign=?, data=?, updated_at=NOW() WHERE score_id=?";

    exec_query($sql, [ "dssssi", $score, $ip, $country, $sign, $data, $scoreId]);
  }

  public static function listSortedByGameId(int $gameId, int $leaderboardId, int $page, int $limit, string $order, $playerIdOrName = NULL,
  ?string $startTime = NULL, ?string $endTime = NULL, ?string $env = NULL) {
    global $dbTableScores;
    global $dbTablePlayers;

    $pageOffset = $page * $limit;

    $sql = "SELECT P.player_id, P.username, S.score_id, S.tags, S.score, S.created_at, S.updated_at, S.sign, S.data, S.env
            FROM $dbTableScores AS S
            INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
            WHERE S.game_id=? AND S.leaderboard_id=?";

    $params = ["ii", $gameId, $leaderboardId];
    
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
    if (!is_null($env)) {
      $sql .= " AND S.env=?";
      $params[0] .= "s";
      $params[] = $env;
    }

    $sql .= " ORDER BY S.score $order LIMIT ?,?";
    $params[0] .= "ii";
    $params[] = $pageOffset;
    $params[] = $limit;
    
    return exec_query($sql, $params);
  }

  public static function listByGame(int $gameId, int $page, string $sort, string $sortOrder, array $filters = []) {
    global $dbTableScores;
    global $dbTablePlayers;
    $pageOffset = $page * 100;
    $sortSanitized = preg_replace("/[^A-Za-z_]/", '', $sort);

    $allowedSortColumns = [
        'tags' => 'S.tags',
        'username' => 'P.username',
        'score' => 'S.score',
        'ip_country' => 'S.ip_country',
        'updated_at' => 'S.updated_at'
    ];

    $sqlSortExpression = $allowedSortColumns[$sortSanitized] ?? 'S.updated_at';

    $sql = "SELECT P.player_id, P.username, S.score_id, S.score, S.data, S.updated_at, S.ip_country, S.tags, S.env
            FROM $dbTableScores AS S
            INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
            WHERE S.game_id=?";

    $params = [ "i", $gameId ];

    if (!empty($filters['leaderboard_id'])) {
      $sql .= " AND S.leaderboard_id=?";
      $params[0] .= "i";
      $params[] = (int)$filters['leaderboard_id'];
    }

    if (!empty($filters['player'])) {
      $sql .= " AND P.username LIKE ?";
      $params[0] .= "s";
      $params[] = "%" . $filters['player'] . "%";
    }
    if (isset($filters['score_min']) && $filters['score_min'] !== '') {
      $sql .= " AND S.score >= ?";
      $params[0] .= "d";
      $params[] = (float)$filters['score_min'];
    }
    if (isset($filters['score_max']) && $filters['score_max'] !== '') {
      $sql .= " AND S.score <= ?";
      $params[0] .= "d";
      $params[] = (float)$filters['score_max'];
    }
    if (!empty($filters['ip_country'])) {
      $sql .= " AND S.ip_country = ?";
      $params[0] .= "s";
      $params[] = $filters['ip_country'];
    }
    if (!empty($filters['tags'])) {
      $sql .= " AND S.tags LIKE ?";
      $params[0] .= "s";
      $params[] = "%" . $filters['tags'] . "%";
    }
    if (!empty($filters['date_from'])) {
      $sql .= " AND S.updated_at >= ?";
      $params[0] .= "s";
      $params[] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
      $sql .= " AND S.updated_at <= ?";
      $params[0] .= "s";
      $params[] = $filters['date_to'];
    }
    if (!empty($filters['env'])) {
      $sql .= " AND S.env = ?";
      $params[0] .= "s";
      $params[] = $filters['env'];
    }

    $sql .= "\n            ORDER BY $sqlSortExpression $sortOrder\n            LIMIT ?,?";

    $params[0] .= "ii";
    $params[] = $pageOffset;
    $params[] = 100;

    return exec_query($sql, $params);
  }

  public static function countByGame(int $gameId, array $filters = []) {
    global $dbTableScores;
    global $dbTablePlayers;

    $sql = "SELECT COUNT(S.score_id) as count FROM $dbTableScores AS S
            INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
            WHERE S.game_id=?";

    $params = [ "i", $gameId ];

    if (!empty($filters['leaderboard_id'])) {
      $sql .= " AND S.leaderboard_id=?";
      $params[0] .= "i";
      $params[] = (int)$filters['leaderboard_id'];
    }

    if (!empty($filters['player'])) {
      $sql .= " AND P.username LIKE ?";
      $params[0] .= "s";
      $params[] = "%" . $filters['player'] . "%";
    }
    if (isset($filters['score_min']) && $filters['score_min'] !== '') {
      $sql .= " AND S.score >= ?";
      $params[0] .= "d";
      $params[] = (float)$filters['score_min'];
    }
    if (isset($filters['score_max']) && $filters['score_max'] !== '') {
      $sql .= " AND S.score <= ?";
      $params[0] .= "d";
      $params[] = (float)$filters['score_max'];
    }
    if (!empty($filters['ip_country'])) {
      $sql .= " AND S.ip_country = ?";
      $params[0] .= "s";
      $params[] = $filters['ip_country'];
    }
    if (!empty($filters['tags'])) {
      $sql .= " AND S.tags LIKE ?";
      $params[0] .= "s";
      $params[] = "%" . $filters['tags'] . "%";
    }
    if (!empty($filters['date_from'])) {
      $sql .= " AND S.updated_at >= ?";
      $params[0] .= "s";
      $params[] = $filters['date_from'];
    }
    if (!empty($filters['date_to'])) {
      $sql .= " AND S.updated_at <= ?";
      $params[0] .= "s";
      $params[] = $filters['date_to'];
    }
    if (!empty($filters['env'])) {
      $sql .= " AND S.env = ?";
      $params[0] .= "s";
      $params[] = $filters['env'];
    }

    return exec_query($sql, $params);
  }

  public static function delete(int $scoreId, int $userId) {
    global $dbTableScores;
    global $dbTableGames;
    
    $sql = "DELETE S FROM $dbTableScores AS S
            INNER JOIN $dbTableGames AS G
            ON S.game_id = G.game_id AND G.user_id = ?
            WHERE S.score_id = ?";

    exec_query($sql, [ "ii", $userId, $scoreId ]);
  }

  public static function deleteBatch(array $scoreIds, int $userId) {
    global $dbTableScores;
    global $dbTableGames;

    if (empty($scoreIds)) return;

    $ids = array_map('intval', $scoreIds);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sql = "DELETE S FROM $dbTableScores AS S
            INNER JOIN $dbTableGames AS G
            ON S.game_id = G.game_id AND G.user_id = ?
            WHERE S.score_id IN ($placeholders)";

    $params = array_merge(["i" . str_repeat("i", count($ids)), $userId], $ids);
    exec_query($sql, $params);
  }  

  public static function clear(int $gameId, int $userId, ?int $leaderboardId = NULL) {
    global $dbTableScores;
    global $dbTableGames;
    
    $sql = "DELETE S FROM $dbTableScores AS S
            LEFT JOIN $dbTableGames AS G
            ON S.game_id = G.game_id AND G.user_id = ?
            WHERE S.game_id = ?";
    $params = ["ii", $userId, $gameId];

    if (!is_null($leaderboardId)) {
      $sql .= " AND S.leaderboard_id = ?";
      $params[0] .= "i";
      $params[] = $leaderboardId;
    }

    exec_query($sql, $params);
  }

  public static function getAll(int $gameId, int $userId, ?string $env = NULL) {
    global $dbTableScores;
    global $dbTablePlayers;
    global $dbTableUsers;
    
    $sql = "SELECT P.player_id, P.username, S.score, S.ip, S.ip_country, S.created_at, S.sign, S.tags, S.leaderboard_id, S.data, S.env
    FROM $dbTableScores AS S
    INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
    INNER JOIN $dbTableUsers AS U ON S.game_id = S.game_id AND U.id=?
    WHERE S.game_id=?";
    $params = [ "ii", $userId, $gameId ];

    if (!is_null($env)) {
      $sql .= " AND S.env=?";
      $params[0] .= "s";
      $params[] = $env;
    }

    return exec_query($sql, $params);
  }

  public static function getById(int $scoreId) {
    global $dbTableScores;
    global $dbTablePlayers;
    
    $sql = "SELECT S.ip, P.player_id, P.username, S.leaderboard_id
            FROM $dbTableScores AS S
            INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
            WHERE S.score_id=?";

    return exec_query($sql, [ "i", $scoreId ]);
  }

  public static function deleteByPlayerAndGame(int $playerId, int $gameId) {
    global $dbTableScores;
    $sql = "DELETE FROM $dbTableScores WHERE player_id=? AND game_id=?";
    return exec_query($sql, [ "ii", $playerId, $gameId ]);
  }

  public static function count() {
    global $dbTableScores;
    $sql = "SELECT COUNT(score_id) AS count FROM $dbTableScores";
    $result = exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  }

  public static function getRankByScoreId($scoreId, $gameId) {
    global $dbTableScores;

    $sql = "SELECT 1 + (
                SELECT COUNT(*) FROM $dbTableScores AS T
                WHERE T.game_id = S.game_id AND T.leaderboard_id = S.leaderboard_id AND T.score > S.score
            ) AS `rank`
            FROM $dbTableScores AS S
            WHERE S.score_id = ? AND S.game_id = ?
            LIMIT 1";

    $result = exec_query($sql, ["ii", (int)$scoreId, (int)$gameId]);
    return $result->num_rows ? (int)$result->fetch_assoc()["rank"] : -1;
  }
  
  public static function getActiveGames() {
    global $dbTableScores;
    $prevTime = date('Y-m-d', strtotime('-3 MONTH'));
    $sql = "SELECT game_id FROM $dbTableScores WHERE updated_at >= '" . $prevTime . "' GROUP BY game_id";
    $result = exec_query($sql);    
    return $result->num_rows;
  }
  
  public static function getGameWithMoreScores() {
    global $dbTableScores;    
    global $dbTableGames;
    $sql = "SELECT count(S.score_id) count, G.name FROM $dbTableScores S INNER JOIN $dbTableGames G ON G.game_id = S.game_id GROUP BY G.game_id ORDER BY count DESC LIMIT 1";
    $result = exec_query($sql);    
    return $result->fetch_assoc();
  }
  
  public static function getPlayerWithMoreScores() {
    global $dbTableScores;    
    global $dbTablePlayers;
    $sql = "SELECT count(S.player_id) count, P.username FROM $dbTableScores S INNER JOIN $dbTablePlayers P ON P.player_id = S.player_id GROUP BY P.player_id LIMIT 1";
    $result = exec_query($sql);    
    return $result->fetch_assoc();
  }  
  
  public static function getUniqueCountriesCount() {
    global $dbTableScores;  
    $sql = "SELECT DISTINCT ip_country FROM $dbTableScores";
    $result = exec_query($sql);    
    return $result->num_rows;
  }

  public static function countByUser(int $userId) {
    global $dbTableScores;
    global $dbTableGames;
    $sql = "SELECT COUNT(S.score_id) AS count FROM $dbTableScores S
            INNER JOIN $dbTableGames G ON S.game_id = G.game_id
            WHERE G.user_id = ? AND S.env = 'production'";
    $result = exec_query($sql, ["i", $userId]);
    return $result->fetch_assoc()["count"] ?? 0;
  }

  public static function countByUserToday(int $userId) {
    global $dbTableScores;
    global $dbTableGames;
    $sql = "SELECT COUNT(S.score_id) AS count FROM $dbTableScores S
            INNER JOIN $dbTableGames G ON S.game_id = G.game_id
            WHERE G.user_id = ? AND S.env = 'production' AND DATE(COALESCE(S.updated_at, S.created_at)) = CURDATE()";
    $result = exec_query($sql, ["i", $userId]);
    return $result->fetch_assoc()["count"] ?? 0;
  }

  public static function getUniquePlayersByUser(int $userId) {
    global $dbTableScores;
    global $dbTableGames;
    $sql = "SELECT COUNT(DISTINCT S.player_id) AS count FROM $dbTableScores S
            INNER JOIN $dbTableGames G ON S.game_id = G.game_id
            WHERE G.user_id = ? AND S.env = 'production'";
    $result = exec_query($sql, ["i", $userId]);
    return $result->fetch_assoc()["count"] ?? 0;
  }

  public static function getCountriesByUser(int $userId) {
    global $dbTableScores;
    global $dbTableGames;
    $sql = "SELECT S.ip_country, COUNT(*) AS count FROM $dbTableScores S
            INNER JOIN $dbTableGames G ON S.game_id = G.game_id
            WHERE G.user_id = ? AND S.env = 'production' AND S.ip_country IS NOT NULL AND S.ip_country != ''
            GROUP BY S.ip_country ORDER BY count DESC";
    return exec_query($sql, ["i", $userId]);
  }

  public static function getScoresPerDayByUser(int $userId, int $days = 30) {
    global $dbTableScores;
    global $dbTableGames;
    $sql = "SELECT DATE(COALESCE(S.updated_at, S.created_at)) AS day, COUNT(*) AS count FROM $dbTableScores S
            INNER JOIN $dbTableGames G ON S.game_id = G.game_id
            WHERE G.user_id = ? AND S.env = 'production' AND COALESCE(S.updated_at, S.created_at) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(COALESCE(S.updated_at, S.created_at)) ORDER BY day ASC";
    return exec_query($sql, ["ii", $userId, $days]);
  }

  public static function getScoresByGameByUser(int $userId) {
    global $dbTableScores;
    global $dbTableGames;
    $sql = "SELECT G.name, G.game_id, COUNT(S.score_id) AS count FROM $dbTableScores S
            INNER JOIN $dbTableGames G ON S.game_id = G.game_id
            WHERE G.user_id = ? AND S.env = 'production'
            GROUP BY G.game_id ORDER BY count DESC";
    return exec_query($sql, ["i", $userId]);
  }

  public static function getCountriesByGame(int $gameId) {
    global $dbTableScores;
    $sql = "SELECT S.ip_country, COUNT(*) AS count FROM $dbTableScores S
            WHERE S.game_id = ? AND S.env = 'production' AND S.ip_country IS NOT NULL AND S.ip_country != ''
            GROUP BY S.ip_country ORDER BY count DESC";
    return exec_query($sql, ["i", $gameId]);
  }

  public static function getScoresOverTimeByGame(int $gameId, int $days = 30) {
    global $dbTableScores;
    $sql = "SELECT DATE(COALESCE(S.updated_at, S.created_at)) AS day, COUNT(*) AS count FROM $dbTableScores S
            WHERE S.game_id = ? AND S.env = 'production' AND COALESCE(S.updated_at, S.created_at) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(COALESCE(S.updated_at, S.created_at)) ORDER BY day ASC";
    return exec_query($sql, ["ii", $gameId, $days]);
  }

  public static function getScoresByLeaderboardByGame(int $gameId) {
    global $dbTableScores;
    global $dbTableLeaderboards;
    $sql = "SELECT L.name, L.leaderboard_id, COUNT(S.score_id) AS count FROM $dbTableScores S
            INNER JOIN $dbTableLeaderboards L ON S.leaderboard_id = L.leaderboard_id
            WHERE S.game_id = ? AND S.env = 'production'
            GROUP BY S.leaderboard_id ORDER BY count DESC";
    return exec_query($sql, ["i", $gameId]);
  }

  public static function getDistinctEnvsByUser(int $userId) {
    global $dbTableScores;
    global $dbTableGames;
    $sql = "SELECT DISTINCT S.env FROM $dbTableScores S
            INNER JOIN $dbTableGames G ON S.game_id = G.game_id
            WHERE G.user_id = ? AND S.env IS NOT NULL";
    return exec_query($sql, ["i", $userId]);
  }

  public static function getUniquePlayersByGame(int $gameId) {
    global $dbTableScores;
    $sql = "SELECT COUNT(DISTINCT player_id) AS count FROM $dbTableScores WHERE game_id = ? AND env = 'production'";
    $result = exec_query($sql, ["i", $gameId]);
    return $result->fetch_assoc()["count"] ?? 0;
  }

  public static function getScoresOverTime(int $days = 30) {
    global $dbTableScores;
    $sql = "SELECT DATE(COALESCE(updated_at, created_at)) AS day, COUNT(*) AS count FROM $dbTableScores
            WHERE env = 'production' AND COALESCE(updated_at, created_at) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            GROUP BY DATE(COALESCE(updated_at, created_at)) ORDER BY day ASC";
    return exec_query($sql, ["i", $days]);
  }

  public static function getScoresByGame() {
    global $dbTableScores;
    global $dbTableGames;
    $sql = "SELECT G.name, G.game_id, COUNT(S.score_id) AS count FROM $dbTableScores S
            INNER JOIN $dbTableGames G ON S.game_id = G.game_id
            WHERE S.env = 'production'
            GROUP BY G.game_id ORDER BY count DESC";
    return exec_query($sql);
  }

  public static function getCountries() {
    global $dbTableScores;
    $sql = "SELECT ip_country, COUNT(*) AS count FROM $dbTableScores
            WHERE env = 'production' AND ip_country IS NOT NULL AND ip_country != ''
            GROUP BY ip_country ORDER BY count DESC";
    return exec_query($sql);
  }
}