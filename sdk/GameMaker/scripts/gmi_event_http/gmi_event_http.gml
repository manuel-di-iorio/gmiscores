/// @func gmi_event_http()
/// @desc Handle all HTTP async events. Call in Async HTTP event.
/// @example // Async HTTP: gmi_event_http();
function gmi_event_http() {
	var _req_id = async_load[? "id"];
	var _status = async_load[? "status"];
	var _http_status = async_load[? "http_status"];
	var _result = async_load[? "result"];
	var _key = string(_req_id);
	
	if (!variable_struct_exists(global.gmi_requests, _key)) return;
	
	var _req = global.gmi_requests[$ _key];
	variable_struct_remove(global.gmi_requests, _key);
	
	// Ignore download-in-progress callbacks
	if (_status == 1) return;
	
	// Network error: connection failed (no http_status or no result)
	var _is_network_error = (_status < 0) || (is_undefined(_http_status) || is_undefined(_result));
	
	if (_is_network_error || string_length(_result) < 2 || string_char_at(_result, 1) != "{") {
		if (global.GMI_LOGS) show_debug_message("[GMI] Request failed (status=" + string(_status) + ", http=" + string(_http_status) + "): " + string(_result));
		
		// Sync request: reset pending flag
		if (variable_struct_exists(_req, "type") && _req.type == "sync") {
			global.gmi_sync_flush_pending = false;
		}
		
		// Token restore failed
		if (global.gmi_player_check_pending) {
			global.gmi_player_check_pending = false;
			if (global.GMI_LOGS) show_debug_message("[GMI] Token restore failed");
		}
		
		// Retry logic for network failures
		if (variable_struct_exists(_req, "max_retries") && variable_struct_exists(_req, "attempts")) {
			if (_req.attempts < _req.max_retries) {
				_req.attempts += 1;
				if (global.GMI_LOGS) show_debug_message("[GMI] Retry " + string(_req.attempts) + "/" + string(_req.max_retries));
				var _new_id;
				if (_req.method == "POST") {
					_new_id = http_post_string(_req.url, _req.body);
				} else {
					_new_id = http_get(_req.url);
				}
				global.gmi_requests[$ string(_new_id)] = _req;
				return;
			}
		}
		
		// Fallback to sync queue for write operations
		if (variable_struct_exists(_req, "fallback_payload") && variable_struct_exists(_req, "fallback_type")) {
			var _op = {
				op_id: uuid_generate(),
				type: _req.fallback_type,
				payload: _req.fallback_payload,
				attempts: 0,
				next_retry: 0
			};
			array_push(global.gmi_sync_queue, _op);
			__gmi_sync_persist();
			if (global.GMI_LOGS) show_debug_message("[GMI] Sync: enqueued " + _req.fallback_type + " (total: " + string(array_length(global.gmi_sync_queue)) + ")");
		}
		
		if (!is_undefined(_req.on_error)) _req.on_error({ status: 0 });
		return;
	}
	
	var _data = json_parse(_result);
	var _ok = (_http_status == 200 && !is_undefined(_data) && variable_struct_exists(_data, "status") && _data.status == 200);
	
	// Handle sync batch response
	if (_ok && variable_struct_exists(_req, "type") && _req.type == "sync") {
		global.gmi_sync_flush_pending = false;
		
		if (variable_struct_exists(_data, "results") && is_array(_data.results)) {
			var _results = _data.results;
			var _queue = global.gmi_sync_queue;
			var _changed = false;
			
			for (var r = 0; r < array_length(_results); r++) {
				var _res = _results[r];
				var _resOpId = _res.op_id;
				var _resStatus = _res.status;
				
				for (var q = array_length(_queue) - 1; q >= 0; q--) {
					if (_queue[q].op_id == _resOpId) {
						if (_resStatus == "applied" || _resStatus == "duplicate") {
							array_delete(_queue, q, 1);
						} else if (_resStatus == "failed") {
							_queue[q].attempts += 1;
							if (_queue[q].attempts >= global.gmi_sync_max_attempts) {
								array_delete(_queue, q, 1);
								if (global.GMI_LOGS) show_debug_message("[GMI] Sync: dropped op " + _resOpId + " after max attempts");
							} else {
								var _delay = gmi_sync_get_backoff(_queue[q].attempts);
								_queue[q].next_retry = (current_time / 1000) + _delay;
								if (global.GMI_LOGS) show_debug_message("[GMI] Sync: op " + _resOpId + " failed, retry in " + string(_delay) + "s");
							}
						}
						_changed = true;
						break;
					}
				}
			}
			
			if (_changed) __gmi_sync_persist();
		}
		
		if (!is_undefined(_req.on_success)) _req.on_success(_data);
		return;
	}
	
	// Handle token check response (from restore_token)
	if (_ok && global.gmi_player_check_pending && variable_struct_exists(_data, "valid")) {
		global.gmi_player_check_pending = false;
		var _approved = variable_struct_exists(_data, "approved") ? _data.approved : false;
		if (_data.valid && _approved) {
			global.GMI_PLAYER_LOGGED = true;
			global.GMI_PLAYER_TOKEN = _data.token;
			global.GMI_PLAYER_USERNAME = _data.username;
			global.GMI_PLAYER_ID = variable_struct_exists(_data, "user_id") ? _data.user_id : undefined;
			if (global.GMI_LOGS) show_debug_message("[GMI] Logged in as " + _data.username + " (restored)");
		} else {
			gmi_player_clear_saved_token();
			global.GMI_PLAYER_LOGGED = false;
			global.GMI_PLAYER_TOKEN = undefined;
			global.GMI_PLAYER_USERNAME = undefined;
			global.GMI_PLAYER_ID = undefined;
			if (global.GMI_LOGS) {
				if (!_data.valid) {
					show_debug_message("[GMI] Saved token invalid, please log in again.");
				} else {
					show_debug_message("[GMI] Account not approved, please wait for approval.");
				}
			}
		}
		return;
	}
	
	if (_ok) {
		if (global.GMI_LOGS) show_debug_message("[GMI] OK #" + string(_req_id) + ": " + _result);
		
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
				if (global.GMI_LOGS) show_debug_message("[GMI] Player logged in as " + global.GMI_PLAYER_USERNAME + "!");
				gmi_player_save_token();
				// Fire login callback
				if (!is_undefined(global.GMI_PLAYER_LOGIN_CB) && !is_undefined(global.GMI_PLAYER_LOGIN_CB.on_success)) {
					global.GMI_PLAYER_LOGIN_CB.on_success({ username: global.GMI_PLAYER_USERNAME, user_id: global.GMI_PLAYER_ID });
				}
			} else {
				global.GMI_PLAYER_LOGGING_IN = false;
				// Schedule next poll in configured seconds
				call_later(global.gmi_player_poll_delay, time_source_units_seconds, __gmi_player_poll_login);
			}
		}
		
		// Fire user callback
		if (!is_undefined(_req.on_success)) _req.on_success(_data);
	} else {
		if (global.GMI_LOGS) show_debug_message("[GMI] ERR #" + string(_http_status) + " " + string(_req_id) + ": " + _result);
		if (variable_struct_exists(_data, "logged")) {
			global.GMI_PLAYER_LOGGING_IN = false;
		}
		if (!is_undefined(_req.on_error)) _req.on_error(_data);
	}
}
