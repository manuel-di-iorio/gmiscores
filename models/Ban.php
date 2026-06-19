<?php

class Ban {
  /**
   * Add a player ban for a game
   */
  public static function add(int $playerId, string $playerName, ?string $ip, int $gameId) {
    global $dbTableBans;
    $sql = "INSERT INTO $dbTableBans (player_id, player_name, ip, game_id) VALUES (?, ?, ?, ?)";
    exec_query($sql, [ "issi", $playerId, $playerName, $ip, $gameId ]);
  }

  /**
   * Get by ID and user
   */
  public static function getByIdAndUser(int $banId, int $userId) {
    global $dbTableBans;
    global $dbTableGames;
    global $dbTableTeamMembers;

    $sql = "SELECT B.game_id
            FROM $dbTableBans AS B
            INNER JOIN $dbTableGames AS G ON B.game_id=G.game_id
            LEFT JOIN $dbTableTeamMembers TM ON G.team_id = TM.team_id AND TM.user_id=?
            WHERE B.ban_id=? AND (G.user_id=? OR TM.id IS NOT NULL)";

    return exec_query($sql, [ "iii", $userId, $banId, $userId ]);
  }

  /**
   * Delete a player ban for a game
   */
  public static function remove(int $banId, int $userId) {
    global $dbTableBans;
    global $dbTableGames;
    global $dbTableTeamMembers;
    $sql = "DELETE B FROM $dbTableBans AS B
            INNER JOIN $dbTableGames AS G ON B.game_id=G.game_id
            LEFT JOIN $dbTableTeamMembers TM ON G.team_id = TM.team_id AND TM.user_id=?
            WHERE B.ban_id=? AND (G.user_id=? OR TM.id IS NOT NULL)";
    exec_query($sql, [ "iii", $userId, $banId, $userId ]);
  }

  /**
   * List the players ban for a game
   */
  public static function list(int $gameId, ?string $playerName = null) {
    global $dbTableBans;
    global $dbTablePlayers;

    $sql = "SELECT ban_id, player_id, player_name, created_at
            FROM $dbTableBans
            WHERE game_id=?";

    $params = [ "i", $gameId ];

    if (!is_null($playerName) && $playerName !== '') {
      $sql .= " AND player_name LIKE ?";
      $params[0] .= "s";
      $params[] = "%" . $playerName . "%";
    }

    $sql .= "\n            ORDER BY created_at DESC\n            LIMIT 200";

    return exec_query($sql, $params);
  }

  /**
   * Check if the player is banned
   */
  public static function isBanned(int $gameId, string $playerName, string $ip) {
    global $dbTableBans;

    $sql = "SELECT ban_id
            FROM $dbTableBans
            WHERE game_id=? AND (player_name=? OR ip=?)
            LIMIT 1";

    return exec_query($sql, [ "iss", $gameId, $playerName, $ip ]);
  }

  public static function getByPlayerAndGame(int $playerId, int $gameId) {
    global $dbTableBans;
    $sql = "SELECT ban_id FROM $dbTableBans WHERE player_id=? AND game_id=? LIMIT 1";
    return exec_query($sql, ["ii", $playerId, $gameId]);
  }

  public static function removeByPlayerAndGame(int $playerId, int $gameId) {
    global $dbTableBans;
    $sql = "DELETE FROM $dbTableBans WHERE player_id=? AND game_id=?";
    exec_query($sql, ["ii", $playerId, $gameId]);
  }
}
