-- Migration: Multiple leaderboards per game
-- 1. Create leaderboards table
DROP TABLE IF EXISTS leaderboards;
CREATE TABLE leaderboards (
    leaderboard_id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    name VARCHAR(255) NOT NULL DEFAULT 'Default',
    description TEXT DEFAULT NULL,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(game_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Rename scores.leaderboard_id (varchar) to scores.tags
ALTER TABLE scores CHANGE COLUMN leaderboard_id tags VARCHAR(255) DEFAULT 'default';

-- 3. Add leaderboard_id INT column (nullable initially for migration)
ALTER TABLE scores ADD COLUMN leaderboard_id INT DEFAULT NULL AFTER game_id;

-- 4. Create a default leaderboard for each game that has scores
INSERT INTO leaderboards (game_id, name, user_id)
SELECT DISTINCT s.game_id, 'Default', g.user_id
FROM scores s
JOIN games g ON g.game_id = s.game_id;

-- 5. Assign existing scores to their game's default leaderboard
UPDATE scores s
JOIN leaderboards l ON l.game_id = s.game_id
SET s.leaderboard_id = l.leaderboard_id
WHERE s.leaderboard_id IS NULL;

-- 6. For games with no scores, also create a default leaderboard
INSERT INTO leaderboards (game_id, name, user_id)
SELECT g.game_id, 'Default', g.user_id
FROM games g
LEFT JOIN leaderboards l ON l.game_id = g.game_id
WHERE l.leaderboard_id IS NULL;

-- 7. Add FK constraint with CASCADE delete
ALTER TABLE scores ADD FOREIGN KEY (leaderboard_id) REFERENCES leaderboards(leaderboard_id) ON DELETE CASCADE;
