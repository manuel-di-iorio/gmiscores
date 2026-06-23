/// @func __gmi_load_env()
/// @desc Load .env file from the game working directory if present
/// @internal
function __gmi_load_env() {
	global.gmi_env_game_id = undefined;
	global.gmi_env_leaderboard_id = undefined;
	global.gmi_env_game_secret = undefined;
	global.gmi_env = undefined;
	
	if (!file_exists(".env")) return;
	
	var _file = file_text_open_read(".env");
	
	while (!file_text_eof(_file)) {
		var _line = string_trim(file_text_read_string(_file));
		file_text_readln(_file);
		
		if (string_length(_line) == 0 || string_char_at(_line, 1) == "#") continue;
		
		var _eq = string_pos("=", _line);
		if (_eq <= 0) continue;
		
		var _key = string_trim(string_copy(_line, 1, _eq - 1));
		var _val = string_trim(string_copy(_line, _eq + 1, string_length(_line)));
		
		switch (_key) {
			case "GMI_GAME_ID":
				global.gmi_env_game_id = real(_val);
				break;
			case "GMI_LEADERBOARD_ID":
				global.gmi_env_leaderboard_id = real(_val);
				break;
			case "GMI_GAME_SECRET":
				global.gmi_env_game_secret = _val;
				break;
			case "GMI_ENV":
				global.gmi_env = _val;
				break;
		}
	}
	
	file_text_close(_file);
	
	show_debug_message("[GMI] .env loaded — game_id=" + string(global.gmi_env_game_id) + " env=" + string(global.gmi_env));
}
