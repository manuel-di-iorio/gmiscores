<?php

class QueryAnalyzer {

  private static function getQueries(): array {
    global $dbTableScores, $dbTableGames, $dbTablePlayers, $dbTableBans,
           $dbTableLeaderboards, $dbTableUsers, $dbTableTeams, $dbTableTeamMembers;

    return [
      // === SCORES (37 queries) ===
      'Score::findByGameLeaderboardAndPlayerId' => [
        'sql' => "SELECT score_id, score, sign FROM $dbTableScores WHERE game_id=? AND leaderboard_id=? AND player_id=? LIMIT ?",
        'params' => ['iiii', 1, 1, 1, 1],
        'tables' => ['scores'],
      ],
      'Score::listSortedByGameId' => [
        'sql' => "SELECT P.player_id, P.username, S.score_id, S.tags, S.score, S.created_at, S.updated_at, S.sign, S.data, S.env
                  FROM $dbTableScores AS S
                  INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
                  WHERE S.game_id=? AND S.leaderboard_id=?
                  ORDER BY S.score DESC LIMIT 0,20",
        'params' => ['ii', 1, 1],
        'tables' => ['scores', 'players'],
      ],
      'Score::listByGame' => [
        'sql' => "SELECT P.player_id, P.username, S.score_id, S.score, S.data, S.updated_at, S.ip_country, S.tags, S.env
                  FROM $dbTableScores AS S
                  INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
                  WHERE S.game_id=?
                  ORDER BY S.updated_at DESC LIMIT 0,100",
        'params' => ['i', 1],
        'tables' => ['scores', 'players'],
      ],
      'Score::countByGame' => [
        'sql' => "SELECT COUNT(S.score_id) as count FROM $dbTableScores AS S
                  INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
                  WHERE S.game_id=?",
        'params' => ['i', 1],
        'tables' => ['scores', 'players'],
      ],
      'Score::delete' => [
        'sql' => "DELETE S FROM $dbTableScores AS S
                  INNER JOIN $dbTableGames AS G ON S.game_id = G.game_id
                  LEFT JOIN $dbTableTeamMembers TM ON G.team_id = TM.team_id AND TM.user_id = ?
                  WHERE S.score_id = ? AND (G.user_id = ? OR TM.id IS NOT NULL)",
        'params' => ['iii', 1, 1, 1],
        'tables' => ['scores', 'games', 'team_members'],
      ],
      'Score::clear' => [
        'sql' => "DELETE S FROM $dbTableScores AS S
                  INNER JOIN $dbTableGames AS G ON S.game_id = G.game_id
                  LEFT JOIN $dbTableTeamMembers TM ON G.team_id = TM.team_id AND TM.user_id = ?
                  WHERE S.game_id = ? AND (G.user_id = ? OR TM.id IS NOT NULL)",
        'params' => ['iii', 1, 1, 1],
        'tables' => ['scores', 'games', 'team_members'],
      ],
      'Score::getAll' => [
        'sql' => "SELECT P.player_id, P.username, S.score, S.ip, S.ip_country, S.created_at, S.sign, S.tags, S.leaderboard_id, S.data, S.env
                  FROM $dbTableScores AS S
                  INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
                  INNER JOIN $dbTableGames AS G ON S.game_id = G.game_id
                  LEFT JOIN $dbTableTeamMembers AS TM ON G.team_id = TM.team_id AND TM.user_id = ?
                  WHERE S.game_id=? AND (G.user_id = ? OR TM.id IS NOT NULL)",
        'params' => ['iii', 1, 1, 1],
        'tables' => ['scores', 'players', 'games', 'team_members'],
      ],
      'Score::getById' => [
        'sql' => "SELECT S.ip, P.player_id, P.username, S.leaderboard_id
                  FROM $dbTableScores AS S
                  INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
                  WHERE S.score_id=?",
        'params' => ['i', 1],
        'tables' => ['scores', 'players'],
      ],
      'Score::listAllRecent' => [
        'sql' => "SELECT S.score_id, S.game_id, S.player_id, S.score, S.updated_at, S.ip,
                         P.username, G.name AS game_name
                  FROM $dbTableScores AS S
                  INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
                  INNER JOIN $dbTableGames AS G ON S.game_id = G.game_id
                  ORDER BY S.updated_at DESC, S.score_id DESC LIMIT 0,50",
        'params' => ['ii', 0, 50],
        'tables' => ['scores', 'players', 'games'],
      ],
      'Score::countAllFiltered' => [
        'sql' => "SELECT COUNT(S.score_id) AS count
                  FROM $dbTableScores AS S
                  INNER JOIN $dbTablePlayers AS P ON S.player_id = P.player_id
                  INNER JOIN $dbTableGames AS G ON S.game_id = G.game_id",
        'params' => null,
        'tables' => ['scores', 'players', 'games'],
      ],
      'Score::deleteByPlayerAndGame' => [
        'sql' => "DELETE FROM $dbTableScores WHERE player_id=? AND game_id=?",
        'params' => ['ii', 1, 1],
        'tables' => ['scores'],
      ],
      'Score::getRankByScoreId' => [
        'sql' => "SELECT 1 + (
                    SELECT COUNT(*) FROM $dbTableScores AS T
                    WHERE T.game_id = S.game_id AND T.leaderboard_id = S.leaderboard_id AND T.score > S.score
                  ) AS `rank`
                  FROM $dbTableScores AS S
                  WHERE S.score_id = ? AND S.game_id = ?
                  LIMIT 1",
        'params' => ['ii', 1, 1],
        'tables' => ['scores'],
      ],
      'Score::getActiveGames' => [
        'sql' => "SELECT game_id FROM $dbTableScores WHERE updated_at >= '2025-01-01' GROUP BY game_id",
        'params' => null,
        'tables' => ['scores'],
      ],
      'Score::getGameWithMoreScores' => [
        'sql' => "SELECT count(S.score_id) count, G.name FROM $dbTableScores S INNER JOIN $dbTableGames G ON G.game_id = S.game_id GROUP BY G.game_id ORDER BY count DESC LIMIT 1",
        'params' => null,
        'tables' => ['scores', 'games'],
      ],
      'Score::getPlayerWithMoreScores' => [
        'sql' => "SELECT count(S.player_id) count, P.username FROM $dbTableScores S INNER JOIN $dbTablePlayers P ON P.player_id = S.player_id GROUP BY P.player_id LIMIT 1",
        'params' => null,
        'tables' => ['scores', 'players'],
      ],
      'Score::getUniqueCountriesCount' => [
        'sql' => "SELECT DISTINCT ip_country FROM $dbTableScores",
        'params' => null,
        'tables' => ['scores'],
      ],
      'Score::countByUser' => [
        'sql' => "SELECT COUNT(S.score_id) AS count FROM $dbTableScores S
                  INNER JOIN $dbTableGames G ON S.game_id = G.game_id
                  WHERE G.user_id = ? AND G.team_id IS NULL AND S.env = 'production'",
        'params' => ['i', 1],
        'tables' => ['scores', 'games'],
      ],
      'Score::countByUserToday' => [
        'sql' => "SELECT COUNT(S.score_id) AS count FROM $dbTableScores S
                  INNER JOIN $dbTableGames G ON S.game_id = G.game_id
                  WHERE G.user_id = ? AND G.team_id IS NULL AND S.env = 'production' AND DATE(COALESCE(S.updated_at, S.created_at)) = CURDATE()",
        'params' => ['i', 1],
        'tables' => ['scores', 'games'],
      ],
      'Score::getUniquePlayersByUser' => [
        'sql' => "SELECT COUNT(DISTINCT S.player_id) AS count FROM $dbTableScores S
                  INNER JOIN $dbTableGames G ON S.game_id = G.game_id
                  WHERE G.user_id = ? AND G.team_id IS NULL AND S.env = 'production'",
        'params' => ['i', 1],
        'tables' => ['scores', 'games'],
      ],
      'Score::getCountriesByUser' => [
        'sql' => "SELECT S.ip_country, COUNT(*) AS count FROM $dbTableScores S
                  INNER JOIN $dbTableGames G ON S.game_id = G.game_id
                  WHERE G.user_id = ? AND G.team_id IS NULL AND S.env = 'production' AND S.ip_country IS NOT NULL AND S.ip_country != ''
                  GROUP BY S.ip_country ORDER BY count DESC",
        'params' => ['i', 1],
        'tables' => ['scores', 'games'],
      ],
      'Score::getScoresPerDayByUser' => [
        'sql' => "SELECT DATE(COALESCE(S.updated_at, S.created_at)) AS day, COUNT(*) AS count FROM $dbTableScores S
                  INNER JOIN $dbTableGames G ON S.game_id = G.game_id
                  WHERE G.user_id = ? AND G.team_id IS NULL AND S.env = 'production' AND COALESCE(S.updated_at, S.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                  GROUP BY DATE(COALESCE(S.updated_at, S.created_at)) ORDER BY day ASC",
        'params' => ['i', 1],
        'tables' => ['scores', 'games'],
      ],
      'Score::getScoresByGameByUser' => [
        'sql' => "SELECT G.name, G.game_id, COUNT(S.score_id) AS count FROM $dbTableScores S
                  INNER JOIN $dbTableGames G ON S.game_id = G.game_id
                  WHERE G.user_id = ? AND G.team_id IS NULL AND S.env = 'production'
                  GROUP BY G.game_id ORDER BY count DESC",
        'params' => ['i', 1],
        'tables' => ['scores', 'games'],
      ],
      'Score::countByTeam' => [
        'sql' => "SELECT COUNT(S.score_id) AS count FROM $dbTableScores S
                  INNER JOIN $dbTableGames G ON S.game_id = G.game_id
                  WHERE G.team_id = ? AND S.env = 'production'",
        'params' => ['i', 1],
        'tables' => ['scores', 'games'],
      ],
      'Score::getUniquePlayersByGame' => [
        'sql' => "SELECT COUNT(DISTINCT player_id) AS count FROM $dbTableScores WHERE game_id = ? AND env = 'production'",
        'params' => ['i', 1],
        'tables' => ['scores'],
      ],
      'Score::getScoresOverTimeByGame' => [
        'sql' => "SELECT DATE(COALESCE(S.updated_at, S.created_at)) AS day, COUNT(*) AS count FROM $dbTableScores S
                  WHERE S.game_id = ? AND S.env = 'production' AND COALESCE(S.updated_at, S.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                  GROUP BY DATE(COALESCE(S.updated_at, S.created_at)) ORDER BY day ASC",
        'params' => ['i', 1],
        'tables' => ['scores'],
      ],
      'Score::getScoresByLeaderboardByGame' => [
        'sql' => "SELECT L.name, L.leaderboard_id, COUNT(S.score_id) AS count FROM $dbTableScores S
                  INNER JOIN $dbTableLeaderboards L ON S.leaderboard_id = L.leaderboard_id
                  WHERE S.game_id = ? AND S.env = 'production'
                  GROUP BY S.leaderboard_id ORDER BY count DESC",
        'params' => ['i', 1],
        'tables' => ['scores', 'leaderboards'],
      ],
      'Score::getCountriesByGame' => [
        'sql' => "SELECT S.ip_country, COUNT(*) AS count FROM $dbTableScores S
                  WHERE S.game_id = ? AND S.env = 'production' AND S.ip_country IS NOT NULL AND S.ip_country != ''
                  GROUP BY S.ip_country ORDER BY count DESC",
        'params' => ['i', 1],
        'tables' => ['scores'],
      ],

      // === GAMES (23 queries, key ones) ===
      'Game::listByUser' => [
        'sql' => "SELECT G.game_id, G.name, COUNT(S.score_id) AS _scoresCount, COUNT(DISTINCT S.player_id) as _playersCount
                  FROM $dbTableGames AS G
                  LEFT JOIN $dbTableScores AS S ON G.game_id = S.game_id
                  WHERE G.user_id=? AND G.team_id IS NULL
                  GROUP BY G.game_id LIMIT 200",
        'params' => ['i', 1],
        'tables' => ['games', 'scores'],
      ],
      'Game::listByTeam' => [
        'sql' => "SELECT G.game_id, G.name, COUNT(S.score_id) AS _scoresCount, COUNT(DISTINCT S.player_id) as _playersCount
                  FROM $dbTableGames AS G
                  LEFT JOIN $dbTableScores AS S ON G.game_id = S.game_id
                  WHERE G.team_id=?
                  GROUP BY G.game_id LIMIT 200",
        'params' => ['i', 1],
        'tables' => ['games', 'scores'],
      ],
      'Game::getByIdWithAccess' => [
        'sql' => "SELECT G.game_id, G.name, G.client_secret, G.team_id
                  FROM $dbTableGames G
                  LEFT JOIN $dbTableTeamMembers TM ON G.team_id = TM.team_id AND TM.user_id = ?
                  WHERE G.game_id = ? AND (G.user_id = ? OR TM.id IS NOT NULL)
                  LIMIT 1",
        'params' => ['iii', 1, 1, 1],
        'tables' => ['games', 'team_members'],
      ],
      'Game::countByUser' => [
        'sql' => "SELECT COUNT(game_id) AS count FROM $dbTableGames WHERE user_id = ? AND team_id IS NULL",
        'params' => ['i', 1],
        'tables' => ['games'],
      ],
      'Game::countByTeamId' => [
        'sql' => "SELECT COUNT(game_id) AS count FROM $dbTableGames WHERE team_id = ?",
        'params' => ['i', 1],
        'tables' => ['games'],
      ],

      // === LEADERBOARDS ===
      'Leaderboard::listByGame' => [
        'sql' => "SELECT l.leaderboard_id, l.name, l.description, l.created_at, l.updated_at, l.is_private,
                         COUNT(s.score_id) AS score_count
                  FROM $dbTableLeaderboards l
                  LEFT JOIN $dbTableScores s ON s.leaderboard_id = l.leaderboard_id
                  WHERE l.game_id = ?
                  GROUP BY l.leaderboard_id ORDER BY l.name ASC",
        'params' => ['i', 1],
        'tables' => ['leaderboards', 'scores'],
      ],

      // === BANS ===
      'Ban::list' => [
        'sql' => "SELECT ban_id, player_id, player_name, created_at
                  FROM $dbTableBans
                  WHERE game_id=?
                  ORDER BY created_at DESC LIMIT 200",
        'params' => ['i', 1],
        'tables' => ['bans'],
      ],
      'Ban::isBanned' => [
        'sql' => "SELECT ban_id FROM $dbTableBans WHERE game_id=? AND (player_name=? OR ip=?) LIMIT 1",
        'params' => ['iss', 1, 'test', '127.0.0.1'],
        'tables' => ['bans'],
      ],
      'Ban::getByPlayerAndGame' => [
        'sql' => "SELECT ban_id FROM $dbTableBans WHERE player_id=? AND game_id=? LIMIT 1",
        'params' => ['ii', 1, 1],
        'tables' => ['bans'],
      ],

      // === PLAYERS ===
      'Player::getByName' => [
        'sql' => "SELECT * FROM $dbTablePlayers WHERE username=?",
        'params' => ['s', 'test'],
        'tables' => ['players'],
      ],
      'Player::listAllWithScores' => [
        'sql' => "SELECT p.player_id, p.username,
                         (SELECT g2.name FROM $dbTableScores s2
                          INNER JOIN $dbTableGames g2 ON s2.game_id = g2.game_id
                          WHERE s2.player_id = p.player_id
                          GROUP BY s2.game_id
                          ORDER BY COUNT(s2.score_id) DESC LIMIT 1) AS top_game,
                         (SELECT MAX(s3.score) FROM $dbTableScores s3
                          WHERE s3.player_id = p.player_id) AS top_score
                  FROM $dbTablePlayers p
                  ORDER BY top_score IS NULL, top_score DESC
                  LIMIT 0,50",
        'params' => ['ii', 0, 50],
        'tables' => ['players', 'scores', 'games'],
      ],

      // === USERS ===
      'User::listAll' => [
        'sql' => "SELECT id, discord_user_id, username, approved, admin FROM $dbTableUsers
                  ORDER BY id ASC LIMIT 0,50",
        'params' => ['ii', 0, 50],
        'tables' => ['users'],
      ],
      'User::countUnapproved' => [
        'sql' => "SELECT COUNT(id) AS count FROM $dbTableUsers WHERE approved = 0",
        'params' => null,
        'tables' => ['users'],
      ],
      'User::getByDiscordUserId' => [
        'sql' => "SELECT id, discord_user_id, username FROM $dbTableUsers WHERE discord_user_id = ?",
        'params' => ['s', '123456789'],
        'tables' => ['users'],
      ],

      // === TEAMS ===
      'Team::listByUser' => [
        'sql' => "SELECT t.team_id, t.name, tm.role, t.created_at,
                         (SELECT COUNT(*) FROM $dbTableTeamMembers WHERE team_id = t.team_id) AS member_count
                  FROM $dbTableTeams t
                  INNER JOIN $dbTableTeamMembers tm ON t.team_id = tm.team_id
                  WHERE tm.user_id = ?
                  ORDER BY t.name ASC",
        'params' => ['i', 1],
        'tables' => ['teams', 'team_members'],
      ],
      'Team::getMembers' => [
        'sql' => "SELECT tm.id, tm.user_id, tm.role, tm.added_by, tm.created_at,
                         u.username, u.discord_user_id
                  FROM $dbTableTeamMembers tm
                  INNER JOIN $dbTableUsers u ON tm.user_id = u.id
                  WHERE tm.team_id = ?
                  ORDER BY tm.role ASC, u.username ASC",
        'params' => ['i', 1],
        'tables' => ['team_members', 'users'],
      ],
    ];
  }

  private static function bindParams(string $sql, $params): string {
    global $db;
    if (is_null($params)) return $sql;
    $types = $params[0];
    $values = array_slice($params, 1);
    $boundSql = $sql;
    for ($i = 0; $i < strlen($types) && $i < count($values); $i++) {
      $val = $values[$i];
      if ($types[$i] === 'i' || $types[$i] === 'd') {
        $replacement = (string)$val;
      } else {
        $replacement = "'" . $db->real_escape_string((string)$val) . "'";
      }
      $boundSql = preg_replace('/\?/', $replacement, $boundSql, 1);
    }
    return $boundSql;
  }

  public static function explainQuery(string $sql, $params = null): array {
    global $db;
    try {
      $trimmed = ltrim($sql);
      $upper = strtoupper($trimmed);

      if (strpos($upper, 'DELETE') === 0 || strpos($upper, 'UPDATE') === 0 || strpos($upper, 'INSERT') === 0) {
        $selectSql = preg_replace('/^DELETE\s+S?\s*FROM\s+/i', 'SELECT 1 FROM ', $trimmed, 1);
        $selectSql = preg_replace('/^UPDATE\s+/i', 'SELECT 1 FROM ', $selectSql, 1);
        $selectSql = preg_replace('/^INSERT\s+.+\s+SELECT\s+/i', 'SELECT 1 FROM ', $selectSql, 1);
        $selectSql = preg_replace('/\bSET\b.*/i', '', $selectSql, 1);
        $selectSql = preg_replace('/\bVALUES\b.*/i', '', $selectSql, 1);
        $selectSql = trim($selectSql);
        $explainSql = "EXPLAIN $selectSql";
      } else {
        $explainSql = "EXPLAIN $sql";
      }

      $boundSql = self::bindParams($explainSql, $params);
      $result = $db->query($boundSql);
      $rows = [];
      while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
      }
      return $rows;
    } catch (Exception $e) {
      return [['error' => $e->getMessage()]];
    }
  }

