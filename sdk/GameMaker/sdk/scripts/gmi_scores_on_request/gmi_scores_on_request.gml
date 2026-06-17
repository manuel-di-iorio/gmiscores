/// @func gmi_scores_on_request([leaderboard])
/// @desc Parse the scores returned from the Get Scores request. 
///       The scores are stored into the 'gmi_scores_list' array and gmi_scores_player_score struct
/// @arg {real} leaderboard Leaderboard ID. A game may have multiple leaderboards. (Optional)
/// @ret {real} HTTP Status Code. 200 or error code (>= 400 & <600)
function gmi_scores_on_request(leaderboard="default") {
	// Check if an error happened
	if (async_load[? "status"] != 0) return 500;
	if (async_load[? "http_status"] != 200) {
		var error = json_parse(async_load[? "result"]);
		show_debug_message("[GMI Scores] Request error: " + error.code + " - Message: " + error.message);
		return error.status;	
	}

	if (async_load[? "id"] == gmi_scores_list_req) {
		// Cleanup the old scores data
		if (gmi_scores_list != noone) gmi_scores_list = undefined;

		// Parse the new scores
		var result = json_parse(async_load[? "result"]);		
		var list = result.scores;
		gmi_scores_list = list;
		gmi_scores_player_score = result.playerScore;
	}

	return 200;
}
