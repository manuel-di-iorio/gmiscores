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
	
	var _data = json_parse(_result);
	var _ok = (_http_status == 200 && !is_undefined(_data) && variable_struct_exists(_data, "status") && _data.status == 200);
	
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
				global.GMI_PLAYER_SESSION = undefined;
				global.GMI_PLAYER_LOGGING_IN = false;
				global.gmi_player_poll_timer = 0;
				global.gmi_player_poll_count = 0;
				show_debug_message("[GMI] Player login successful!" + (global.GMI_PLAYER_USERNAME != undefined ? " (" + global.GMI_PLAYER_USERNAME + ")" : ""));
			} else {
				global.GMI_PLAYER_LOGGING_IN = false;
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
