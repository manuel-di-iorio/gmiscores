<?php

class Leaderboard {
    /**
     * Create a new leaderboard for a game
     */
    public static function create(int $gameId, string $name, ?string $description) {
        global $db, $dbTableLeaderboards;

        $name = escapeChars($name);
        $description = $description ? escapeChars($description) : null;

        $sql = "INSERT INTO $dbTableLeaderboards (game_id, name, description) VALUES (?, ?, ?)";
        return exec_query($sql, ["iss", $gameId, $name, $description]);
    }

    /**
     * List leaderboards for a specific game
     */
    public static function listByGame(int $gameId) {
        global $db, $dbTableLeaderboards;
        $sql = "SELECT leaderboard_id, name, description, created_at, updated_at 
                FROM $dbTableLeaderboards 
                WHERE game_id = ? 
                ORDER BY name ASC";
        $result = exec_query($sql, ["i", $gameId]);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get a specific leaderboard by its ID
     */
    public static function getById(int $leaderboardId) {
        global $db, $dbTableLeaderboards;
        $sql = "SELECT leaderboard_id, game_id, name, description, created_at, updated_at 
                FROM $dbTableLeaderboards 
                WHERE leaderboard_id = ?";
        $result = exec_query($sql, ["i", $leaderboardId]);
        return $result->fetch_assoc();
    }

    /**
     * Update an existing leaderboard
     */
    public static function update(int $leaderboardId, string $name, ?string $description) {
        global $db, $dbTableLeaderboards;

        $name = escapeChars($name);
        $description = $description ? escapeChars($description) : null;

        $sql = "UPDATE $dbTableLeaderboards SET name = ?, description = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE leaderboard_id = ?";
        return exec_query($sql, ["ssi", $name, $description, $leaderboardId]);
    }

    /**
     * Delete a leaderboard
     * Note: This does not delete associated scores. Handle that separately if needed.
     */
    public static function delete(int $leaderboardId) {
        global $db, $dbTableLeaderboards;
        $sql = "DELETE FROM $dbTableLeaderboards WHERE leaderboard_id = ?";
        return exec_query($sql, ["i", $leaderboardId]);
    }
}
?>