  public static function analyzeAllQueries(): array {
    $queries = self::getQueries();
    $results = [];
    foreach ($queries as $name => $query) {
      $start = microtime(true);
      $explain = self::explainQuery($query['sql'], $query['params']);
      $elapsed = round((microtime(true) - $start) * 1000, 2);

      $accessTypes = [];
      $usesIndex = true;
      $warnings = [];
      foreach ($explain as $row) {
        if (isset($row['error'])) {
          $warnings[] = $row['error'];
          $usesIndex = false;
          continue;
        }
        $extra = $row['Extra'] ?? '';
        if (strpos($extra, 'Impossible WHERE') !== false) {
          continue;
        }
        $type = $row['type'] ?? '';
        if ($type !== '') {
          $accessTypes[] = $type;
        }
        if ($type === 'ALL') {
          $usesIndex = false;
          $warnings[] = "Full table scan on `{$row['table']}`";
        }
        if ($type === 'index' && ($row['key'] ?? '') === 'NULL') {
          $usesIndex = false;
          $warnings[] = "Index scan (no key used) on `{$row['table']}`";
        }
        if (strpos($extra, 'Using filesort') !== false) {
          $warnings[] = "Filesort on `{$row['table']}`";
        }
        if (strpos($extra, 'Using temporary') !== false) {
          $warnings[] = "Temporary table on `{$row['table']}`";
        }
      }

      $results[$name] = [
        'sql' => $query['sql'],
        'tables' => $query['tables'],
        'explain' => $explain,
        'elapsed_ms' => $elapsed,
        'access_types' => $accessTypes,
        'uses_index' => $usesIndex,
        'warnings' => $warnings,
      ];
    }
    uasort($results, function($a, $b) { return $b['elapsed_ms'] <=> $a['elapsed_ms']; });
    return $results;
  }

