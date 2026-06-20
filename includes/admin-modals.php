<?php
if (basename($_SERVER["REQUEST_URI"]) === 'admin.php' || strpos($_SERVER["REQUEST_URI"], '/admin.php') === 0) {
  echo ui_modal('modal-admin-user-toggle', [
    'title' => 'Confirm action',
    'content' => '<p id="modal-admin-user-toggle__body"></p>',
    'footer' =>
      ui_button('Cancel', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-user-toggle', onAdminUserToggleClose)"]]) .
      ui_button('Confirm', 'primary', 'md', ['icon' => 'fas fa-check', 'attrs' => ['onclick' => 'adminUserToggleConfirm()'], 'class' => 'ui-destructive']),
  ]);

  echo ui_modal('modal-admin-player-ban', [
    'title' => 'Confirm action',
    'content' => '<p id="modal-admin-player-ban__body"></p>',
    'footer' =>
      ui_button('Cancel', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-player-ban', onAdminPlayerBanClose)"]]) .
      ui_button('Confirm', 'danger', 'md', ['icon' => 'fas fa-ban', 'attrs' => ['onclick' => 'adminPlayerBanConfirm()'], 'class' => 'ui-destructive']),
  ]);

  echo ui_modal('modal-admin-score-delete', [
    'title' => 'Confirm deletion',
    'content' => '<p id="modal-admin-score-delete__body"></p><p>This operation cannot be undone.</p>',
    'footer' =>
      ui_button('Cancel', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-score-delete', onAdminScoreDeleteClose)"]]) .
      ui_button('Delete score', 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'adminScoreDeleteConfirm()'], 'class' => 'ui-destructive']),
    'footer_right' => true,
  ]);

  echo ui_modal('modal-admin-score-ban', [
    'title' => 'Confirm ban',
    'content' => '<p id="modal-admin-score-ban__body"></p><p>All their scores submitted on this game will be removed and they will not be able to submit new ones.</p><p>You can remove the ban later but the removed scores cannot be recovered.</p>',
    'footer' =>
      ui_button('Cancel', 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-score-ban', onAdminScoreBanClose)"]]) .
      ui_button('Ban player', 'danger', 'md', ['icon' => 'fas fa-ban', 'attrs' => ['onclick' => 'adminScoreBanConfirm()'], 'class' => 'ui-destructive']),
    'footer_right' => true,
  ]);

  echo '<script>
    var _t = {
      scores_modal_delete_body: "Are you sure you want to delete the score of",
      scores_modal_ban_body1: "Do you want to ban",
      admin_col_banned: "Banned",
      admin_ban: "Ban",
      admin_unban: "Unban",
      admin_ban_infinitive: "ban",
      admin_unban_infinitive: "unban",
      admin_enable_infinitive: "enable",
      admin_disable_infinitive: "disable",
      admin_confirm_player_ban_body: "Are you sure you want to {action} player \"{player}\" from game \"{game}\"?",
      admin_confirm_user_toggle_body: "Are you sure you want to {action} user \"{user}\"?"
    };
  </script>';
}
?>
