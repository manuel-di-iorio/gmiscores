<?php

class Player {
  public static array $schema = [
    'table'      => 'players',
    'primaryKey' => 'player_id',
    'timestamps' => false,
    'columns'    => [
      'player_id' => ['type' => 'int',    'auto' => true],
      'username'  => ['type' => 'string', 'unique' => true],
      'user_id'   => ['type' => 'int',    'nullable' => true],
      'game_id'   => ['type' => 'int',    'nullable' => true],
    ],
    'indexes'    => [
      ['columns' => ['username'], 'unique' => true],
    ],
    'relations'  => [
      'scores' => ['type' => 'hasMany', 'model' => 'Score', 'foreignKey' => 'player_id'],
      'bans'   => ['type' => 'hasMany', 'model' => 'Ban',   'foreignKey' => 'player_id'],
    ],
  ];
  /**
   * Create a player entity if not exists
   */
  public static function create(string $playerName, ?int $gameId = null) {
    global $dbTablePlayers;

    $sql = "INSERT INTO $dbTablePlayers (username, game_id)
            SELECT ?, ?
            WHERE NOT EXISTS (
              SELECT username FROM $dbTablePlayers WHERE username = ?
            ) LIMIT 1";

    exec_query($sql, [ "ssi", $playerName, $gameId, $playerName ]);
  }

  /**
   * Get the player data by name
   */
  public static function getByName(string $playerName) {
    global $dbTablePlayers;
    $sql = "SELECT * FROM $dbTablePlayers WHERE username=?";
    return exec_query($sql, [ "s", $playerName ]);
  }

  /**
   * Get the player by user_id
   */
  public static function getByUserId(int $userId) {
    global $dbTablePlayers;
    $sql = "SELECT * FROM $dbTablePlayers WHERE user_id=?";
    return exec_query($sql, [ "i", $userId ]);
  }

  /**
   * Get or create a player for an authenticated user.
   * If a guest player with the same username exists, link it to the user.
   */
  public static function getOrCreateForUser(int $userId, ?string $playerName = null, ?int $gameId = null) {
    global $dbTablePlayers;

    $result = Player::getByUserId($userId);
    if ($result->num_rows) {
      return $result->fetch_assoc();
    }

    if ($playerName) {
      $existing = Player::getByName($playerName);
      if ($existing->num_rows) {
        $player = $existing->fetch_assoc();
        if (empty($player["user_id"])) {
          $sql = "UPDATE $dbTablePlayers SET user_id = ?, game_id = ? WHERE player_id = ?";
          exec_query($sql, ["iii", $userId, $gameId, $player["player_id"]]);
          $player["user_id"] = $userId;
          $player["game_id"] = $gameId;
        }
        return $player;
      }
    }

    Player::createWithUser($userId, $playerName, $gameId);
    $result = Player::getByUserId($userId);
    return $result->fetch_assoc();
  }

  /**
   * Create a player linked to a user
   */
  public static function createWithUser(int $userId, ?string $playerName = null, ?int $gameId = null) {
    global $dbTablePlayers;

    $username = $playerName ?? '';
    $sql = "INSERT INTO $dbTablePlayers (username, user_id, game_id)
            SELECT ?, ?, ?
            WHERE NOT EXISTS (
              SELECT user_id FROM $dbTablePlayers WHERE user_id = ?
            ) LIMIT 1";

    exec_query($sql, [ "ssii", $username, $userId, $gameId, $userId ]);
  }

  /** Get the count of all players */
  public static function count() {
    global $dbTablePlayers;
    $sql = "SELECT COUNT(player_id) AS count FROM $dbTablePlayers";
    $result = exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  }

