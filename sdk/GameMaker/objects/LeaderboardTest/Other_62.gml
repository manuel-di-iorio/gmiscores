// Parse the incoming scores data
gmi_scores_on_request();

// Get again the updated highscore, once the send score request has been processed
if (gmi_scores_send_req != noone) {
	gmi_scores_get_list({ leaderboard_id: <leaderboard_id>, player: "Harry" });
	gmi_scores_send_req = noone;
}