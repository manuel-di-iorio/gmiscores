/// @func gmi_scores_get_list(leaderboard_id, page, limit, order, start_time, end_time, player, includePlayer)
/// @desc Get the leaderboard scores
/// @arg {struct} opts
	// @arg {real} opts.leaderboard Leaderboard ID
	// @arg {real} opts.page Current page. Use in combination with 'limit' if you need to get all the scores.
	// @arg {real} opts.limit Number of results to get (by default 10, max 1000 per page).
	// @arg {string} opts.order Results sorting. Can be DESC (default) or ASC.
	// @arg {string} opts.startTime Filter the scores starting from this date (eg. "2020-05-04" or "2020-05-04 22:20:20").
	// @arg {string} opts.endTime Filter the scores ending to this date (eg. "2020-05-04" or "2020-05-04 22:20:20").
	// @arg {string|real} opts.player Player ID or name (base64). If specified, filter the scores by this player.
	// @arg {string} opts.includePlayer Player name (base64). If specified, include in the response, the best score of this player.
function gmi_scores_get_list(opts = {}) {
	var leaderboard_id = opts.leaderboard_id;
	var page = variable_struct_exists(opts, "page") ? opts.page : 0;
	var limit = variable_struct_exists(opts, "limit") ? opts.limit : 10;
	var order = variable_struct_exists(opts, "order") ? opts.order : "DESC";
	var start_time = variable_struct_exists(opts, "startTime") ? opts.startTime : undefined;
	var end_time = variable_struct_exists(opts, "endTime") ? opts.endTime : undefined;
	var player = variable_struct_exists(opts, "player") ? opts.player : undefined;
	var includePlayer = variable_struct_exists(opts, "includePlayer") ? opts.includePlayer : undefined;
	
	var url = global.GMI_ENDPOINT_HOST + "/list.php?game=" + string(global.GMI_GAME_CLIENT_ID);

	// Leaderboard
	url += "&leaderboard_id=" + string(leaderboard_id);

	// Page
	if (!is_undefined(page)) url += "&page=" + string(page);

	// Limit
	if (!is_undefined(limit)) url += "&limit=" + string(limit);

	// Order
	if (!is_undefined(order)) url += "&order=" + order;

	// Start time
	if (!is_undefined(start_time)) url += "&startTime=" + start_time;

	// End time
	if (!is_undefined(end_time)) url += "&endTime=" + end_time;

	// Player
	if (!is_undefined(player)) {
		if (is_string(player)) player = base64_encode(player);
		url += "&player=" + string(player);
	}

	// Include player best score
	if (!is_undefined(includePlayer)) url += "&includePlayer=" + base64_encode(includePlayer);

	// Hash (always computed and sent for authentication)
	var dataForHash = "game=" + string(global.GMI_GAME_CLIENT_ID);
	if (!is_undefined(leaderboard_id)) dataForHash += "&leaderboard_id=" + string(leaderboard_id);
	url += "&hash=" + sha1_string_utf8(dataForHash + global.GMI_GAME_CLIENT_SECRET);
    show_debug_message("[GMI Scores] Scores.GetList: " + url);

	gmi_scores_list = noone;
	gmi_scores_list_req = http_get(url);
	gmi_scores_send_req = noone;
	gmi_scores_player_score = noone;
}
