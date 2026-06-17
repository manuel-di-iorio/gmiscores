/// @func gmi_scores_draw_debug()
/// @desc Simple function to quickly debug the highscore
function gmi_scores_draw_debug() {

	if (gmi_scores_list != noone) {
		// Set the temporary drawing options
		var font = draw_get_font();
		var halign = draw_get_halign();
		var valign = draw_get_valign();
		draw_set_font(-1); draw_set_halign(fa_left); draw_set_valign(fa_top);

		// Draw the scores
		for (var i=0; i<array_length(gmi_scores_list); i++) {
			var player = gmi_scores_list[i];
		    draw_text(20, 20+i*20, player.username + " - " + string(player.score));
		}

		// Reset the drawing settings back to the original
		draw_set_font(font); draw_set_halign(halign); draw_set_valign(valign);
	}
}
