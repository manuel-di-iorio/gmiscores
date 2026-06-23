/// @func gmi_player_logout(opts)
/// @desc Log out the player
/// @example gmi_player_logout()
function gmi_player_logout(opts = {}) {
	var on_success = variable_struct_exists(opts, "on_success") ? opts.on_success : undefined;
	var on_error = variable_struct_exists(opts, "on_error") ? opts.on_error : undefined;
	
	var url = global.GMI_ENDPOINT_HOST + "/player-logout.php";
	show_debug_message("[GMI Player] Logging out...");
	var _req_id = http_get(url);
	global.gmi_requests[$ string(_req_id)] = { on_success: on_success, on_error: on_error };
}
