// SPACE - Send a random score
gmi_scores_send({ 
    leaderboard_id: 30,
    score: irandom_range(1, 9999), 
    player: "Harry",
    on_success: function(_data) {
        gmi_scores_get_list({ leaderboard_id: 30 });
    }
});