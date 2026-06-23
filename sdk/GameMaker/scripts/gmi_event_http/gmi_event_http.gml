/// @func gmi_event_http()
/// @desc Handle all HTTP async events. Call in Async HTTP event.
/// @example // Async HTTP: gmi_event_http();
function gmi_event_http() {
	var _req_id = async_load[? "id"];
	var _http_status = async_load[? "http_status"];
	var _result = async_load[? "result"];
	var _key = string(_req_id);
	
	if (!variable_struct_exists(global.gmi_requests, _key)) return;
	
	var _req = global.gmi_requests[$ _key];
	variable_struct_remove(global.gmi_requests, _key);
	
	if (string_length(_result) < 2 || string_char_at(_result, 1) != "{") {
		show_debug_message("[GMI] Non-JSON response: " + string_copy(_result, 1, 120));
		if (!is_undefined(_req.on_error)) _req.on_error({ status: 0 });
		return;
	}
	
	var _data = json_parse(_result);
	var _ok = (_http_status == 200 && !is_undefined(_data) && variable_struct_exists(_data, "status") && _data.status == 200);
	
	// Handle token check response (from restore_token) - double confirmation: valid + approved
	if (_ok && global.gmi_player_check_pending && variable_struct_exists(_data, "valid")) {
		global.gmi_player_check_pending = false;
		var _approved = variable_struct_exists(_data, "approved") ? _data.approved : false;
		if (_data.valid && _approved) {
			global.GMI_PLAYER_LOGGED = true;
			global.GMI_PLAYER_TOKEN = _data.token;
			global.GMI_PLAYER_USERNAME = _data.username;
			global.GMI_PLAYER_ID = variable_struct_exists(_data, "user_id") ? _data.user_id : undefined;
			show_debug_message("[GMI] Logged in as " + _data.username + " (restored, double confirmed)");
		} else {
			gmi_player_clear_saved_token();
			global.GMI_PLAYER_LOGGED = false;
			global.GMI_PLAYER_TOKEN = undefined;
			global.GMI_PLAYER_USERNAME = undefined;
			global.GMI_PLAYER_ID = undefined;
			if (!_data.valid) {
				show_debug_message("[GMI] Saved token invalid, please log in again.");
			} else {
				show_debug_message("[GMI] Account not approved, please wait for approval.");
			}
		}
		return;
	}
	
	if (_ok) {
		show_debug_message("[GMI] OK #" + string(_req_id) + ": " + _result);
		
		// Auto-store known response types
		if (variable_struct_exists(_data, "scores")) {
			global.gmi_scores_list = _data.scores;
			global.gmi_scores_player_score = _data.playerScore;
		}
		if (variable_struct_exists(_data, "logged")) {
			if (_data.logged) {
				global.GMI_PLAYER_LOGGED = true;
				global.GMI_PLAYER_TOKEN = _data.token;
				global.GMI_PLAYER_USERNAME = variable_struct_exists(_data, "username") ? _data.username : undefined;
				global.GMI_PLAYER_ID = variable_struct_exists(_data, "user_id") ? _data.user_id : undefined;
				global.GMI_PLAYER_SESSION = undefined;
				global.GMI_PLAYER_LOGGING_IN = false;
				global.gmi_player_poll_count = 0;
				show_debug_message("[GMI] Player logged in as " + global.GMI_PLAYER_USERNAME + "!");
				gmi_player_save_token();
				// Fire login callback
				if (!is_undefined(global.GMI_PLAYER_LOGIN_CB) && !is_undefined(global.GMI_PLAYER_LOGIN_CB.on_success)) {
					global.GMI_PLAYER_LOGIN_CB.on_success({ username: global.GMI_PLAYER_USERNAME, user_id: global.GMI_PLAYER_ID });
				}
			} else {
				global.GMI_PLAYER_LOGGING_IN = false;
				// Schedule next poll
				call_later(global.gmi_player_poll_interval, timeunit_frames, __gmi_player_poll_login);
			}
		}
		
		// Fire user callback
		if (!is_undefined(_req.on_success)) _req.on_success(_data);
	} else {
		show_debug_message("[GMI] ERR #" + string(_http_status) + " " + string(_req_id) + ": " + _result);
		if (variable_struct_exists(_data, "logged")) {
			global.GMI_PLAYER_LOGGING_IN = false;
		}
		if (!is_undefined(_req.on_error)) _req.on_error(_data);
	}
}
