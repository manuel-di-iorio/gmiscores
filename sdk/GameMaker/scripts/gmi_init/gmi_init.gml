/// @func gmi_init(game_id, game_secret)
/// @desc Initialize the GMI Cloud Services client
/// @version 0.1
/// @arg {real} game_id
/// @arg {string} game_secret
function gmi_init(clientId, clientSecret) {
	// API Endpoint
	global.GMI_ENDPOINT_HOST = "https://gmiscores.altervista.org/api/v1";
	
	// Game data
	global.GMI_GAME_CLIENT_ID = clientId;
	global.GMI_GAME_CLIENT_SECRET = clientSecret;
}