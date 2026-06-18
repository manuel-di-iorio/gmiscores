// Initialize GMI Cloud Services with the game data
gmi_init("<game_id>", "<secret>");

// Get the scores list
gmi_scores_get_list({ leaderboard_id: <leaderboard_id>, player: "Harry" });