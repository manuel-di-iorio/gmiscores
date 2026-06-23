/// @func gmi_player_is_logged()
/// @desc Check if the player is logged in. Call in Step event. Polls the server every 5s after a login request, up to 3 min.
/// @example if (gmi_player_is_logged()) { ... }
function gmi_player_is_logged() {
	if (global.GMI_PLAYER_LOGGED) return true;
	if (is_undefined(global.GMI_PLAYER_SESSION)) return false;
	
	if (global.gmi_player_poll_count >= global.gmi_player_poll_max) return false;
	
	global.gmi_player_poll_timer++;
	if (global.gmi_player_poll_timer < global.gmi_player_poll_interval) return false;
	global.gmi_player_poll_timer = 0;
	
	if (global.GMI_PLAYER_LOGGING_IN) return false;
	global.GMI_PLAYER_LOGGING_IN = true;
	global.gmi_player_poll_count++;
    
    show_debug_message("[GMI] Checking if the player has logged in. Session: " +  + global.GMI_PLAYER_SESSION);
	
	var url = global.GMI_ENDPOINT_HOST + "/player-login-session.php?session=" + global.GMI_PLAYER_SESSION;
	var _req_id = http_get(url);
	global.gmi_requests[$ string(_req_id)] = { on_success: undefined, on_error: undefined };
	
	return false;
}
