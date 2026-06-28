/// @func gmi_init([game_id, game_secret, env])
/// @desc Initialize the GMI Cloud Services client. All parameters are optional if a .env file is present.
/// @arg {real} [game_id]
/// @arg {string} [game_secret]
/// @arg {string} [env]
function gmi_init(clientId = undefined, clientSecret = undefined, env = undefined) {
	// Debug logs (set to false to disable all debug output)
	global.GMI_LOGS = true;
	
	// Load .env file if present (provides defaults for all parameters, for internal debug)
	__gmi_load_env();
	
	if (!is_undefined(global.gmi_env_game_id)) {
		clientId = global.gmi_env_game_id;
	}
	if (!is_undefined(global.gmi_env_game_secret)) {
		clientSecret = global.gmi_env_game_secret;
	}
	if (!is_undefined(global.gmi_env)) {
		env = global.gmi_env;
	}
	if (is_undefined(env)) env = "production";
	
	if (env == "production") {
	   global.GMI_ENDPOINT_HOST = "https://gmiscores.altervista.org/api/v1";
    } else {
        global.GMI_ENDPOINT_HOST = "http://localhost:8080/api/v1";
    }
	
	global.GMI_GAME_CLIENT_ID = clientId;
	global.GMI_GAME_CLIENT_SECRET = clientSecret;
	
	// Player login state
	global.GMI_PLAYER_SESSION = undefined;
	global.GMI_PLAYER_LOGGED = false;
	global.GMI_PLAYER_TOKEN = undefined;
	global.GMI_PLAYER_USERNAME = undefined;
	global.GMI_PLAYER_ID = undefined;
	global.GMI_PLAYER_LOGGING_IN = false;
	
	// Poll state
	global.gmi_player_poll_count = 0;
	global.gmi_player_poll_max = 60; // 60 * 3s = 3 min
	global.gmi_player_poll_delay = 3; // seconds between polls
	
	// Token check state (for startup restore)
	global.gmi_player_check_pending = false;
	
	// Scores state
	global.gmi_scores_list = noone;
	global.gmi_scores_player_score = noone;
	
	// Request registry: maps string(request_id) -> { on_success, on_error }
	global.gmi_requests = {};
	
	// HTTP retry state
	global.gmi_http_max_retries = 3;
	
	// Offline sync state
	global.gmi_sync_queue = [];
	global.gmi_sync_flush_pending = false;
	global.gmi_sync_flush_delay = 15;
	global.gmi_sync_max_attempts = 15;
	global.gmi_sync_max_batch = 20;
	global.GMI_ENDPOINT_SYNC = global.GMI_ENDPOINT_HOST + "/sync.php";
	
	// Try to restore saved token from disk
	gmi_player_restore_token();
	
	// Load persisted sync queue and flush if non-empty
	__gmi_sync_load();
	if (array_length(global.gmi_sync_queue) > 0) {
		gmi_sync_flush();
	}
	
	// Periodic flush
	call_later(global.gmi_sync_flush_delay, time_source_units_seconds, gmi_sync_flush, true);
}
