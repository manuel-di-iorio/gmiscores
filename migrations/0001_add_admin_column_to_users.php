<?php

return [
  'description' => 'Add admin boolean column to users table',
  'sql' => [
    "ALTER TABLE users ADD COLUMN admin TINYINT(1) NOT NULL DEFAULT 0 AFTER approved",
  ],
];
