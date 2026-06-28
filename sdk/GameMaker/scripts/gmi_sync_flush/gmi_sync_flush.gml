/// @func gmi_sync_get_backoff(attempts)
/// @desc Calculate backoff delay in seconds based on attempt count.
/// Schedule: 30s, 1min, 2min, 4min, 8min, 16min, 32min, 1h, then 1h each
/// Total: ~5.8 hours across 15 attempts
/// @arg {real} attempts Current attempt number (0-based)
/// @return {real} Delay in seconds with jitter
function gmi_sync_get_backoff(attempts) {
	static BASE_DELAYS = [
		0,      30,     60,     120,    240,
		480,    960,    1920,   3600,   3600,
		3600,   3600,   3600,   3600,   3600
	];
	var _idx = min(attempts, array_length(BASE_DELAYS) - 1);
	var _base = BASE_DELAYS[_idx];
	var _jitter = _base * 0.2 * (random(1) * 2 - 1);
	return max(0, _base + _jitter);
}

/// @func gmi_sync_flush([opts])
/// @desc Send queued operations to the server in a batch.
/// @arg {struct} [opts] Optional callbacks: { on_success, on_error }
function gmi_sync_flush(opts = {}) {
	if (global.gmi_sync_flush_pending) return;
	if (array_length(global.gmi_sync_queue) == 0) return;
	
	global.gmi_sync_flush_pending = true;
	
	var _now = current_time / 1000;
	var _ready = [];
	var _maxBatch = global.gmi_sync_max_batch;
	
	for (var i = 0; i < array_length(global.gmi_sync_queue); i++) {
		var _op = global.gmi_sync_queue[i];
		if (_op.next_retry <= _now) {
			array_push(_ready, _op);
			if (array_length(_ready) >= _maxBatch) break;
		}
	}
	
	if (array_length(_ready) == 0) {
		global.gmi_sync_flush_pending = false;
		return;
	}
	
	var _body = { operations: _ready };
	var _json = json_stringify(_body);
	
	if (global.GMI_LOGS) show_debug_message("[GMI] SyncFlush → POST /sync.php: " + string(array_length(_ready)) + " operations");
	
	var _req_id = http_post_string(global.GMI_ENDPOINT_SYNC, _json);
	global.gmi_requests[$ string(_req_id)] = {
		type: "sync",
		on_success: variable_struct_exists(opts, "on_success") ? opts.on_success : undefined,
		on_error: variable_struct_exists(opts, "on_error") ? opts.on_error : undefined
	};
}