  private static function getAliasToTableMap(string $sql): array {
    $map = [];
    if (preg_match_all('/(?:FROM|JOIN)\s+`?(\w+)`?\s+(?:AS\s+)?(\w+)/i', $sql, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $m) {
        $realTable = $m[1];
        $alias = $m[2];
        if (strtolower($alias) !== strtolower($realTable)) {
          $map[strtolower($alias)] = $realTable;
        }
        $map[strtolower($realTable)] = $realTable;
      }
    }
    return $map;
  }

  public static function findMissingIndexes(): array {
    $analysis = self::analyzeAllQueries();
    $existingIndexes = self::getExistingIndexes();
    $tableColumnsNeeded = [];

    foreach ($analysis as $name => $info) {
      if (empty($info['warnings'])) continue;

      $aliasMap = self::getAliasToTableMap($info['sql']);

      foreach ($info['explain'] as $row) {
        if (isset($row['error'])) continue;
        $type = $row['type'] ?? '';
        $explainTable = $row['table'] ?? '';
        if ($explainTable === '' || $type === 'const' || $type === 'system' || $type === 'eq_ref') continue;
        if ($type !== 'ALL' && $type !== 'index') continue;

        $realTable = $aliasMap[strtolower($explainTable)] ?? $explainTable;

        $whereCols = self::extractWhereColumns($info['sql'], $explainTable);
        $joinCols = self::extractJoinColumns($info['sql'], $explainTable);
        $orderCols = self::extractOrderColumns($info['sql'], $explainTable);

        if (!isset($tableColumnsNeeded[$realTable])) {
          $tableColumnsNeeded[$realTable] = ['single' => [], 'composite' => []];
        }

        foreach ($whereCols as $col) {
          if (!in_array($col, $tableColumnsNeeded[$realTable]['single'])) {
            $tableColumnsNeeded[$realTable]['single'][] = $col;
          }
        }

        $allCols = array_merge($whereCols, $joinCols);
        if (count($allCols) >= 2) {
          $compositeKey = implode(',', $allCols);
          $exists = false;
          foreach ($tableColumnsNeeded[$realTable]['composite'] as $existing) {
            if (implode(',', $existing) === $compositeKey) { $exists = true; break; }
          }
          if (!$exists) {
            $tableColumnsNeeded[$realTable]['composite'][] = $allCols;
          }
        }

        foreach ($orderCols as $col) {
          if (!in_array($col, $tableColumnsNeeded[$realTable]['single'])) {
            $tableColumnsNeeded[$realTable]['single'][] = $col;
          }
        }
      }
    }

    $suggestions = [];
    foreach ($tableColumnsNeeded as $table => $data) {
      foreach ($data['single'] as $col) {
        $indexName = "idx_{$table}_{$col}";
        if (!self::indexExists($existingIndexes, $table, [$col])) {
          $reason = self::findQueryUsingColumn($analysis, $table, $col);
          $suggestions[] = [
            'table' => $table,
            'index_name' => $indexName,
            'columns' => [$col],
            'sql' => "CREATE INDEX $indexName ON $table($col)",
            'reason' => $reason,
          ];
        }
      }
      foreach ($data['composite'] as $cols) {
        $indexName = "idx_{$table}_" . implode('_', $cols);
        if (!self::indexExists($existingIndexes, $table, $cols)) {
          $reason = self::findQueryUsingColumns($analysis, $table, $cols);
          $suggestions[] = [
            'table' => $table,
            'index_name' => $indexName,
            'columns' => $cols,
            'sql' => "CREATE INDEX $indexName ON $table(" . implode(', ', $cols) . ")",
            'reason' => $reason,
          ];
        }
      }
    }

    return $suggestions;
  }

