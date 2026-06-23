<?php

class User {
  public static array $schema = [
    'table'      => 'users',
    'primaryKey' => 'id',
    'timestamps' => true,
    'columns'    => [
      'id'              => ['type' => 'int',    'auto' => true],
      'auth_discord_id' => ['type' => 'string', 'unique' => true],
      'username'        => ['type' => 'string'],
      'approved'        => ['type' => 'bool',   'default' => false],
      'admin'           => ['type' => 'bool',   'default' => false],
      'auth_discord_id' => ['type' => 'string', 'nullable' => true],
      'created_at'      => ['type' => 'datetime'],
      'updated_at'      => ['type' => 'datetime'],
    ],
    'indexes'    => [
      ['columns' => ['auth_discord_id'], 'unique' => true],
      ['columns' => ['created_at']],
    ],
    'relations'  => [
      'games' => ['type' => 'hasMany', 'model' => 'Game', 'foreignKey' => 'user_id'],
    ],
  ];
  /**
   * Upsert the user
   */
  public static function upsert(string $discordUserId, $username, $avatar) {
    global $dbTableUsers;

    $sql = "INSERT INTO $dbTableUsers (auth_discord_id, username) VALUES (?, ?)
    ON DUPLICATE KEY UPDATE username = ?";

    exec_query($sql, [ "sss", $discordUserId, $username, $username ]);
  }

  /**
   * Get the user by the id
   */
  public static function getById(string $userId) {
    global $dbTableUsers;
    $sql = "SELECT id, auth_discord_id, username, approved, admin FROM $dbTableUsers WHERE id = ?";
    return exec_query($sql, [ "i", $userId ]);
  }

  /**
   * List all users with optional search and pending filter
   */
  public static function listAll(?string $search = null, bool $pendingOnly = false, int $page = 0, int $perPage = 50) {
    global $dbTableUsers;
    $offset = $page * $perPage;

    $sql = "SELECT id, auth_discord_id, username, approved, admin FROM $dbTableUsers";

    $conditions = [];
    $params = [];
    $types = "";

    if (!is_null($search) && $search !== '') {
      $conditions[] = "username LIKE ?";
      $types .= "s";
      $params[] = "%" . $search . "%";
    }

    if ($pendingOnly) {
      $conditions[] = "approved = 0";
    }

    if (!empty($conditions)) {
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    $sql .= " ORDER BY id ASC LIMIT ?,?";
    $types .= "ii";
    $params[] = $offset;
    $params[] = $perPage;

    if ($types) {
      array_unshift($params, $types);
    }

    return exec_query($sql, $params);
  }

  /**
   * Count all users with optional search and pending filter
   */
  public static function countAll(?string $search = null, bool $pendingOnly = false) {
    global $dbTableUsers;
    $sql = "SELECT COUNT(id) AS count FROM $dbTableUsers";

    $conditions = [];
    $params = [];
    $types = "";

    if (!is_null($search) && $search !== '') {
      $conditions[] = "username LIKE ?";
      $types .= "s";
      $params[] = "%" . $search . "%";
    }

    if ($pendingOnly) {
      $conditions[] = "approved = 0";
    }

    if (!empty($conditions)) {
      $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    if ($types) {
      array_unshift($params, $types);
      $result = exec_query($sql, $params);
    } else {
      $result = exec_query($sql);
    }

    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  }

  /**
   * Toggle the approved status of a user
   */
  public static function toggleApproved(int $userId) {
    global $dbTableUsers;
    $sql = "UPDATE $dbTableUsers SET approved = NOT approved WHERE id = ?";
    exec_query($sql, ["i", $userId]);
  }

  /**
   * Count unapproved users
   */
  public static function countUnapproved() {
    global $dbTableUsers;
    $sql = "SELECT COUNT(id) AS count FROM $dbTableUsers WHERE approved = 0";
    $result = exec_query($sql);
    return $result->num_rows ? $result->fetch_assoc()["count"] : 0;
  }

  /**
   * Get the user by the discord user id
   */
  public static function getByDiscordUserId(string $discordUserId) {
    global $dbTableUsers;
    $sql = "SELECT id, auth_discord_id, username FROM $dbTableUsers WHERE auth_discord_id = ?";
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
