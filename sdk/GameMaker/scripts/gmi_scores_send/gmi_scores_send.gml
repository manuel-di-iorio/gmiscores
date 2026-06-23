/// @func gmi_scores_send(opts)
/// @desc Send a score to a game leaderboard.
/// @example gmi_scores_send({ leaderboard_id: 30, score: 5000 })
/// @arg {struct} opts
function gmi_scores_send(opts = {}) {
	var player = variable_struct_exists(opts, "player") ? opts.player : undefined;
	var points = opts.score;
	var leaderboard_id = opts.leaderboard_id;
	var insert_mode = variable_struct_exists(opts, "insertMode") ? opts.insertMode : "higher";
	var scoreData = variable_struct_exists(opts, "data") ? opts.data : undefined;
	var on_success = variable_struct_exists(opts, "on_success") ? opts.on_success : undefined;
	var on_error = variable_struct_exists(opts, "on_error") ? opts.on_error : undefined;

	var authToken = undefined;
	if (global.GMI_PLAYER_LOGGED) {
		authToken = global.GMI_PLAYER_TOKEN;
		player = global.GMI_PLAYER_USERNAME;
	}

	var data = "game=" + string(global.GMI_GAME_CLIENT_ID);
	data += "&leaderboard_id=" + string(leaderboard_id);
	data += "&score=" + string(points) + "&player=" + base64_encode(is_undefined(player) ? "" : player);

	var hash = sha1_string_utf8(data + global.GMI_GAME_CLIENT_SECRET);
	var totalData = data + "&hash=" + hash;

	if (!is_undefined(insert_mode)) totalData += "&insertMode=" + insert_mode;
	if (!is_undefined(scoreData)) totalData += "&data=" + scoreData;
	if (!is_undefined(authToken)) totalData += "&token=" + string_replace_all(string_replace_all(string_replace_all(authToken, "+", "%2B"), "/", "%2F"), "=", "%3D");
        
    show_debug_message("[GMI] SendScore: " + totalData);

	var _req_id = http_post_string(global.GMI_ENDPOINT_HOST + "/add.php", totalData);
	global.gmi_requests[$ string(_req_id)] = { on_success: on_success, on_error: on_error };
}
