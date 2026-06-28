/// @func __gmi_sync_load()
/// @desc Load persisted sync queue from disk. Internal use only.
function __gmi_sync_load() {
	global.gmi_sync_queue = [];
	
	var _map = ds_map_secure_load("gmi_sync.dat");
	if (_map == -1) return;
	
	var _json = ds_map_find_value(_map, "queue");
	ds_map_destroy(_map);
	
	if (is_undefined(_json) || _json == "") return;
	
	var _parsed = json_parse(_json);
	if (is_array(_parsed)) {
		global.gmi_sync_queue = _parsed;
		if (global.GMI_LOGS) show_debug_message("[GMI] Sync: loaded " + string(array_length(global.gmi_sync_queue)) + " operations from disk");
	}
}
