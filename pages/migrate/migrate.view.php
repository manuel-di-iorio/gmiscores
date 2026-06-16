<div class="internal-page">
  <div class="info-panel" style="border-left-color:#3b82f6">
    <p><i class="fas fa-info-circle w3-margin-right"></i> Utente: <strong><?= htmlspecialchars($user['username']) ?></strong></p>
  </div>

  <?php if ($run && !empty($output)) { ?>
    <div class="internal-card" style="border-left:4px solid #22c55e">
      <div class="internal-card__title"><i class="fas fa-check-circle" style="color:#22c55e"></i> Risultato</div>
      <?php foreach ($output as $line) {
        $cls = strpos($line, 'ERROR') === 0 ? 'w3-text-red' : (strpos($line, 'FAIL') === 0 ? 'w3-text-red' : (strpos($line, 'SKIP') === 0 ? 'w3-text-gray' : 'w3-text-green'));
        echo '<div class="' . $cls . '">' . htmlspecialchars($line) . '</div>';
      } ?>
    </div>
  <?php } ?>

  <div class="internal-card">
    <div class="internal-card__title"><i class="fas fa-database"></i> Migrazioni</div>
      <table class="w3-table w3-striped w3-bordered">
        <thead>
          <tr>
            <th>File</th>
            <th>Descrizione</th>
            <th>Stato</th>
            <th>Data</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($migrations as $m) { ?>
            <tr>
              <td><code><?= htmlspecialchars($m['name']) ?></code></td>
              <td><?= htmlspecialchars($m['description']) ?></td>
              <td>
                <?php if ($m['is_applied']) { ?>
                  <span class="w3-tag w3-green w3-round">Applicata</span>
                <?php } else { ?>
                  <span class="w3-tag w3-orange w3-round">In attesa</span>
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
    <form method="POST" onsubmit="return confirm('Eseguire ' + <?= $pendingCount ?> + ' migrazioni pendenti?')">
      <?= ui_button('Esegui migrazioni pendenti (' . $pendingCount . ')', 'primary', 'md', ['icon' => 'fas fa-play', 'type' => 'submit', 'class' => 'mt-2']) ?>
    </form>
  <?php } else { ?>
    <div class="info-panel" style="border-left-color:#22c55e">
      <p><i class="fas fa-check-circle w3-margin-right"></i> Tutte le migrazioni sono state applicate.</p>
    </div>
  <?php } ?>
</div>
