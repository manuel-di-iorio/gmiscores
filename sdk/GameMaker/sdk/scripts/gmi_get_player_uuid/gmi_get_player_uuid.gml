/// @func gmi_get_player_uuid()
/// @desc Get the player UUID cached on the filesystem. This is useful when you want to send scores without using usernames
/// @return {string}
function gmi_get_player_uuid() {

	var uuid, f;

	if (!file_exists("gmi_cloud_uuid")) {
		uuid = uuid_generate();
		f = file_text_open_write("gmi_cloud_uuid");
		file_text_write_string(f, uuid);
	} else {
		f = file_text_open_read("gmi_cloud_uuid");
		uuid = file_text_read_string(f);
	}
	file_text_close(f);

	return uuid;
}
