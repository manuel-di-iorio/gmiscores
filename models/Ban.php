<?php

class Ban {
  /**
   * Add a player ban for a game
   */
  public static function add(int $playerId, string $playerName, string $ip, int $gameId) {
    global $dbTableBans;
    print_r([$playerId, $playerName, $ip, $gameId]);
    $sql = "INSERT INTO $dbTableBans (player_id, player_name, ip, game_id) VALUES (?, ?, ?, ?)";
    exec_query($sql, [ "issi", $playerId, $playerName, $ip, $gameId ]);
  }

  /**
   * Get by ID and user
   */
  public static function getByIdAndUser(int $banId, int $userId) {
    global $dbTableBans;
    global $dbTableGames;

    $sql = "SELECT B.game_id
            FROM $dbTableBans AS B
            LEFT JOIN $dbTableGames AS G ON B.game_id=G.game_id AND G.user_id=?
            WHERE B.ban_id=?";

    return exec_query($sql, [ "ii", $userId, $banId ]);
  }

  /**
   * Delete a player ban for a game
   */
  public static function remove(int $banId) {
    global $dbTableBans;
    $sql = "DELETE FROM $dbTableBans WHERE ban_id=?";
    exec_query($sql, [ "i", $banId ]);
  }

  /**
   * List the players ban for a game
   */
  public static function list(int $gameId) {
    global $dbTableBans;
    global $dbTablePlayers;

    $sql = "SELECT ban_id, player_id, player_name, created_at
            FROM $dbTableBans
            WHERE game_id=?
            ORDER BY created_at DESC
            LIMIT 200";

    return exec_query($sql, [ "i", $gameId ]);
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
}
