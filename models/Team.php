<?php

class Team {
  public static array $schema = [
    'table'      => 'teams',
    'primaryKey' => 'team_id',
    'timestamps' => true,
    'columns'    => [
      'team_id'    => ['type' => 'int',    'auto' => true],
      'name'       => ['type' => 'string'],
      'created_by' => ['type' => 'int'],
      'created_at' => ['type' => 'datetime'],
      'updated_at' => ['type' => 'datetime'],
    ],
    'foreignKeys' => [
      ['columns' => ['created_by'], 'references' => ['users', 'id']],
    ],
    'relations'  => [
      'members' => ['type' => 'hasMany', 'model' => 'TeamMember', 'foreignKey' => 'team_id'],
      'games'   => ['type' => 'hasMany', 'model' => 'Game',       'foreignKey' => 'team_id'],
    ],
  ];

  public static array $teamMembersSchema = [
    'table'      => 'team_members',
    'primaryKey' => 'id',
    'timestamps' => false,
    'columns'    => [
      'id'         => ['type' => 'int',    'auto' => true],
      'team_id'    => ['type' => 'int'],
      'user_id'    => ['type' => 'int'],
      'role'       => ['type' => 'enum',   'values' => ['admin', 'member'], 'default' => 'member'],
      'added_by'   => ['type' => 'int'],
      'created_at' => ['type' => 'datetime'],
    ],
    'indexes'    => [
      ['columns' => ['team_id', 'user_id'], 'unique' => true],
      ['columns' => ['user_id']],
      ['columns' => ['added_by']],
    ],
    'foreignKeys' => [
      ['columns' => ['team_id'],  'references' => ['teams', 'team_id']],
      ['columns' => ['user_id'],  'references' => ['users', 'id']],
      ['columns' => ['added_by'], 'references' => ['users', 'id']],
    ],
    'relations'  => [
      'team' => ['type' => 'belongsTo', 'model' => 'Team', 'foreignKey' => 'team_id'],
      'user' => ['type' => 'belongsTo', 'model' => 'User', 'foreignKey' => 'user_id'],
    ],
  ];
  public static function create(int $userId, string $name) {
    global $dbTableTeams;
    global $dbTableTeamMembers;
    global $db;

    $sql = "INSERT INTO $dbTableTeams (name, created_by) VALUES(?, ?)";
    exec_query($sql, ["si", $name, $userId]);
    $teamId = $db->insert_id;

    $sql = "INSERT INTO $dbTableTeamMembers (team_id, user_id, role, added_by) VALUES(?, ?, 'admin', ?)";
    exec_query($sql, ["iii", $teamId, $userId, $userId]);

    return $teamId;
  }

  public static function listByUser(int $userId) {
    global $dbTableTeams;
    global $dbTableTeamMembers;

    $sql = "SELECT t.team_id, t.name, tm.role, t.created_at,
                   (SELECT COUNT(*) FROM $dbTableTeamMembers WHERE team_id = t.team_id) AS member_count
            FROM $dbTableTeams t
            INNER JOIN $dbTableTeamMembers tm ON t.team_id = tm.team_id
            WHERE tm.user_id = ?
            ORDER BY t.name ASC";

    return exec_query($sql, ["i", $userId]);
  }

  public static function getById(int $teamId) {
    global $dbTableTeams;
    $sql = "SELECT team_id, name, created_by, created_at, updated_at FROM $dbTableTeams WHERE team_id = ?";
    $result = exec_query($sql, ["i", $teamId]);
    return $result->num_rows ? $result->fetch_assoc() : null;
  }

  public static function updateName(int $teamId, int $userId, string $name) {
    global $dbTableTeams;
    $sql = "UPDATE $dbTableTeams SET name = ?, updated_at = CURRENT_TIMESTAMP WHERE team_id = ? AND created_by = ?";
    exec_query($sql, ["sii", $name, $teamId, $userId]);
  }

  public static function delete(int $teamId, int $userId) {
    global $dbTableTeams;
    $sql = "DELETE FROM $dbTableTeams WHERE team_id = ? AND created_by = ?";
    exec_query($sql, ["ii", $teamId, $userId]);
  }

  public static function addMember(int $teamId, int $addedByUserId, string $discordUserId, string $role = 'member') {
    global $dbTableTeamMembers;
    global $dbTableUsers;

    $sql = "SELECT id FROM $dbTableUsers WHERE discord_user_id = ?";
    $result = exec_query($sql, ["s", $discordUserId]);
    if (!$result->num_rows) return false;
    $targetUser = $result->fetch_assoc();

    $sql = "INSERT INTO $dbTableTeamMembers (team_id, user_id, role, added_by)
            SELECT ?, ?, ?, ?
            WHERE NOT EXISTS (
              SELECT 1 FROM $dbTableTeamMembers WHERE team_id = ? AND user_id = ?
            )";
    exec_query($sql, ["iisiii", $teamId, $targetUser["id"], $role, $addedByUserId, $teamId, $targetUser["id"]]);
    return true;
  }

  public static function removeMember(int $teamId, int $removedByUserId, int $targetUserId) {
    global $dbTableTeamMembers;
    $sql = "DELETE FROM $dbTableTeamMembers WHERE team_id = ? AND user_id = ? AND user_id != ?";
    exec_query($sql, ["iii", $teamId, $targetUserId, $removedByUserId]);
  }

  public static function getMembers(int $teamId) {
    global $dbTableTeamMembers;
    global $dbTableUsers;

    $sql = "SELECT tm.id, tm.user_id, tm.role, tm.added_by, tm.created_at,
                   u.username, u.discord_user_id
            FROM $dbTableTeamMembers tm
            INNER JOIN $dbTableUsers u ON tm.user_id = u.id
            WHERE tm.team_id = ?
            ORDER BY tm.role ASC, u.username ASC";

    return exec_query($sql, ["i", $teamId]);
  }

  public static function isMember(int $teamId, int $userId) {
    global $dbTableTeamMembers;
    $sql = "SELECT 1 FROM $dbTableTeamMembers WHERE team_id = ? AND user_id = ? LIMIT 1";
    $result = exec_query($sql, ["ii", $teamId, $userId]);
    return $result->num_rows > 0;
  }

  public static function isAdmin(int $teamId, int $userId) {
    global $dbTableTeamMembers;
    $sql = "SELECT 1 FROM $dbTableTeamMembers WHERE team_id = ? AND user_id = ? AND role = 'admin' LIMIT 1";
    $result = exec_query($sql, ["ii", $teamId, $userId]);
    return $result->num_rows > 0;
  }

  public static function countMembers(int $teamId) {
    global $dbTableTeamMembers;
    $sql = "SELECT COUNT(*) AS count FROM $dbTableTeamMembers WHERE team_id = ?";
    $result = exec_query($sql, ["i", $teamId]);
    return $result->num_rows ? (int)$result->fetch_assoc()["count"] : 0;
  }

  public static function getUserById(int $userId) {
    global $dbTableUsers;
    $sql = "SELECT id, discord_user_id, username FROM $dbTableUsers WHERE id = ?";
    return exec_query($sql, ["i", $userId]);
  }
}
