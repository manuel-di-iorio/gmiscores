<?php

class Game {
  /**
   * Get the client secret by the game ID
   */
  public static function getClientSecretById(string $gameId) {
    global $dbTableGames;

    $sql = "SELECT client_secret FROM $dbTableGames WHERE game_id=?";
    return exec_query($sql, [ "i", $gameId ]);
  }

  /**
   * Get the games list by user
   */
  public static function listByUser(int $userId) {
    global $dbTableGames;
    global $dbTableScores;

    $sql = "SELECT G.game_id, G.name, COUNT(S.score_id) AS _scoresCount, COUNT(DISTINCT S.player_id) as _playersCount
            FROM $dbTableGames AS G
            LEFT JOIN $dbTableScores AS S ON G.game_id = S.game_id
            WHERE user_id=?
            GROUP BY G.game_id
            LIMIT 200";

    return exec_query($sql, [ "i", $userId ]);
  }

  /**
   * Create a game
   */
  public static function create(int $userId, string $gameName, string $clientSecret) {
    global $dbTableGames;
    $sql = "INSERT INTO $dbTableGames (name, user_id, client_secret) VALUES(?, ?, ?)";
    exec_query($sql, [ "sis", $gameName, $userId, $clientSecret ]);
  }

  /**
   * Delete a game
   */
  public static function delete(int $gameId, int $userId) {
    global $dbTableGames;    
    $sql = "DELETE FROM $dbTableGames WHERE game_id = ? AND user_id = ?";
    exec_query($sql, [ "is", $gameId, $userId ]);
  }

  /**
   * Rename a game
   */
  public static function rename(int $gameId, int $userId, string $name) {
    global $dbTableGames;    
    $sql = "UPDATE $dbTableGames SET name=? WHERE game_id=? AND user_id=?";
    exec_query($sql, [ "sii", $name, $gameId, $userId ]);
  }

  /**
   * Regenerate the game client_secret
   */
  public static function regenerateSecret(int $gameId, int $userId, string $client_secret) {
    global $dbTableGames;    
    $sql = "UPDATE $dbTableGames SET client_secret=? WHERE game_id=? AND user_id=?";
    exec_query($sql, [ "sii", $client_secret, $gameId, $userId ]);
  }

  /**
   * Get by ID
   */
  public static function getById(int $gameId) {
    global $dbTableGames;    
    $sql = "SELECT game_id FROM $dbTableGames WHERE game_id=?";
    return exec_query($sql, [ "i", $gameId ]);    
  }

  /**
   * Get by ID and user
   */
  public static function getByIdAndUser(int $gameId, int $userId) {
    global $dbTableGames;    
    $sql = "SELECT game_id, name, client_secret FROM $dbTableGames WHERE game_id=? AND user_id=?";
    return exec_query($sql, [ "ii", $gameId, $userId ]);    
  }

  /** Get the count of all games */
  public static function count() {
    global $dbTableGames;
    $sql = "SELECT COUNT(game_id) AS count FROM $dbTableGames";
    $result = exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  } 
}
