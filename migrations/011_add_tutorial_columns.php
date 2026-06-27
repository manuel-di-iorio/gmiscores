<?php
return [
  'description' => 'Add tutorial_progress and tutorial_skipped columns to users table',
  'sql' => [
    "ALTER TABLE users ADD COLUMN tutorial_progress VARCHAR(64) DEFAULT NULL AFTER admin",
    "ALTER TABLE users ADD COLUMN tutorial_skipped TINYINT(1) DEFAULT 0 AFTER tutorial_progress",
  ],
];
