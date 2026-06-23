/// @func __gmi_player_poll_login()
/// @desc Internal polling function. Called automatically after gmi_login().
function __gmi_player_poll_login() {
	if (global.GMI_PLAYER_LOGGED) return;
	if (is_undefined(global.GMI_PLAYER_SESSION)) return;
	if (global.gmi_player_poll_count >= global.gmi_player_poll_max) {
		if (global.GMI_LOGS) show_debug_message("[GMI] Polling timeout, please try again.");
		return;
	}
	if (global.GMI_PLAYER_LOGGING_IN) return;
	
	global.GMI_PLAYER_LOGGING_IN = true;
	global.gmi_player_poll_count++;
	
	if (global.GMI_LOGS) show_debug_message("[GMI] Polling login status (" + string(global.gmi_player_poll_count) + "/" + string(global.gmi_player_poll_max) + ")...");
	
	var url = global.GMI_ENDPOINT_HOST + "/player-login-session.php?session=" + global.GMI_PLAYER_SESSION;
	var _req_id = http_get(url);
	global.gmi_requests[$ string(_req_id)] = { on_success: undefined, on_error: undefined };
}
