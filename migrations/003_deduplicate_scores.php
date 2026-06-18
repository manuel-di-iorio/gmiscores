<?php
return [
  'description' => 'Deduplicate scores: keep only the best score per player per game per leaderboard',
  'sql' => [
    // Self-join: delete s1 when a "better" s2 exists in the same group.
    // Better = higher score, or same score but older (lower score_id).
    "DELETE s1 FROM scores s1
     INNER JOIN scores s2
       ON  s1.player_id = s2.player_id
       AND s1.game_id = s2.game_id
       AND s1.leaderboard_id <=> s2.leaderboard_id
     WHERE s2.score > s1.score
        OR (s2.score = s1.score AND s2.score_id < s1.score_id)",
  ],
];
