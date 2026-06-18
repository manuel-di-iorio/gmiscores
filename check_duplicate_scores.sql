-- ============================================================
-- DIAGNOSTICA: Punteggi duplicati per giocatore/gioco
-- Esegui queste query PRIMA della migrazione per capire l'entità
-- ============================================================

-- 1. Riepilogo: quanti gruppi duplicati e quanti punteggi rimuovere
SELECT
  COUNT(*) AS duplicate_groups,
  SUM(cnt - 1) AS scores_to_remove,
  (SELECT COUNT(*) FROM scores) AS total_scores
FROM (
  SELECT player_id, game_id, leaderboard_id, COUNT(*) AS cnt
  FROM scores
  GROUP BY player_id, game_id, leaderboard_id
  HAVING cnt > 1
) t;

-- 2. Dettaglio: quali giocatori/giochi/leaderboard hanno duplicati
SELECT
  p.username,
  g.name AS game_name,
  s.leaderboard_id,
  COUNT(*) AS score_count,
  MAX(s.score) AS best_score,
  MIN(s.score) AS worst_score,
  MIN(s.created_at) AS first_score,
  MAX(s.created_at) AS last_score
FROM scores s
INNER JOIN players p ON s.player_id = p.player_id
INNER JOIN games g ON s.game_id = g.game_id
GROUP BY s.player_id, s.game_id, s.leaderboard_id, p.username, g.name
HAVING COUNT(*) > 1
ORDER BY score_count DESC, p.username;

-- 3. Solo conteggio per leaderboard_id (per capire se ci sono molti NULL)
SELECT
  leaderboard_id,
  COUNT(*) AS duplicate_groups,
  SUM(cnt - 1) AS scores_to_remove
FROM (
  SELECT player_id, game_id, leaderboard_id, COUNT(*) AS cnt
  FROM scores
  GROUP BY player_id, game_id, leaderboard_id
  HAVING cnt > 1
) t
GROUP BY leaderboard_id;