  private static function extractWhereColumns(string $sql, string $table): array {
    $cols = [];
    $prefix = self::getTableAlias($sql, $table);
    $patterns = [
      "/WHERE.*?{$prefix}\.(\w+)\s*=/i",
      "/AND\s+{$prefix}\.(\w+)\s*[=<>!]/i",
      "/AND\s+{$prefix}\.(\w+)\s+LIKE/i",
      "/AND\s+{$prefix}\.(\w+)\s+IS/i",
    ];
    foreach ($patterns as $pattern) {
      if (preg_match_all($pattern, $sql, $matches)) {
        foreach ($matches[1] as $col) {
          $lower = strtolower($col);
          if (!in_array($lower, $cols) && $lower !== 'score_id' && $lower !== 'game_id' && $lower !== 'ban_id' && $lower !== 'leaderboard_id') {
            $cols[] = $lower;
          }
        }
      }
    }
    return $cols;
  }

  private static function extractJoinColumns(string $sql, string $table): array {
    $cols = [];
    $prefix = self::getTableAlias($sql, $table);
    $patterns = [
      "/ON\s+{$prefix}\.(\w+)\s*=/i",
      "/=\s*{$prefix}\.(\w+)\b/i",
    ];
    foreach ($patterns as $pattern) {
      if (preg_match_all($pattern, $sql, $matches)) {
        foreach ($matches[1] as $col) {
          $lower = strtolower($col);
          if (!in_array($lower, $cols)) {
            $cols[] = $lower;
          }
        }
      }
    }
    return $cols;
  }

