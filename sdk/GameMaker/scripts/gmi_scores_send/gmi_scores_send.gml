/// @func gmi_scores_send(leaderboard_id, player, score, [insert_mode])
/// @desc Send a score to a game leaderboard
/// @example gmi_scores_send({ player: "pippo", score: 58 })
/// @arg {struct} opts
    // @arg {string} opts.player Player name.
	// @arg {real} opts.score Player score.
	// @arg {real} opts.leaderboard Leaderboard ID.
	// @arg {bool} opts.useSign If truthy, signs the request with your private key.
	// @arg {string} opts.insertMode Can be "higher" (default) or "lower".
	// @arg {string} opts.data Optional custom string to associate to this score
function gmi_scores_send(opts = {}) {
	var player = opts.player;
	var points = opts.score;
	var leaderboard_id = opts.leaderboard_id;
	var insert_mode = variable_struct_exists(opts, "insertMode") ? opts.insertMode : "higher";
	var scoreData = variable_struct_exists(opts, "data") ? opts.data : undefined;

	var data = "game=" + string(global.GMI_GAME_CLIENT_ID);
	data += "&leaderboard_id=" + string(leaderboard_id);
	data += "&score=" + string(points) + "&player=" + base64_encode(player);

	var hash = sha1_string_utf8(data + global.GMI_GAME_CLIENT_SECRET);
	var totalData = data + "&hash=" + hash;

	// Specify the insert mode
	if (!is_undefined(insert_mode)) totalData += "&insertMode=" + insert_mode;
	
	// Add the custom data
	if (!is_undefined(scoreData)) totalData += "&data=" + scoreData;
        
    show_debug_message("[GMI Scores] Scores.SendScore: " + totalData);

	// Send the score
	gmi_scores_send_req = http_post_string(global.GMI_ENDPOINT_HOST + "/add.php", totalData);
	gmi_scores_list_req = noone;
}
