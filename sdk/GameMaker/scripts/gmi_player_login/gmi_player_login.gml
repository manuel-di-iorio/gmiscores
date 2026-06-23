/// @func gmi_player_login(opts)
/// @desc Open the Discord login page for the player
/// @example gmi_player_login()
function gmi_player_login(opts = {}) {
	if (global.GMI_PLAYER_LOGGED) return;
	
	var on_success = variable_struct_exists(opts, "on_success") ? opts.on_success : undefined;
	var on_error = variable_struct_exists(opts, "on_error") ? opts.on_error : undefined;
	
	var hex = "0123456789abcdef";
	var sessionToken = "";
	for (var i = 0; i < 64; i++) {
		sessionToken += string_char_at(hex, irandom(15) + 1);
	}
	
	global.GMI_PLAYER_SESSION = sessionToken;
	global.GMI_PLAYER_LOGGING_IN = false;
	global.gmi_player_poll_timer = 0;
	global.gmi_player_poll_count = 0;
	
	// Store login callbacks to fire when the session check succeeds
	global.GMI_PLAYER_LOGIN_CB = {
		on_success: on_success,
		on_error: on_error
	};
	
	var loginUrl = global.GMI_ENDPOINT_HOST + "/../../player-auth/discord/login.php?session=" + sessionToken;
	show_debug_message("[GMI Player] Opening login: " + loginUrl);
	url_open(loginUrl);
}