  private static function extractOrderColumns(string $sql, string $table): array {
    $cols = [];
    $prefix = self::getTableAlias($sql, $table);
    if (preg_match_all("/ORDER\s+BY\s+{$prefix}\.(\w+)/i", $sql, $matches)) {
      foreach ($matches[1] as $col) {
        $lower = strtolower($col);
        if (!in_array($lower, $cols)) {
          $cols[] = $lower;
        }
      }
    }
    return $cols;
  }

  private static function getTableAlias(string $sql, string $table): string {
    $pattern = "/(?:FROM|JOIN)\s+`?{$table}`?\s+(?:AS\s+)?(\w+)/i";
    if (preg_match($pattern, $sql, $matches)) {
      return preg_replace('/[`"]/', '', $matches[1]);
    }
    return '';
  }

  private static function indexExists(array $existingIndexes, string $table, array $columns): bool {
    if (!isset($existingIndexes[$table])) return false;
    $lowerCols = array_map('strtolower', $columns);
    sort($lowerCols);

    $grouped = [];
    foreach ($existingIndexes[$table] as $index) {
      $key = $index['key_name'];
      if (!isset($grouped[$key])) $grouped[$key] = [];
      $grouped[$key][] = strtolower($index['column_name']);
    }

    foreach ($grouped as $keyName => $indexCols) {
      sort($indexCols);
      if ($indexCols === $lowerCols) return true;
      if (count($indexCols) > count($lowerCols)) {
        $isPrefix = true;
        for ($i = 0; $i < count($lowerCols); $i++) {
          if ($indexCols[$i] !== $lowerCols[$i]) { $isPrefix = false; break; }
        }
        if ($isPrefix) return true;
      }
    }
    return false;
  }

