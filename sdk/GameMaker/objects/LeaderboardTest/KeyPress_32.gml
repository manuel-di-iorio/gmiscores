// SPACE - Send a random score
var _score = irandom_range(1, 9999);
gmi_scores_send({ leaderboard_id: 30, score: _score, player: "Harry" });