/// @func gmi_logout(opts)
/// @desc Log out the player
/// @example gmi_logout()
function gmi_logout(opts = {}) {
	var on_success = variable_struct_exists(opts, "on_success") ? opts.on_success : undefined;
	var on_error = variable_struct_exists(opts, "on_error") ? opts.on_error : undefined;
	
	// Clear local saved token
	gmi_player_clear_saved_token();
	
	// Reset player state
	global.GMI_PLAYER_LOGGED = false;
	global.GMI_PLAYER_TOKEN = undefined;
	global.GMI_PLAYER_USERNAME = undefined;
	global.GMI_PLAYER_ID = undefined;
	global.GMI_PLAYER_SESSION = undefined;
	global.GMI_PLAYER_LOGGING_IN = false;
	
	var url = global.GMI_ENDPOINT_HOST + "/player-logout.php";
	if (global.GMI_LOGS) show_debug_message("[GMI Player] Logging out...");
	var _req_id = http_get(url);
	global.gmi_requests[$ string(_req_id)] = { on_success: on_success, on_error: on_error };
}
