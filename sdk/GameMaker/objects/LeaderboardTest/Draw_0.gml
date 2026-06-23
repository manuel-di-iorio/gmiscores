// Draw debug overlay with login status and leaderboard
gmi_draw_debug(20, 20);

// Instructions
draw_set_halign(fa_left);
draw_set_valign(fa_bottom);
draw_set_color(c_gray);
draw_text(20, room_height - 20, "L: Login | SPACE: Send Score | R: Refresh");
draw_set_color(c_white);
