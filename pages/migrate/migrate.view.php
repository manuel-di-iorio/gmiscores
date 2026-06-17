<div class="internal-page">
  <div class="info-panel" style="border-left-color:#3b82f6">
    <p><i class="fas fa-info-circle" style="margin-right:8px"></i> <?= __('migrate_user_label') ?> <strong><?= htmlspecialchars($user['username']) ?></strong></p>
  </div>

  <?php if ($run && !empty($output)) { ?>
    <div class="internal-card" style="border-left:4px solid #22c55e">
      <div class="internal-card__title"><i class="fas fa-check-circle" style="color:#22c55e"></i> <?= __('migrate_result_title') ?></div>
      <?php foreach ($output as $line) {
        $cls = strpos($line, 'ERROR') === 0 ? 'text-red' : (strpos($line, 'FAIL') === 0 ? 'text-red' : (strpos($line, 'SKIP') === 0 ? 'text-gray' : 'text-green'));
        echo '<div class="' . $cls . '">' . htmlspecialchars($line) . '</div>';
      } ?>
    </div>
  <?php } ?>

  <div class="internal-card">
    <div class="internal-card__title"><i class="fas fa-database"></i> <?= __('migrate_migrations_title') ?></div>
      <table style="width:100%;border-collapse:collapse;border:1px solid var(--border-color,#ccc)">
        <thead>
          <tr>
            <th><?= __('migrate_col_file') ?></th>
            <th><?= __('migrate_col_description') ?></th>
            <th><?= __('migrate_col_status') ?></th>
            <th><?= __('migrate_col_date') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($migrations as $m) { ?>
            <tr>
              <td><code><?= htmlspecialchars($m['name']) ?></code></td>
              <td><?= htmlspecialchars($m['description']) ?></td>
              <td>
                <?php if ($m['is_applied']) { ?>
                  <span class="tag tag-green"><?= __('migrate_status_applied') ?></span>
                <?php } else { ?>
                  <span class="tag tag-orange"><?= __('migrate_status_pending') ?></span>
                <?php } ?>
              </td>
              <td><?= $m['is_applied'] ? htmlspecialchars($applied[$m['name']]) : '-' ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php
    $pendingCount = 0;
    foreach ($migrations as $m) { if (!$m['is_applied']) $pendingCount++; }
  ?>
  <?php if ($pendingCount > 0) { ?>
    <form method="POST" onsubmit="return confirm('<?= __('migrate_button', ['count' => $pendingCount]) ?>')">
      <?= ui_button(__('migrate_button', ['count' => $pendingCount]), 'primary', 'md', ['icon' => 'fas fa-play', 'type' => 'submit', 'class' => 'mt-2']) ?>
    </form>
  <?php } else { ?>
    <div class="info-panel" style="border-left-color:#22c55e">
      <p><i class="fas fa-check-circle" style="margin-right:8px"></i> <?= __('migrate_all_applied') ?></p>
    </div>
  <?php } ?>
</div>