  private static function findQueryUsingColumn(array $analysis, string $table, string $col): string {
    foreach ($analysis as $name => $info) {
      if (!in_array($table, $info['tables'])) continue;
      $sql = $info['sql'];
      $aliasMap = self::getAliasToTableMap($sql);
      $prefixes = array_keys(array_filter($aliasMap, function($v) use ($table) { return strcasecmp($v, $table) === 0; }));
      if (empty($prefixes)) $prefixes = [$table];
      foreach ($prefixes as $prefix) {
        if (preg_match("/{$prefix}\.`?{$col}`?\s*[=<>!]/i", $sql) ||
            preg_match("/{$prefix}\.`?{$col}`?\s+LIKE/i", $sql) ||
            preg_match("/{$prefix}\.`?{$col}`?\s+IS/i", $sql) ||
            preg_match("/ON\s+{$prefix}\.`?{$col}`?\s*=/i", $sql) ||
            preg_match("/ORDER\s+BY\s+{$prefix}\.`?{$col}`/i", $sql)) {
          return "Usato in $name";
        }
      }
    }
    return "Necessario per ottimizzazione";
  }

  private static function findQueryUsingColumns(array $analysis, string $table, array $cols): string {
    foreach ($analysis as $name => $info) {
      if (!in_array($table, $info['tables'])) continue;
      $sql = $info['sql'];
      $aliasMap = self::getAliasToTableMap($sql);
      $prefixes = array_keys(array_filter($aliasMap, function($v) use ($table) { return strcasecmp($v, $table) === 0; }));
      if (empty($prefixes)) $prefixes = [$table];
      $allFound = true;
      foreach ($cols as $col) {
        $colFound = false;
        foreach ($prefixes as $prefix) {
          if (preg_match("/{$prefix}\.`?{$col}`?\s*[=<>!LIKEIS]/i", $sql) ||
              preg_match("/ON\s+{$prefix}\.`?{$col}`?\s*=/i", $sql)) {
            $colFound = true;
            break;
          }
        }
        if (!$colFound) { $allFound = false; break; }
      }
      if ($allFound) return "Usato in $name";
    }
    return "Necessario per ottimizzazione";
  }

