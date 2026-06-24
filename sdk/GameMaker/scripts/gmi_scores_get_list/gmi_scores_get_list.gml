/// @func gmi_scores_get_list(opts)
/// @desc Get the leaderboard scores
/// @example gmi_scores_get_list({ leaderboard_id: 30 })
/// @arg {struct} opts
function gmi_scores_get_list(opts = {}) {
	var leaderboard_id = opts.leaderboard_id;
	var page = variable_struct_exists(opts, "page") ? opts.page : 0;
	var limit = variable_struct_exists(opts, "limit") ? opts.limit : 10;
	var order = variable_struct_exists(opts, "order") ? opts.order : "DESC";
	var tags = variable_struct_exists(opts, "tags") ? opts.tags : undefined;
	var start_time = variable_struct_exists(opts, "startTime") ? opts.startTime : undefined;
	var end_time = variable_struct_exists(opts, "endTime") ? opts.endTime : undefined;
	var player = variable_struct_exists(opts, "player") ? opts.player : undefined;
	var includePlayer = variable_struct_exists(opts, "includePlayer") ? opts.includePlayer : undefined;
	var authToken = variable_struct_exists(opts, "token") ? opts.token : undefined;
	if (is_undefined(authToken) && global.GMI_PLAYER_LOGGED) authToken = global.GMI_PLAYER_TOKEN;
	var on_success = variable_struct_exists(opts, "on_success") ? opts.on_success : undefined;
	var on_error = variable_struct_exists(opts, "on_error") ? opts.on_error : undefined;
	
	var url = global.GMI_ENDPOINT_HOST + "/list.php?game=" + string(global.GMI_GAME_CLIENT_ID);
	url += "&leaderboard_id=" + string(leaderboard_id);

	if (!is_undefined(page)) url += "&page=" + string(page);
	if (!is_undefined(limit)) url += "&limit=" + string(limit);
	if (!is_undefined(order)) url += "&order=" + order;
	if (!is_undefined(tags)) url += "&tags=" + string(tags);
	if (!is_undefined(start_time)) url += "&startTime=" + start_time;
	if (!is_undefined(end_time)) url += "&endTime=" + end_time;

	if (!is_undefined(player)) {
		if (is_string(player)) player = base64_encode(player);
		url += "&player=" + string(player);
	}

	if (!is_undefined(includePlayer)) url += "&includePlayer=" + base64_encode(includePlayer);
	if (!is_undefined(authToken)) url += "&token=" + authToken;

	var dataForHash = "game=" + string(global.GMI_GAME_CLIENT_ID);
	if (!is_undefined(leaderboard_id)) dataForHash += "&leaderboard_id=" + string(leaderboard_id);
	url += "&hash=" + sha1_string_utf8(dataForHash + global.GMI_GAME_CLIENT_SECRET);
    if (global.GMI_LOGS) show_debug_message("[GMI] GetList: " + url);

	var _req_id = http_get(url);
	global.gmi_requests[$ string(_req_id)] = { on_success: on_success, on_error: on_error };
}
