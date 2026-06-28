/// @func __gmi_sync_persist()
/// @desc Save sync queue to disk. Internal use only.
function __gmi_sync_persist() {
	var _map = ds_map_create();
	var _json = json_stringify(global.gmi_sync_queue);
	ds_map_add(_map, "queue", _json);
	ds_map_secure_save(_map, "gmi_sync.dat");
	ds_map_destroy(_map);
}
