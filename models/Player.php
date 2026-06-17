<?php

class Player {
  /**
   * Create a player entity if not exists
   */
  public static function create(string $playerName) {
    global $dbTablePlayers;

    $sql = "INSERT INTO $dbTablePlayers (username)
            SELECT * FROM (SELECT ?) AS tmp
            WHERE NOT EXISTS (
              SELECT username FROM $dbTablePlayers WHERE username = ?
            ) LIMIT 1";

    exec_query($sql, [ "ss", $playerName, $playerName ]);
  }

  /**
   * Get the player data by name
   */
  public static function getByName(string $playerName) {
    global $dbTablePlayers;
    $sql = "SELECT * FROM $dbTablePlayers WHERE username=?";
    return exec_query($sql, [ "s", $playerName ]);
  } 

  /** Get the count of all players */
  public static function count() {
    global $dbTablePlayers;
    $sql = "SELECT COUNT(player_id) AS count FROM $dbTablePlayers";
    $result = exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  }

  public static function listAllWithScores(?string $search = null, int $page = 0, int $perPage = 50) {
    global $dbTablePlayers;
    global $dbTableScores;
    global $dbTableGames;
    global $dbTableBans;
    global $dbTableLeaderboards;
    $offset = $page * $perPage;

    $sql = "SELECT p.player_id, p.username,
                   COUNT(s.score_id) AS total_scores,
                   (SELECT g2.name FROM $dbTableScores s2
                    INNER JOIN $dbTableGames g2 ON s2.game_id = g2.game_id
                    WHERE s2.player_id = p.player_id
                    GROUP BY s2.game_id
                    ORDER BY COUNT(s2.score_id) DESC
                    LIMIT 1) AS top_game,
                   (SELECT g2.game_id FROM $dbTableScores s2
                    INNER JOIN $dbTableGames g2 ON s2.game_id = g2.game_id
                    WHERE s2.player_id = p.player_id
                    GROUP BY s2.game_id
                    ORDER BY COUNT(s2.score_id) DESC
                    LIMIT 1) AS top_game_id,
                   (SELECT s3.score FROM $dbTableScores s3
                    WHERE s3.player_id = p.player_id
                    AND s3.game_id = (SELECT s4.game_id FROM $dbTableScores s4
                                      INNER JOIN $dbTableGames g4 ON s4.game_id = g4.game_id
                                      WHERE s4.player_id = p.player_id
                                      GROUP BY s4.game_id
                                      ORDER BY COUNT(s4.score_id) DESC
                                      LIMIT 1)
                    AND s3.leaderboard_id = (SELECT MIN(l.leaderboard_id) FROM $dbTableLeaderboards l
                                             WHERE l.game_id = (SELECT s4.game_id FROM $dbTableScores s4
                                                                INNER JOIN $dbTableGames g4 ON s4.game_id = g4.game_id
                                                                WHERE s4.player_id = p.player_id
                                                                GROUP BY s4.game_id
                                                                ORDER BY COUNT(s4.score_id) DESC
                                                                LIMIT 1))
                    ORDER BY s3.score DESC
                    LIMIT 1) AS top_score,
                   CASE WHEN EXISTS (
                     SELECT 1 FROM $dbTableBans b
                     WHERE b.player_id = p.player_id
                   ) THEN 1 ELSE 0 END AS has_bans
            FROM $dbTablePlayers p
            LEFT JOIN $dbTableScores s ON s.player_id = p.player_id";

    $conditions = [];
    $params = [];
    $types = "";

    if (!is_null($search) && $search !== '') {
      $conditions[] = "p.username LIKE ?";
      $types .= "s";
      $params[] = "%" . $search . "%";
    }

    if ($conditions) {
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " GROUP BY p.player_id ORDER BY top_score IS NULL, top_score DESC, total_scores DESC LIMIT ?,?";
    $types .= "ii";
    $params[] = $offset;
    $params[] = $perPage;

    return exec_query($sql, array_merge([$types], $params));
  }

  public static function getByIdWithScores(int $playerId) {
    global $dbTablePlayers;
    global $dbTableScores;
    global $dbTableGames;
    global $dbTableBans;

    $sql = "SELECT p.player_id, p.username,
                   (SELECT g2.game_id FROM $dbTableScores s2
                    INNER JOIN $dbTableGames g2 ON s2.game_id = g2.game_id
                    WHERE s2.player_id = p.player_id
                    GROUP BY s2.game_id
                    ORDER BY COUNT(s2.score_id) DESC
                    LIMIT 1) AS top_game_id
            FROM $dbTablePlayers p
            WHERE p.player_id = ?
            LIMIT 1";

    $result = exec_query($sql, ["i", $playerId]);
    return $result->num_rows ? $result->fetch_assoc() : null;
  }

  public static function countAllWithScores(?string $search = null) {
    global $dbTablePlayers;
    global $dbTableScores;

    $sql = "SELECT COUNT(DISTINCT p.player_id) AS count
            FROM $dbTablePlayers p
            LEFT JOIN $dbTableScores s ON s.player_id = p.player_id";

    $conditions = [];
    $params = [];
    $types = "";

    if (!is_null($search) && $search !== '') {
      $conditions[] = "p.username LIKE ?";
      $types .= "s";
      $params[] = "%" . $search . "%";
    }

    if ($conditions) {
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $result = $types ? exec_query($sql, array_merge([$types], $params)) : exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  }

}
