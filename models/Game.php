<?php

class Game {
  public static array $schema = [
    'table'       => 'games',
    'primaryKey'  => 'game_id',
    'timestamps'  => true,
    'columns'     => [
      'game_id'       => ['type' => 'int',      'auto' => true],
      'name'          => ['type' => 'string'],
      'client_secret' => ['type' => 'string'],
      'user_id'       => ['type' => 'int'],
      'team_id'       => ['type' => 'int',      'nullable' => true],
      'require_player_auth' => ['type' => 'bool', 'default' => false],
      'created_at'    => ['type' => 'datetime'],
      'updated_at'    => ['type' => 'datetime'],
    ],
    'indexes'     => [
      ['columns' => ['user_id']],
      ['columns' => ['team_id']],
      ['columns' => ['user_id', 'team_id']],
    ],
    'foreignKeys' => [
      ['columns' => ['user_id'], 'references' => ['users', 'id']],
      ['columns' => ['team_id'], 'references' => ['teams', 'team_id']],
    ],
    'relations'   => [
      'scores'       => ['type' => 'hasMany',   'model' => 'Score',       'foreignKey' => 'game_id'],
      'leaderboards' => ['type' => 'hasMany',   'model' => 'Leaderboard', 'foreignKey' => 'game_id'],
      'user'         => ['type' => 'belongsTo', 'model' => 'User',        'foreignKey' => 'user_id'],
      'team'         => ['type' => 'belongsTo', 'model' => 'Team',        'foreignKey' => 'team_id'],
    ],
  ];
  public static function getClientSecretById(string $gameId) {
    global $dbTableGames;
    $sql = "SELECT client_secret FROM $dbTableGames WHERE game_id=?";
    return exec_query($sql, [ "i", $gameId ]);
  }

  /**
   * Check if a game requires player authentication
   */
  public static function requiresPlayerAuth(int $gameId) {
    global $dbTableGames;
    $sql = "SELECT require_player_auth FROM $dbTableGames WHERE game_id=?";
    $result = exec_query($sql, [ "i", $gameId ]);
    if ($result->num_rows) {
      return (bool)$result->fetch_assoc()["require_player_auth"];
    }
    return false;
  }

  /**
   * Toggle require_player_auth for a game
   */
  public static function toggleRequirePlayerAuth(int $gameId, int $userId) {
    global $dbTableGames;
    $sql = "UPDATE $dbTableGames SET require_player_auth = NOT require_player_auth WHERE game_id = ? AND user_id = ?";
    exec_query($sql, [ "ii", $gameId, $userId ]);
  }

  public static function listByUser(int $userId, ?string $nameFilter = null) {
    global $dbTableGames;
    global $dbTableScores;

    $sql = "SELECT G.game_id, G.name, COUNT(S.score_id) AS _scoresCount, COUNT(DISTINCT S.player_id) as _playersCount
            FROM $dbTableGames AS G
            LEFT JOIN $dbTableScores AS S ON G.game_id = S.game_id
            WHERE G.user_id=? AND G.team_id IS NULL";

    $params = [ "i", $userId ];

    if (!is_null($nameFilter) && $nameFilter !== '') {
      $sql .= " AND G.name LIKE ?";
      $params[0] .= "s";
      $params[] = "%" . $nameFilter . "%";
    }

    $sql .= "\n            GROUP BY G.game_id\n            LIMIT 200";

    return exec_query($sql, $params);
  }

  public static function listByTeam(int $teamId, ?string $nameFilter = null) {
    global $dbTableGames;
    global $dbTableScores;

    $sql = "SELECT G.game_id, G.name, COUNT(S.score_id) AS _scoresCount, COUNT(DISTINCT S.player_id) as _playersCount
            FROM $dbTableGames AS G
            LEFT JOIN $dbTableScores AS S ON G.game_id = S.game_id
            WHERE G.team_id=?";

    $params = [ "i", $teamId ];

    if (!is_null($nameFilter) && $nameFilter !== '') {
      $sql .= " AND G.name LIKE ?";
      $params[0] .= "s";
      $params[] = "%" . $nameFilter . "%";
    }

    $sql .= "\n            GROUP BY G.game_id\n            LIMIT 200";

    return exec_query($sql, $params);
  }

