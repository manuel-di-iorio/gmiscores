// Send a random score for the player Harry
gmi_scores_send({ leaderboard_id: <leaderboard_id>, player: "Harry", score: irandom_range(1, 9999) });