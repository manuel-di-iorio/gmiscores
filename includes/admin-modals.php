<?php
if (basename($_SERVER["REQUEST_URI"]) === 'admin.php' || strpos($_SERVER["REQUEST_URI"], '/admin.php') === 0) {
  echo ui_modal('modal-admin-user-toggle', [
    'title' => __('admin_confirm_title'),
    'content' => '<p id="modal-admin-user-toggle__body"></p>',
    'footer' =>
      ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-user-toggle', onAdminUserToggleClose)"]]) .
      ui_button(__('admin_confirm_confirm'), 'primary', 'md', ['icon' => 'fas fa-check', 'attrs' => ['onclick' => 'adminUserToggleConfirm()'], 'class' => 'ui-destructive']),
  ]);

  echo ui_modal('modal-admin-player-ban', [
    'title' => __('admin_confirm_title'),
    'content' => '<p id="modal-admin-player-ban__body"></p>',
    'footer' =>
      ui_button(__('admin_confirm_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-player-ban', onAdminPlayerBanClose)"]]) .
      ui_button(__('admin_confirm_confirm'), 'danger', 'md', ['icon' => 'fas fa-ban', 'attrs' => ['onclick' => 'adminPlayerBanConfirm()'], 'class' => 'ui-destructive']),
  ]);

  echo ui_modal('modal-admin-score-delete', [
    'title' => __('scores_modal_delete_title'),
    'content' => '<p id="modal-admin-score-delete__body"></p><p>' . __('scores_modal_delete_irreversible') . '</p>',
    'footer' =>
      ui_button(__('scores_modal_delete_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-score-delete', onAdminScoreDeleteClose)"]]) .
      ui_button(__('scores_modal_delete_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'adminScoreDeleteConfirm()'], 'class' => 'ui-destructive']),
    'footer_right' => true,
  ]);

  echo ui_modal('modal-admin-score-ban', [
    'title' => __('scores_modal_ban_title'),
    'content' => '<p id="modal-admin-score-ban__body"></p><p>' . __('scores_modal_ban_body2') . '</p><p>' . __('scores_modal_ban_body3') . '</p>',
    'footer' =>
      ui_button(__('scores_modal_ban_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-admin-score-ban', onAdminScoreBanClose)"]]) .
      ui_button(__('scores_modal_ban_confirm'), 'danger', 'md', ['icon' => 'fas fa-ban', 'attrs' => ['onclick' => 'adminScoreBanConfirm()'], 'class' => 'ui-destructive']),
    'footer_right' => true,
  ]);

  echo '<script>
    var _t = {
      scores_modal_delete_body: ' . json_encode(__('scores_modal_delete_body')) . ',
      scores_modal_ban_body1: ' . json_encode(__('scores_modal_ban_body1')) . ',
      admin_col_banned: ' . json_encode(__('admin_col_banned')) . ',
      admin_ban: ' . json_encode(__('admin_ban')) . ',
      admin_unban: ' . json_encode(__('admin_unban')) . ',
      admin_ban_infinitive: ' . json_encode(__('admin_ban_infinitive')) . ',
      admin_unban_infinitive: ' . json_encode(__('admin_unban_infinitive')) . ',
      admin_enable_infinitive: ' . json_encode(__('admin_enable_infinitive')) . ',
      admin_disable_infinitive: ' . json_encode(__('admin_disable_infinitive')) . ',
      admin_confirm_player_ban_body: ' . json_encode(__('admin_confirm_player_ban_body', ['action' => '__ACTION__', 'player' => '__PLAYER__', 'game' => '__GAME__'])) . ',
      admin_confirm_user_toggle_body: ' . json_encode(__('admin_confirm_user_toggle_body', ['action' => '__ACTION__', 'user' => '__USER__'])) . '
    };
  </script>';
}
?>
