<style>
.team-settings-card {
  max-width: 500px;
}
</style>

<?php
$activeTab = $_GET["tab"] ?? "config";

$settingsContent = '';
if ($activeTab === 'config') {
  ob_start();
  require "pages/team/team-tab-render.php";
  $settingsContent = ob_get_clean();
}

$membersContent = '';
if ($activeTab === 'members') {
  ob_start();
  require "pages/team/team-tab-render.php";
  $membersContent = ob_get_clean();
}

$gamesContent = '';
if ($activeTab === 'games') {
  ob_start();
  require "pages/team/team-tab-render.php";
  $gamesContent = ob_get_clean();
}

echo ui_tabs([
  ["id" => "config", "label" => __('team_tab_config'), "icon" => "fas fa-cog", "content" => $activeTab === 'config' ? $settingsContent : ui_skeleton('table-row', 3), "url" => $activeTab !== 'config' ? "/team.php?id=$teamId&tab=config&ajax=1" : null],
  ["id" => "members", "label" => __('team_tab_members'), "icon" => "fas fa-users", "content" => $activeTab === 'members' ? $membersContent : ui_skeleton('table-row', 5), "url" => $activeTab !== 'members' ? "/team.php?id=$teamId&tab=members&ajax=1" : null],
  ["id" => "games", "label" => __('team_tab_games'), "icon" => "fas fa-gamepad", "content" => $activeTab === 'games' ? $gamesContent : ui_skeleton('table-row', 5), "url" => $activeTab !== 'games' ? "/team.php?id=$teamId&tab=games&ajax=1" : null],
], ["active" => $activeTab]);
?>

<?php if ($isTeamAdmin) { ?>
<?= ui_modal('modal-delete-team', [
  'title' => __('team_settings_delete_title'),
  'content' => '<p>' . __('team_settings_delete_body') . ' <strong>' . htmlspecialchars($team['name']) . '</strong> ?</p><p>' . __('team_settings_delete_warning') . '</p>',
  'footer' =>
    ui_button(__('team_settings_delete_cancel'), 'secondary', 'md', ['attrs' => ['onclick' => "closeModal('modal-delete-team')"]]) .
    ui_button(__('team_settings_delete_confirm'), 'danger', 'md', ['icon' => 'fas fa-trash', 'attrs' => ['onclick' => 'deleteTeam()'], 'class' => 'ui-destructive']),
  'footer_right' => true,
]) ?>
<?php } ?>

<script>
function deleteTeam() {
  fetch('/team-settings.php?id=<?= $teamId ?>', {
    method: 'DELETE',
    headers: { 'X-CSRF-Token': '<?= csrf_token() ?>' }
  }).then(function(r) {
    if (r.redirected) { location.href = r.url; } else { location.href = 'teams.php'; }
  });
}
</script>
