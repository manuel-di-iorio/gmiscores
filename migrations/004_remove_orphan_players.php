<?php
return [
  'description' => 'Remove orphan players with no scores',
  'sql' => [
    "DELETE p FROM players p LEFT JOIN scores s ON p.player_id = s.player_id WHERE s.score_id IS NULL",
  ],
];