  public static function create(int $userId, string $gameName, string $clientSecret, ?int $teamId = NULL, bool $requirePlayerAuth = false) {
    global $dbTableGames;
    if (is_null($teamId)) {
      $sql = "INSERT INTO $dbTableGames (name, user_id, client_secret, require_player_auth) VALUES(?, ?, ?, ?)";
      exec_query($sql, [ "sisi", $gameName, $userId, $clientSecret, $requirePlayerAuth ? 1 : 0 ]);
    } else {
      $sql = "INSERT INTO $dbTableGames (name, user_id, team_id, client_secret, require_player_auth) VALUES(?, ?, ?, ?, ?)";
      exec_query($sql, [ "sisii", $gameName, $userId, $teamId, $clientSecret, $requirePlayerAuth ? 1 : 0 ]);
    }
  }

  public static function delete(int $gameId, int $userId) {
    global $dbTableGames;
    $sql = "DELETE FROM $dbTableGames WHERE game_id = ? AND user_id = ?";
    exec_query($sql, [ "ii", $gameId, $userId ]);
  }

  /**
   * Admin-only: delete game without user_id check.
   * Callers must verify admin status before using this method.
   */
  public static function deleteById(int $gameId) {
    global $dbTableGames;
    $sql = "DELETE FROM $dbTableGames WHERE game_id = ?";
    exec_query($sql, [ "i", $gameId ]);
  }

  public static function rename(int $gameId, int $userId, string $name) {
    global $dbTableGames;
    $sql = "UPDATE $dbTableGames SET name=? WHERE game_id=? AND user_id=?";
    exec_query($sql, [ "sii", $name, $gameId, $userId ]);
  }

  public static function regenerateSecret(int $gameId, int $userId, string $client_secret) {
    global $dbTableGames;
    $sql = "UPDATE $dbTableGames SET client_secret=? WHERE game_id=? AND user_id=?";
    exec_query($sql, [ "sii", $client_secret, $gameId, $userId ]);
  }

  public static function getById(int $gameId) {
    global $dbTableGames;
    $sql = "SELECT game_id FROM $dbTableGames WHERE game_id=?";
    return exec_query($sql, [ "i", $gameId ]);
  }

  public static function getByIdAndUser(int $gameId, int $userId) {
    global $dbTableGames;
    $sql = "SELECT game_id, name, client_secret, team_id FROM $dbTableGames WHERE game_id=? AND user_id=?";
    return exec_query($sql, [ "ii", $gameId, $userId ]);
  }

  public static function getByIdAndTeam(int $gameId, int $teamId) {
    global $dbTableGames;
    $sql = "SELECT game_id, name, client_secret, team_id FROM $dbTableGames WHERE game_id=? AND team_id=?";
    return exec_query($sql, [ "ii", $gameId, $teamId ]);
  }

  public static function getByIdWithAccess(int $gameId, int $userId) {
    global $dbTableGames;
    global $dbTableTeamMembers;
    $sql = "SELECT G.game_id, G.name, G.client_secret, G.team_id, G.require_player_auth
            FROM $dbTableGames G
            LEFT JOIN $dbTableTeamMembers TM ON G.team_id = TM.team_id AND TM.user_id = ?
            WHERE G.game_id = ? AND (G.user_id = ? OR TM.id IS NOT NULL)
            LIMIT 1";
    $result = exec_query($sql, [ "iii", $userId, $gameId, $userId]);
    return $result->num_rows ? $result : null;
  }

  public static function moveToTeam(int $gameId, int $userId, ?int $targetTeamId) {
    global $dbTableGames;
    $sql = "UPDATE $dbTableGames SET team_id = ? WHERE game_id = ? AND user_id = ?";
    exec_query($sql, [ "iii", $targetTeamId, $gameId, $userId ]);
  }

