<?php
return [
  'description' => 'Add team_id column to games table',
  'sql' => [
    "ALTER TABLE games ADD COLUMN team_id INT DEFAULT NULL AFTER user_id",
    "ALTER TABLE games ADD FOREIGN KEY (team_id) REFERENCES teams(team_id) ON DELETE SET NULL",
  ],
];
