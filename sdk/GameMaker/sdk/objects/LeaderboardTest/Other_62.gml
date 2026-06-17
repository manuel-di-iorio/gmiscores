// Parse the incoming scores data
gmi_scores_on_request("default");

// Get again the updated highscore, once the send score request has been processed
if (gmi_scores_send_req != noone) {
	gmi_scores_get_list();
	gmi_scores_send_req = noone;
}