  public static function listAllWithScores(?string $search = null, int $page = 0, int $perPage = 50, ?string $sortBy = null, ?string $sortDir = null, bool $bannedOnly = false) {
    global $dbTablePlayers;
    global $dbTableUsers;
    global $dbTableScores;
    global $dbTableGames;
    global $dbTableBans;
    $offset = $page * $perPage;

    $allowedSorts = [
      'id' => 'p.player_id',
      'username' => 'COALESCE(u.username, p.username)',
      'top_score' => 'top_score',
      'game' => 'top_game',
    ];
    $sortCol = $allowedSorts[$sortBy] ?? null;
    $sortDirection = strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';

    $sql = "SELECT p.player_id, COALESCE(u.username, p.username) AS username,
                   (SELECT g2.name FROM $dbTableScores s2
                    INNER JOIN $dbTableGames g2 ON s2.game_id = g2.game_id
                    WHERE s2.player_id = p.player_id
                    GROUP BY s2.game_id
                    ORDER BY COUNT(s2.score_id) DESC
                    LIMIT 1) AS top_game,
                   (SELECT MAX(s3.score) FROM $dbTableScores s3
                    WHERE s3.player_id = p.player_id
                    AND s3.game_id = (
                      SELECT s2.game_id FROM $dbTableScores s2
                      WHERE s2.player_id = p.player_id
                      GROUP BY s2.game_id
                      ORDER BY COUNT(s2.score_id) DESC
                      LIMIT 1
                    )) AS top_score,
                   CASE WHEN EXISTS (
                     SELECT 1 FROM $dbTableBans b
                     WHERE b.player_id = p.player_id
                   ) THEN 1 ELSE 0 END AS has_bans
            FROM $dbTablePlayers p
            LEFT JOIN $dbTableUsers u ON p.user_id = u.id";

    $conditions = [];
    $params = [];
    $types = "";

    if (!is_null($search) && $search !== '') {
      $conditions[] = "(p.username LIKE ? OR u.username LIKE ?)";
      $types .= "ss";
      $params[] = "%" . $search . "%";
      $params[] = "%" . $search . "%";
    }

    if ($bannedOnly) {
      $conditions[] = "EXISTS (SELECT 1 FROM $dbTableBans b WHERE b.player_id = p.player_id)";
    }

    if ($conditions) {
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    if ($sortCol) {
      $sql .= " ORDER BY $sortCol $sortDirection, p.player_id DESC";
    } else {
      $sql .= " ORDER BY top_score IS NULL, top_score DESC";
    }

    $sql .= " LIMIT ?,?";
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

  public static function listByGameWithBanStatus(int $gameId, ?string $search = null, bool $bannedOnly = false, int $page = 0, int $perPage = 50) {
    global $dbTablePlayers;
    global $dbTableUsers;
    global $dbTableScores;
    global $dbTableBans;

    $offset = $page * $perPage;

    $sql = "SELECT DISTINCT p.player_id, COALESCE(u.username, p.username) AS username, p.user_id,
                   CASE WHEN EXISTS (
                     SELECT 1 FROM $dbTableBans b
                     WHERE b.player_id = p.player_id AND b.game_id = ?
                   ) THEN 1 ELSE 0 END AS is_banned
            FROM $dbTablePlayers p
            LEFT JOIN $dbTableUsers u ON p.user_id = u.id
            INNER JOIN $dbTableScores s ON s.player_id = p.player_id AND s.game_id = ?
            WHERE 1=1";

    $params = [ "ii", $gameId, $gameId ];

    if (!is_null($search) && $search !== '') {
      $sql .= " AND (p.username LIKE ? OR u.username LIKE ?)";
      $params[0] .= "ss";
      $params[] = "%" . $search . "%";
      $params[] = "%" . $search . "%";
    }

    if ($bannedOnly) {
      $sql .= " AND EXISTS (SELECT 1 FROM $dbTableBans b WHERE b.player_id = p.player_id AND b.game_id = ?)";
      $params[0] .= "i";
      $params[] = $gameId;
    } else {
      $sql .= " AND NOT EXISTS (SELECT 1 FROM $dbTableBans b WHERE b.player_id = p.player_id AND b.game_id = ?)";
      $params[0] .= "i";
      $params[] = $gameId;
    }

    $sql .= " ORDER BY p.username ASC LIMIT ?,?";
    $params[0] .= "ii";
    $params[] = $offset;
    $params[] = $perPage;

    return exec_query($sql, $params);
  }

  public static function countByGameWithBanStatus(int $gameId, ?string $search = null, bool $bannedOnly = false) {
    global $dbTablePlayers;
    global $dbTableUsers;
    global $dbTableScores;
    global $dbTableBans;

    $sql = "SELECT COUNT(DISTINCT p.player_id) AS count
            FROM $dbTablePlayers p
            LEFT JOIN $dbTableUsers u ON p.user_id = u.id
            INNER JOIN $dbTableScores s ON s.player_id = p.player_id AND s.game_id = ?
            WHERE 1=1";

    $params = [ "i", $gameId ];

    if (!is_null($search) && $search !== '') {
      $sql .= " AND (p.username LIKE ? OR u.username LIKE ?)";
      $params[0] .= "ss";
      $params[] = "%" . $search . "%";
      $params[] = "%" . $search . "%";
    }

    if ($bannedOnly) {
      $sql .= " AND EXISTS (SELECT 1 FROM $dbTableBans b WHERE b.player_id = p.player_id AND b.game_id = ?)";
      $params[0] .= "i";
      $params[] = $gameId;
    } else {
      $sql .= " AND NOT EXISTS (SELECT 1 FROM $dbTableBans b WHERE b.player_id = p.player_id AND b.game_id = ?)";
      $params[0] .= "i";
      $params[] = $gameId;
    }

    $result = exec_query($sql, $params);
    return $result->num_rows ? (int)$result->fetch_assoc()["count"] : 0;
  }

  public static function countAllWithScores(?string $search = null, bool $bannedOnly = false) {
    global $dbTablePlayers;
    global $dbTableUsers;
    global $dbTableBans;

    $sql = "SELECT COUNT(p.player_id) AS count
            FROM $dbTablePlayers p
            LEFT JOIN $dbTableUsers u ON p.user_id = u.id";

    $conditions = [];
    $params = [];
    $types = "";

    if (!is_null($search) && $search !== '') {
      $conditions[] = "(p.username LIKE ? OR u.username LIKE ?)";
      $types .= "ss";
      $params[] = "%" . $search . "%";
      $params[] = "%" . $search . "%";
    }

    if ($bannedOnly) {
      $conditions[] = "EXISTS (SELECT 1 FROM $dbTableBans b WHERE b.player_id = p.player_id)";
    }

    if ($conditions) {
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $result = $types ? exec_query($sql, array_merge([$types], $params)) : exec_query($sql);
    return $result->num_rows ? (int)$result->fetch_assoc()["count"] : 0;
  }

}
