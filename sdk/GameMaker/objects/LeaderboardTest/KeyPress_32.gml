// SPACE - Send a random score
var _score = irandom_range(1, 9999);
gmi_scores_send({
	leaderboard_id: 30,
	score: _score,
    player: "Harry",
	on_success: function(data) {
		gmi_scores_get_list({ leaderboard_id: 30 });
	},
	on_error: function(data) {
		show_debug_message("[Test] Send failed: " + data.message);
	}
});