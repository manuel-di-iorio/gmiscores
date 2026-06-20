<?php

class Leaderboard {
  public static array $schema = [
    'table'      => 'leaderboards',
    'primaryKey' => 'leaderboard_id',
    'timestamps' => true,
    'columns'    => [
      'leaderboard_id' => ['type' => 'int',    'auto' => true],
      'game_id'        => ['type' => 'int'],
      'name'           => ['type' => 'string'],
      'description'    => ['type' => 'text',   'nullable' => true],
      'user_id'        => ['type' => 'int'],
      'is_private'     => ['type' => 'bool',   'default' => false],
      'created_at'     => ['type' => 'datetime'],
      'updated_at'     => ['type' => 'datetime'],
    ],
    'indexes'    => [
      ['columns' => ['game_id']],
    ],
    'foreignKeys' => [
      ['columns' => ['game_id'], 'references' => ['games', 'game_id']],
    ],
    'relations'  => [
      'game'   => ['type' => 'belongsTo', 'model' => 'Game',  'foreignKey' => 'game_id'],
      'scores' => ['type' => 'hasMany',   'model' => 'Score', 'foreignKey' => 'leaderboard_id'],
    ],
  ];

    public static function create(int $gameId, string $name, ?string $description, int $userId, bool $isPrivate = false) {
        global $db, $dbTableLeaderboards;

        $name = escapeChars($name);
        $description = $description ? escapeChars($description) : null;

        $sql = "INSERT INTO $dbTableLeaderboards (game_id, name, description, user_id, is_private) VALUES (?, ?, ?, ?, ?)";
        return exec_query($sql, ["issii", $gameId, $name, $description, $userId, $isPrivate ? 1 : 0]);
    }

    public static function listByGame(int $gameId, array $filters = []) {
        global $db, $dbTableLeaderboards, $dbTableScores;
        $sql = "SELECT l.leaderboard_id, l.name, l.description, l.created_at, l.updated_at, l.is_private,
                       COUNT(s.score_id) AS score_count
                FROM $dbTableLeaderboards l
                LEFT JOIN $dbTableScores s ON s.leaderboard_id = l.leaderboard_id
                WHERE l.game_id = ?";
        $params = ["i", $gameId];

        if (!empty($filters['name'])) {
            $sql .= " AND l.name LIKE ?";
            $params[0] .= "s";
            $params[] = "%" . $filters['name'] . "%";
        }

        $sql .= " GROUP BY l.leaderboard_id";

        if (isset($filters['score_min']) && $filters['score_min'] !== '') {
            $sql .= " HAVING score_count >= ?";
            $params[0] .= "i";
            $params[] = (int)$filters['score_min'];
        }
        if (isset($filters['score_max']) && $filters['score_max'] !== '') {
            $sql .= " AND score_count <= ?";
            $params[0] .= "i";
            $params[] = (int)$filters['score_max'];
        }

        $sql .= " ORDER BY l.name ASC";
        $result = exec_query($sql, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public static function getById(int $leaderboardId) {
        global $db, $dbTableLeaderboards;
        $sql = "SELECT leaderboard_id, game_id, name, description, created_at, updated_at, is_private
                FROM $dbTableLeaderboards 
                WHERE leaderboard_id = ?";
        $result = exec_query($sql, ["i", $leaderboardId]);
        return $result->fetch_assoc();
    }

    public static function update(int $leaderboardId, string $name, ?string $description, int $gameId, bool $isPrivate = false) {
        global $db, $dbTableLeaderboards;

        $name = escapeChars($name);
        $description = $description ? escapeChars($description) : null;

        $sql = "UPDATE $dbTableLeaderboards SET name = ?, description = ?, is_private = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE leaderboard_id = ? AND game_id = ?";
        return exec_query($sql, ["ssiii", $name, $description, $isPrivate ? 1 : 0, $leaderboardId, $gameId]);
    }

    public static function delete(int $leaderboardId, int $gameId) {
        global $db, $dbTableLeaderboards;
        $sql = "DELETE FROM $dbTableLeaderboards WHERE leaderboard_id = ? AND game_id = ?";
        return exec_query($sql, ["ii", $leaderboardId, $gameId]);
    }
}
?>