/// @func gmi_init(game_id, game_secret)
/// @desc Initialize the GMI Cloud Services client
/// @arg {real} game_id
/// @arg {string} game_secret
function gmi_init(clientId, clientSecret, env = "production") {
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
	global.GMI_PLAYER_LOGGING_IN = false;
	
	// Poll state
	global.gmi_player_poll_timer = 0;
	global.gmi_player_poll_count = 0;
	global.gmi_player_poll_interval = 300; // 5 sec at 60fps
	global.gmi_player_poll_max = 36; // 36 * 5s = 3 min
	
	// Scores state
	global.gmi_scores_list = noone;
	global.gmi_scores_player_score = noone;
	
	// Request registry: maps string(request_id) -> { on_success, on_error }
	global.gmi_requests = {};
}