  public static function moveToTeamWithAccess(int $gameId, int $userId, ?int $targetTeamId) {
    global $dbTableGames;
    global $dbTableTeamMembers;

    if ($targetTeamId !== null) {
      $sql = "UPDATE $dbTableGames AS G SET team_id = ?
              WHERE G.game_id = ? AND (
                G.user_id = ? OR
                EXISTS (SELECT 1 FROM $dbTableTeamMembers TM WHERE TM.team_id = G.team_id AND TM.user_id = ?)
              )";
      exec_query($sql, [ "iiii", $targetTeamId, $gameId, $userId, $userId ]);
    } else {
      $sql = "UPDATE $dbTableGames AS G SET team_id = NULL
              WHERE G.game_id = ? AND (
                G.user_id = ? OR
                EXISTS (SELECT 1 FROM $dbTableTeamMembers TM WHERE TM.team_id = G.team_id AND TM.user_id = ?)
              )";
      exec_query($sql, [ "iii", $gameId, $userId, $userId ]);
    }
  }

  public static function count() {
    global $dbTableGames;
    $sql = "SELECT COUNT(game_id) AS count FROM $dbTableGames";
    $result = exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  }

  public static function countByUser(int $userId) {
    global $dbTableGames;
    $sql = "SELECT COUNT(game_id) AS count FROM $dbTableGames WHERE user_id = ? AND team_id IS NULL";
    $result = exec_query($sql, ["i", $userId]);
    return $result->fetch_assoc()["count"] ?? 0;
  }

  public static function countByTeamId(int $teamId) {
    global $dbTableGames;
    $sql = "SELECT COUNT(game_id) AS count FROM $dbTableGames WHERE team_id = ?";
    $result = exec_query($sql, ["i", $teamId]);
    return $result->fetch_assoc()["count"] ?? 0;
  }

  public static function getLeaderboardCountByGame(int $gameId) {
    global $dbTableLeaderboards;
    $sql = "SELECT COUNT(leaderboard_id) AS count FROM $dbTableLeaderboards WHERE game_id = ?";
    $result = exec_query($sql, ["i", $gameId]);
    return $result->fetch_assoc()["count"] ?? 0;
  }

  public static function countByTeam(int $teamId) {
    global $dbTableGames;
    $sql = "SELECT COUNT(game_id) AS count FROM $dbTableGames WHERE team_id = ?";
    $result = exec_query($sql, ["i", $teamId]);
    return $result->fetch_assoc()["count"] ?? 0;
  }

  public static function renameWithAccess(int $gameId, int $userId, string $name) {
    global $dbTableGames;
    global $dbTableTeamMembers;
    $sql = "UPDATE $dbTableGames AS G SET name=?
            WHERE G.game_id=? AND (
              G.user_id = ? OR
              EXISTS (SELECT 1 FROM $dbTableTeamMembers TM WHERE TM.team_id = G.team_id AND TM.user_id = ?)
            )";
    exec_query($sql, [ "siii", $name, $gameId, $userId, $userId ]);
  }

  public static function deleteWithAccess(int $gameId, int $userId) {
    global $dbTableGames;
    global $dbTableTeamMembers;
    $sql = "DELETE G FROM $dbTableGames AS G
            WHERE G.game_id=? AND (
              G.user_id = ? OR
              EXISTS (SELECT 1 FROM $dbTableTeamMembers TM WHERE TM.team_id = G.team_id AND TM.user_id = ?)
            )";
    exec_query($sql, [ "iii", $gameId, $userId, $userId ]);
  }

  public static function regenerateSecretWithAccess(int $gameId, int $userId, string $client_secret) {
    global $dbTableGames;
    global $dbTableTeamMembers;
    $sql = "UPDATE $dbTableGames AS G SET client_secret=?
            WHERE G.game_id=? AND (
              G.user_id = ? OR
              EXISTS (SELECT 1 FROM $dbTableTeamMembers TM WHERE TM.team_id = G.team_id AND TM.user_id = ?)
            )";
    exec_query($sql, [ "siii", $client_secret, $gameId, $userId, $userId ]);
  }
}
