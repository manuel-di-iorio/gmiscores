<?php
return [
  'description' => 'Remove avatar column from users table (Discord avatar URLs are unreliable)',
  'sql' => [
    "ALTER TABLE users DROP COLUMN avatar",
  ],
];
