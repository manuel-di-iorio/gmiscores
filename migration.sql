-- 1. Crea la tabella leaderboards (SENZA FK inizialmente)
DROP TABLE IF EXISTS leaderboards;
CREATE TABLE leaderboards (
    leaderboard_id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    name VARCHAR(255) NOT NULL DEFAULT 'Default',
    description TEXT DEFAULT NULL,
    user_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 1b. Aggiunge FK separatamente per diagnostica piu' chiara
ALTER TABLE leaderboards ADD CONSTRAINT fk_lb_game
    FOREIGN KEY (game_id) REFERENCES games(game_id) ON DELETE CASCADE;

ALTER TABLE leaderboards ADD CONSTRAINT fk_lb_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- 2. Rinomina scores.leaderboard_id (varchar) in scores.tags
ALTER TABLE scores CHANGE COLUMN leaderboard_id tags VARCHAR(255) DEFAULT 'default';

-- 3. Aggiunge colonna leaderboard_id INT (inizialmente nullable per la migrazione)
ALTER TABLE scores ADD COLUMN leaderboard_id INT DEFAULT NULL AFTER game_id;

-- 4. Crea una leaderboard "Default" per ogni gioco che ha punteggi
INSERT INTO leaderboards (game_id, name, user_id)
SELECT DISTINCT s.game_id, 'Default', g.user_id
FROM scores s
JOIN games g ON g.game_id = s.game_id;

-- 5. Assegna i punteggi esistenti alla leaderboard di default del loro gioco
UPDATE scores s
JOIN leaderboards l ON l.game_id = s.game_id
SET s.leaderboard_id = l.leaderboard_id
WHERE s.leaderboard_id IS NULL;

-- 6. Crea una leaderboard di default anche per i giochi senza punteggi
INSERT INTO leaderboards (game_id, name, user_id)
SELECT g.game_id, 'Default', g.user_id
FROM games g
LEFT JOIN leaderboards l ON l.game_id = g.game_id
WHERE l.leaderboard_id IS NULL;

-- 7. Aggiunge FK con CASCADE delete su scores
ALTER TABLE scores ADD CONSTRAINT fk_scores_leaderboard
    FOREIGN KEY (leaderboard_id) REFERENCES leaderboards(leaderboard_id) ON DELETE CASCADE;
