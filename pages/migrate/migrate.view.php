<div class="w3-container w3-padding-large">
  <div class="w3-panel w3-blue">
    <p><i class="fas fa-info-circle w3-margin-right"></i> Utente: <strong><?= htmlspecialchars($user['username']) ?></strong></p>
  </div>

  <?php if ($run && !empty($output)) { ?>
    <div class="w3-panel w3-pale-green w3-border w3-leftbar w3-border-green">
      <h4>Risultato</h4>
      <?php foreach ($output as $line) {
        $cls = strpos($line, 'ERROR') === 0 ? 'w3-text-red' : (strpos($line, 'FAIL') === 0 ? 'w3-text-red' : (strpos($line, 'SKIP') === 0 ? 'w3-text-gray' : 'w3-text-green'));
        echo '<div class="' . $cls . '">' . htmlspecialchars($line) . '</div>';
      } ?>
    </div>
  <?php } ?>

  <div class="w3-card-4 w3-margin-bottom" style="background-color: var(--bg-color-card, #fff); color: var(--text-color, #000);">
    <header class="w3-container w3-padding-16" style="background-color: var(--bg-color-offset, #f1f1f1); color: var(--text-color-headings, #000);">
      <h3><i class="fas fa-database w3-margin-right"></i> Migrazioni</h3>
    </header>

    <div class="w3-container w3-padding">
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
      <input type="hidden" name="run" value="1">
      <button type="submit" class="w3-button w3-black w3-padding-large">
        <i class="fas fa-play w3-margin-right"></i> Esegui migrazioni pendenti (<?= $pendingCount ?>)
      </button>
    </form>
  <?php } else { ?>
    <div class="w3-panel w3-pale-green w3-leftbar w3-border-green">
      <p><i class="fas fa-check-circle w3-margin-right"></i> Tutte le migrazioni sono state applicate.</p>
    </div>
  <?php } ?>
</div>
