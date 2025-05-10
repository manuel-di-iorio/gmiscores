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

    // global $dbTableScores;
    // $sql = "SELECT COUNT(DISTINCT ip) AS count FROM $dbTableScores";
    // $result = exec_query($sql);
    // return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  } 
}
