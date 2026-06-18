<?php
return [
  'description' => 'Deduplicate scores: keep only the best score per player per game per leaderboard',
  'sql' => [
    // Keep only the row with MAX(score), tiebreak by MIN(score_id) (oldest),
    // for each (player_id, game_id, leaderboard_id) group.
    "DELETE FROM scores WHERE score_id NOT IN (
      SELECT keep_id FROM (
        SELECT MIN(s.score_id) AS keep_id
        FROM scores s
        INNER JOIN (
          SELECT player_id, game_id, leaderboard_id, MAX(score) AS max_score
          FROM scores
          GROUP BY player_id, game_id, leaderboard_id
        ) g ON s.player_id = g.player_id
           AND s.game_id = g.game_id
           AND s.leaderboard_id = g.leaderboard_id
           AND s.score = g.max_score
        GROUP BY s.player_id, s.game_id, s.leaderboard_id
      ) tmp
    )",
  ],
];
