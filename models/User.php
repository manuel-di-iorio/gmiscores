<?php

class User {
  /**
   * Upsert the user
   */
  public static function upsert(string $discordUserId, $username, $avatar) {
    global $dbTableUsers;

    $sql = "INSERT INTO $dbTableUsers (discord_user_id, username, avatar) VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE username = ?, avatar = ?";

    exec_query($sql, [ "sssss", $discordUserId, $username, $avatar, $username, $avatar ]);
  }

  /**
   * Get the user by the id
   */
  public static function getById(string $userId) {
    global $dbTableUsers;
    $sql = "SELECT id, discord_user_id, username, avatar, approved FROM $dbTableUsers WHERE id = ?";
    return exec_query($sql, [ "i", $userId ]);
  }

  /**
   * Get the user by the discord user id
   */
  public static function getByDiscordUserId(string $discordUserId) {
    global $dbTableUsers;
    $sql = "SELECT id, discord_user_id, username, avatar FROM $dbTableUsers WHERE discord_user_id = ?";
    return exec_query($sql, [ "s", $discordUserId ]);
  }

  /** Get the count of all users */
  public static function count() {
    global $dbTableUsers;
    $sql = "SELECT COUNT(id) AS count FROM $dbTableUsers";
    $result = exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  }
}
