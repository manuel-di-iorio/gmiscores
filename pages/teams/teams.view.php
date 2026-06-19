<div class="internal-page">
  <?php if (!empty($teams)) { ?>
    <div class="internal-actions internal-actions--right">
      <?= ui_button(__('teams_create_button'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-team.php']) ?>
    </div>
    <div class="ui-table-container">
      <table class="ui-table">
        <thead class="ui-table-header">
          <tr>
            <th class="ui-table-header-cell"><?= __('teams_col_name') ?></th>
            <th class="ui-table-header-cell"><?= __('teams_col_role') ?></th>
            <th class="ui-table-header-cell"><?= __('teams_col_members') ?></th>
            <th class="ui-table-header-cell"><?= __('table_actions') ?></th>
          </tr>
        </thead>
        <tbody class="ui-table-body">
          <?php foreach ($teams as $t) { ?>
            <tr class="ui-table-row">
              <td class="ui-table-cell">
                <a href="team.php?id=<?= $t['team_id'] ?>" class="link" data-tippy-content="<?= __('teams_action_view') ?>">
                  <?= htmlspecialchars($t['name']) ?>
                </a>
              </td>
              <td class="ui-table-cell">
                <?php if ($t['role'] === 'admin') { ?>
                  <?= ui_badge(__('team_members_role_admin'), 'primary', ['icon' => 'fas fa-crown']) ?>
                <?php } else { ?>
                  <?= ui_badge(__('team_members_role_member'), 'default') ?>
                <?php } ?>
              </td>
              <td class="ui-table-cell"><?= (int)$t['member_count'] ?></td>
              <td class="ui-table-cell actions-cell">
                <a href="team.php?id=<?= $t['team_id'] ?>" class="admin-score-action" data-tippy-content="<?= __('teams_action_view') ?>">
                  <i class="fas fa-cog"></i>
                </a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  <?php } else { ?>
    <div class="internal-empty">
      <i class="fas fa-users" style="font-size:2em;margin-bottom:8px"></i>
      <h4><?= __('teams_empty_title') ?></h4>
      <p><?= __('teams_empty_desc') ?></p>
      <div style="display:flex;justify-content:center">
        <?= ui_button(__('teams_empty_btn'), 'primary', 'md', ['icon' => 'fas fa-plus-circle', 'href' => 'add-team.php', 'class' => 'internal-empty-btn']) ?>
      </div>
    </div>
  <?php } ?>
</div>