  private static function getExistingIndexes(): array {
    global $db;
    $indexes = [];
    $tables = ['scores', 'games', 'players', 'bans', 'leaderboards', 'users', 'teams', 'team_members'];
    foreach ($tables as $table) {
      try {
        $result = $db->query("SHOW INDEX FROM $table");
        while ($row = $result->fetch_assoc()) {
          $indexes[$table][] = [
            'key_name' => $row['Key_name'],
            'column_name' => $row['Column_name'],
            'seq_in_index' => $row['Seq_in_index'],
            'unique' => !$row['Non_unique'],
          ];
        }
      } catch (Exception $e) {
        // table might not exist
      }
    }
    return $indexes;
  }

  public static function applyIndex(string $sql): array {
    global $db;
    try {
      $db->query($sql);
      return ['success' => true, 'message' => 'Indice applicato con successo'];
    } catch (Exception $e) {
      return ['success' => false, 'message' => $e->getMessage()];
    }
  }

  public static function applyAllIndexes(array $indexSqls): array {
    $results = [];
    foreach ($indexSqls as $sql) {
      $results[] = self::applyIndex($sql);
    }
    return $results;
  }

  public static function getSlowQueries(int $thresholdMs = 50): array {
    $analysis = self::analyzeAllQueries();
    $slow = [];
    foreach ($analysis as $name => $data) {
      if ($data['elapsed_ms'] >= $thresholdMs) {
        $slow[$name] = $data;
      }
    }
    return $slow;
  }
}
