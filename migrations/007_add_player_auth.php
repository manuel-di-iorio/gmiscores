<?php
return [
  'description' => 'Add player auth support (Discord login for players)',
  'sql' => [
    "ALTER TABLE users ADD COLUMN auth_discord_id VARCHAR(64) DEFAULT NULL AFTER admin",
    "UPDATE users SET auth_discord_id = discord_user_id WHERE auth_discord_id IS NULL",
    "ALTER TABLE users ADD UNIQUE INDEX idx_users_auth_discord_id (auth_discord_id)",
    "ALTER TABLE users DROP COLUMN discord_user_id",
    "ALTER TABLE players ADD COLUMN user_id INT DEFAULT NULL AFTER username",
    "ALTER TABLE players ADD INDEX idx_players_user_id (user_id)",
    "ALTER TABLE players ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL",
    "ALTER TABLE scores ADD COLUMN user_id INT DEFAULT NULL AFTER player_id",
    "ALTER TABLE scores ADD INDEX idx_scores_user_id (user_id)",
    "ALTER TABLE scores ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL",
    "ALTER TABLE games ADD COLUMN require_player_auth TINYINT(1) DEFAULT 0 AFTER team_id",
  ],
];
