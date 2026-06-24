/// @func gmi_login(opts)
/// @desc Open the Discord login page for the player
/// @example gmi_login()
function gmi_login(opts = {}) {
	if (global.GMI_PLAYER_LOGGED) return;
	
	var on_success = variable_struct_exists(opts, "on_success") ? opts.on_success : undefined;
	var on_error = variable_struct_exists(opts, "on_error") ? opts.on_error : undefined;
	
	// Store login callbacks to fire when the session check succeeds
	global.GMI_PLAYER_LOGIN_CB = {
		on_success: on_success,
		on_error: on_error
	};
	
	// Request a session token from the server
	var url = global.GMI_ENDPOINT_HOST + "/../../player-auth/login-start.php";
	if (global.GMI_LOGS) show_debug_message("[GMI Player] Requesting login session...");
	var _req_id = http_post_string(url, "");
	global.gmi_requests[$ string(_req_id)] = {
		on_success: function(_data) {
			var _session = variable_struct_exists(_data, "session_token") ? _data.session_token : undefined;
			if (is_undefined(_session)) {
				if (global.GMI_LOGS) show_debug_message("[GMI Player] Failed to get session token.");
				if (!is_undefined(global.GMI_PLAYER_LOGIN_CB.on_error)) {
					global.GMI_PLAYER_LOGIN_CB.on_error({ status: 0, error: "No session token" });
				}
				return;
			}
			
			global.GMI_PLAYER_SESSION = _session;
			global.GMI_PLAYER_LOGGING_IN = false;
			global.gmi_player_poll_count = 0;
			
			var loginUrl = global.GMI_ENDPOINT_HOST + "/../../player-auth/discord/login.php?session=" + _session;
			if (global.GMI_LOGS) show_debug_message("[GMI Player] Opening Discord login: " + loginUrl);
			url_open(loginUrl);
			
			// Start internal polling
			__gmi_player_poll_login();
		},
		on_error: function(_data) {
			if (global.GMI_LOGS) show_debug_message("[GMI Player] Failed to start login: " + string(_data));
			if (!is_undefined(global.GMI_PLAYER_LOGIN_CB.on_error)) {
				global.GMI_PLAYER_LOGIN_CB.on_error(_data);
			}
		}
	};
}

/// @func gmi_player_save_token()
/// @desc Save the current token and username to an encrypted local file
function gmi_player_save_token() {
	if (!global.GMI_PLAYER_LOGGED || is_undefined(global.GMI_PLAYER_TOKEN)) return;
	
	var _map = ds_map_create();
	ds_map_add(_map, "token", global.GMI_PLAYER_TOKEN);
	ds_map_add(_map, "username", global.GMI_PLAYER_USERNAME);
	ds_map_add(_map, "user_id", global.GMI_PLAYER_ID);
	
	ds_map_secure_save(_map, "gmi_player.dat");
	ds_map_destroy(_map);
	
	if (global.GMI_LOGS) show_debug_message("[GMI] Token saved locally.");
}

/// @func gmi_player_restore_token()
/// @desc Load a saved token from disk and validate it with the server
function gmi_player_restore_token() {
	if (!file_exists("gmi_player.dat")) return false;
	
	var _map = ds_map_secure_load("gmi_player.dat");
	if (_map == noone || !ds_map_exists(_map, "token")) {
		if (_map != noone) ds_map_destroy(_map);
		file_delete("gmi_player.dat");
		return false;
	}
	
	var _savedToken = ds_map_find_value(_map, "token");
	var _savedUsername = ds_map_exists(_map, "username") ? ds_map_find_value(_map, "username") : "";
	var _savedUserId = ds_map_exists(_map, "user_id") ? ds_map_find_value(_map, "user_id") : undefined;
	ds_map_destroy(_map);
	
	// Optimistically set logged in, then validate with server
	global.GMI_PLAYER_LOGGED = true;
	global.GMI_PLAYER_TOKEN = _savedToken;
	global.GMI_PLAYER_USERNAME = _savedUsername;
	global.GMI_PLAYER_ID = _savedUserId;
	global.gmi_player_check_pending = true;
	
	if (global.GMI_LOGS) show_debug_message("[GMI Player] Found saved token for '" + _savedUsername + "', verifying with server...");
	
	var url = global.GMI_ENDPOINT_HOST + "/../../player-auth/check-token.php?token=" + string_replace_all(string_replace_all(string_replace_all(_savedToken, "+", "%2B"), "/", "%2F"), "=", "%3D") + "&game=" + string(global.GMI_GAME_CLIENT_ID);
	var _req_id = http_get(url);
	global.gmi_requests[$ string(_req_id)] = { on_success: undefined, on_error: undefined };
	
	return true;
}

/// @func gmi_player_clear_saved_token()
/// @desc Delete the saved token file
function gmi_player_clear_saved_token() {
	if (file_exists("gmi_player.dat")) {
		file_delete("gmi_player.dat");
		if (global.GMI_LOGS) show_debug_message("[GMI] Saved token deleted.");
	}
}
