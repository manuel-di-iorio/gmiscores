/// @func gmi_draw_debug(x, y)
/// @desc Draw a debug overlay with login status and leaderboard. Call in Draw event.
/// @arg {real} x X position (default 20)
/// @arg {real} y Y position (default 20)
function gmi_draw_debug(_x = 20, _y = 20) {
	var _font = draw_get_font();
	var _halign = draw_get_halign();
	var _valign = draw_get_valign();
	draw_set_font(-1);
	draw_set_halign(fa_left);
	draw_set_valign(fa_top);

	var _lineHeight = 24;
	var _cury = _y;

	// Login status
	if (global.GMI_PLAYER_LOGGED) {
		draw_set_color(#10b981);
		draw_text(_x, _cury, "● Logged in as " + global.GMI_PLAYER_USERNAME);
	} else if (!is_undefined(global.GMI_PLAYER_SESSION)) {
		draw_set_color(#f59e0b);
		draw_text(_x, _cury, "● Waiting for login...");
	} else {
		draw_set_color(#6b7280);
		draw_text(_x, _cury, "● Guest mode");
	}
	_cury += _lineHeight + 8;

	// Leaderboard
	draw_set_color(c_white);
	draw_text(_x, _cury, "── Leaderboard ──");
	_cury += _lineHeight;

	if (global.gmi_scores_list == noone) {
		draw_set_color(#6b7280);
		draw_text(_x, _cury, "No data loaded");
	} else if (array_length(global.gmi_scores_list) == 0) {
		draw_set_color(#6b7280);
		draw_text(_x, _cury, "No scores yet");
	} else {
		var _max = min(array_length(global.gmi_scores_list), 10);
		for (var i = 0; i < _max; i++) {
			var _s = global.gmi_scores_list[i];
			draw_set_color(#6b7280);
			draw_text(_x, _cury, "#" + string(i + 1));
			draw_set_color(c_white);
			draw_text(_x + 40, _cury, _s.username);
			draw_set_color(#60a5fa);
			draw_text(_x + 200, _cury, string(_s.score));
			_cury += _lineHeight;
		}
	}

	// Reset
	draw_set_color(c_white);
	draw_set_font(_font);
	draw_set_halign(_halign);
	draw_set_valign(_valign);
}
