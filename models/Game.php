<?php

class Game {
  public static function getClientSecretById(string $gameId) {
    global $dbTableGames;
    $sql = "SELECT client_secret FROM $dbTableGames WHERE game_id=?";
    return exec_query($sql, [ "i", $gameId ]);
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

  public static function create(int $userId, string $gameName, string $clientSecret, ?int $teamId = NULL) {
    global $dbTableGames;
    if (is_null($teamId)) {
      $sql = "INSERT INTO $dbTableGames (name, user_id, client_secret) VALUES(?, ?, ?)";
      exec_query($sql, [ "sis", $gameName, $userId, $clientSecret ]);
    } else {
      $sql = "INSERT INTO $dbTableGames (name, user_id, team_id, client_secret) VALUES(?, ?, ?, ?)";
      exec_query($sql, [ "siis", $gameName, $userId, $teamId, $clientSecret ]);
    }
  }

  public static function delete(int $gameId, int $userId) {
    global $dbTableGames;
    $sql = "DELETE FROM $dbTableGames WHERE game_id = ? AND user_id = ?";
    exec_query($sql, [ "ii", $gameId, $userId ]);
  }

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
    $sql = "SELECT G.game_id, G.name, G.client_secret, G.team_id
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
    $sql = "SELECT COUNT(game_id) AS count FROM $dbTableGames WHERE user_id = ?";
    $result = exec_query($sql, ["i", $userId]);
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
    $sql = "DELETE FROM $dbTableGames AS G
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
