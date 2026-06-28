/// @func __gmi_sync_enqueue(type, payload)
/// @desc Add an operation to the offline sync queue. Internal use only.
/// @arg {string} type Operation type (e.g. "score.submit")
/// @arg {string} payload The full request body to send later
function __gmi_sync_enqueue(type, payload) {
	var _op = {
		op_id: uuid_generate(),
		type: type,
		payload: payload,
		attempts: 0,
		next_retry: 0
	};
	
	array_push(global.gmi_sync_queue, _op);
	__gmi_sync_persist();
	
	if (global.GMI_LOGS) show_debug_message("[GMI] Sync: enqueued " + type + " (total: " + string(array_length(global.gmi_sync_queue)) + ")");
}
