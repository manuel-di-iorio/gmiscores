<?php
require_once("lib/db.php");

if (!isset($user)) {
  echo "ERROR: Not logged in";
  exit;
}

if (!isset($user["admin"]) || (int)$user["admin"] !== 1) {
  echo "ERROR: Not an admin";
  exit;
}

$localesDir = __DIR__ . '/locales';
$defaultLang = 'en';
$otherLangs = ['it', 'es', 'fr', 'de'];

$enData = json_decode(file_get_contents("$localesDir/$defaultLang.json"), true);
$enKeys = array_keys($enData);
sort($enKeys);

$localeData = [];
foreach ($otherLangs as $lang) {
  $path = "$localesDir/$lang.json";
  if (file_exists($path)) {
    $localeData[$lang] = json_decode(file_get_contents($path), true);
  } else {
    $localeData[$lang] = [];
  }
}

function findUsedKeys($dir) {
  $used = [];
  $patterns = [
    'php' => '/__\([\'"]([a-zA-Z0-9_]+)[\'"]\)/',
    'js' => '/__\([\'"]([a-zA-Z0-9_]+)[\'"]\)/',
    'html' => '/data-translate="([a-zA-Z0-9_]+)"/',
  ];
  $extensions = ['php', 'js', 'html'];

  $iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
  );

  foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $ext = $file->getExtension();
    if (!in_array($ext, $extensions)) continue;

    $relativePath = str_replace($dir . DIRECTORY_SEPARATOR, '', $file->getPathname());
    if (strpos($relativePath, '.git') === 0) continue;
    if (strpos($relativePath, 'node_modules') === 0) continue;
    if (strpos($relativePath, 'locales') === 0) continue;

    $content = file_get_contents($file->getPathname());
    if ($content === false) continue;

    $pattern = $patterns[$ext] ?? null;
    if ($pattern && preg_match_all($pattern, $content, $matches)) {
      foreach ($matches[1] as $key) {
        $used[$key] = $relativePath;
      }
    }
  }
  return $used;
}

$usedKeys = findUsedKeys(__DIR__);
$unusedKeys = array_diff($enKeys, array_keys($usedKeys));
sort($unusedKeys);

$results = [];
foreach ($enKeys as $key) {
  $row = ['key' => $key, 'locales' => [], 'unused' => in_array($key, $unusedKeys)];
  foreach ($otherLangs as $lang) {
    $row['locales'][$lang] = isset($localeData[$lang][$key]);
  }
  $results[] = $row;
}

$orphanKeys = [];
foreach ($otherLangs as $lang) {
  foreach (array_keys($localeData[$lang] ?? []) as $key) {
    if (!in_array($key, $enKeys)) {
      $orphanKeys[$lang][] = $key;
    }
  }
}
foreach ($orphanKeys as &$keys) { sort($keys); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Controllo Locales - Admin</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; color: #333; padding: 20px; }
    .container { max-width: 1400px; margin: 0 auto; }
    h1 { margin-bottom: 20px; color: #1a1a1a; }
    .summary { display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
    .summary-card { background: white; border-radius: 8px; padding: 16px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .summary-card h3 { font-size: 14px; color: #666; margin-bottom: 4px; }
    .summary-card .value { font-size: 28px; font-weight: 700; }
    .summary-card .value.green { color: #16a34a; }
    .summary-card .value.red { color: #dc2626; }
    .summary-card .value.orange { color: #ea580c; }
    .summary-card .value.gray { color: #6b7280; }
    .legend { background: white; border-radius: 8px; padding: 16px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; gap: 24px; flex-wrap: wrap; font-size: 14px; }
    .legend-item { display: flex; align-items: center; gap: 6px; }
    .legend-icon { width: 20px; text-align: center; font-weight: bold; }
    .filters { background: white; border-radius: 8px; padding: 16px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
    .filters label { font-size: 14px; color: #666; }
    .filters select, .filters input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
    .filters input[type="text"] { width: 250px; }
    table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    th { background: #f8fafc; padding: 12px 16px; text-align: left; font-size: 13px; font-weight: 600; color: #475569; border-bottom: 2px solid #e2e8f0; position: sticky; top: 0; }
    td { padding: 10px 16px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
    tr:hover { background: #f8fafc; }
    tr.highlight-missing { background: #fef2f2; }
    tr.highlight-orphan { background: #fff7ed; }
    tr.highlight-unused { background: #f9fafb; }
    .status-ok { color: #16a34a; font-weight: 600; }
    .status-missing { color: #dc2626; font-weight: 600; }
    .status-orphan { color: #ea580c; font-weight: 600; }
    .status-unused { color: #9ca3af; font-style: italic; }
    .orphan-section { margin-top: 30px; }
    .orphan-section h2 { margin-bottom: 16px; color: #1a1a1a; font-size: 20px; }
    .orphan-lang { margin-bottom: 20px; }
    .orphan-lang h3 { margin-bottom: 8px; color: #475569; font-size: 16px; }
    .orphan-list { background: white; border-radius: 8px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .orphan-list code { display: inline-block; background: #fef2f2; color: #dc2626; padding: 2px 8px; border-radius: 4px; margin: 2px 4px 2px 0; font-size: 13px; }
    .empty { color: #9ca3af; font-style: italic; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Controllo Locales</h1>

    <div class="summary">
      <div class="summary-card">
        <h3>Totale chiavi (EN)</h3>
        <div class="value"><?= count($enKeys) ?></div>
      </div>
      <div class="summary-card">
        <h3>Chiavi mancanti</h3>
        <?php
        $totalMissing = 0;
        foreach ($otherLangs as $lang) {
          $totalMissing += count(array_diff($enKeys, array_keys($localeData[$lang] ?? [])));
        }
        ?>
        <div class="value <?= $totalMissing > 0 ? 'red' : 'green' ?>"><?= $totalMissing ?></div>
      </div>
      <div class="summary-card">
        <h3>Chiavi orfane</h3>
        <?php $totalOrphan = array_sum(array_map('count', $orphanKeys)); ?>
        <div class="value <?= $totalOrphan > 0 ? 'orange' : 'green' ?>"><?= $totalOrphan ?></div>
      </div>
      <div class="summary-card">
        <h3>Chiavi inutilizzate</h3>
        <div class="value <?= count($unusedKeys) > 0 ? 'gray' : 'green' ?>"><?= count($unusedKeys) ?></div>
      </div>
    </div>

    <div class="legend">
      <div class="legend-item"><span class="legend-icon status-ok">✓</span> Presente</div>
      <div class="legend-item"><span class="legend-icon status-missing">✗</span> Mancante (in EN ma non nel locale)</div>
      <div class="legend-item"><span class="legend-icon status-orphan">⚠</span> Orfana (nel locale ma non in EN)</div>
      <div class="legend-item"><span class="legend-icon status-unused">●</span> Inutilizzata (non usata in codice)</div>
    </div>

    <div class="filters">
      <label>Filtro:</label>
      <select id="filterType">
        <option value="all">Tutte</option>
        <option value="missing">Mancanti</option>
        <option value="orphan">Orfane</option>
        <option value="unused">Inutilizzate</option>
        <option value="ok">Solo complete</option>
      </select>
      <input type="text" id="filterSearch" placeholder="Cerca chiave...">
      <button id="exportCsv" style="margin-left:auto;padding:8px 16px;background:#3b82f6;color:white;border:none;border-radius:6px;cursor:pointer;font-size:14px;">Esporta CSV</button>
    </div>

    <table>
      <thead>
        <tr>
          <th>Chiave</th>
          <?php foreach ($otherLangs as $lang): ?>
            <th><?= strtoupper($lang) ?></th>
          <?php endforeach; ?>
          <th>Stato</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($results as $row):
          $hasMissing = in_array(false, $row['locales']);
          $rowClass = '';
          if ($row['unused']) $rowClass = 'highlight-unused';
          elseif ($hasMissing) $rowClass = 'highlight-missing';
        ?>
        <tr class="<?= $rowClass ?>" data-key="<?= htmlspecialchars($row['key']) ?>">
          <td><code><?= htmlspecialchars($row['key']) ?></code></td>
          <?php foreach ($otherLangs as $lang): ?>
            <td>
              <?php if ($row['locales'][$lang]): ?>
                <span class="status-ok">✓</span>
              <?php else: ?>
                <span class="status-missing">✗</span>
              <?php endif; ?>
            </td>
          <?php endforeach; ?>
          <td>
            <?php if ($row['unused']): ?>
              <span class="status-unused">● Inutilizzata</span>
            <?php else: ?>
              <span class="status-ok">✓ Usata</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <?php if (!empty($orphanKeys)): ?>
    <div class="orphan-section">
      <h2>Chiavi orfane (presenti nei locale ma non in EN)</h2>
      <?php foreach ($orphanKeys as $lang => $keys): ?>
        <div class="orphan-lang">
          <h3><?= strtoupper($lang) ?> (<?= count($keys) ?> chiavi)</h3>
          <div class="orphan-list">
            <?php foreach ($keys as $key): ?>
              <code><?= htmlspecialchars($key) ?></code>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($unusedKeys)): ?>
    <div class="orphan-section">
      <h2>Chiavi inutilizzate (<?= count($unusedKeys) ?>)</h2>
      <div class="orphan-list">
        <?php foreach ($unusedKeys as $key): ?>
          <code><?= htmlspecialchars($key) ?></code>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <script>
    const filterType = document.getElementById('filterType');
    const filterSearch = document.getElementById('filterSearch');
    const rows = document.querySelectorAll('tbody tr');

    function applyFilters() {
      const type = filterType.value;
      const search = filterSearch.value.toLowerCase();

      rows.forEach(row => {
        const key = row.dataset.key;
        const hasMissing = row.querySelector('.status-missing') !== null;
        const isUnused = row.querySelector('.status-unused') !== null;

        let showByType = true;
        if (type === 'missing') showByType = hasMissing;
        else if (type === 'orphan') showByType = false;
        else if (type === 'unused') showByType = isUnused;
        else if (type === 'ok') showByType = !hasMissing && !isUnused;

        const showBySearch = !search || key.toLowerCase().includes(search);

        row.style.display = (showByType && showBySearch) ? '' : 'none';
      });
    }

    filterType.addEventListener('change', applyFilters);
    filterSearch.addEventListener('input', applyFilters);

    document.getElementById('exportCsv').addEventListener('click', function() {
      const rows = document.querySelectorAll('tbody tr');
      const csv = [];
      csv.push(['Chiave', 'IT', 'ES', 'FR', 'DE', 'Problema'].join(','));

      rows.forEach(row => {
        if (row.style.display === 'none') return;
        const hasMissing = row.querySelector('.status-missing') !== null;
        const isUnused = row.querySelector('.status-unused') !== null;
        if (!hasMissing && !isUnused) return;

        const key = row.dataset.key;
        const cells = row.querySelectorAll('td');
        const problems = [];
        if (hasMissing) {
          for (let i = 1; i < cells.length - 1; i++) {
            if (cells[i].querySelector('.status-missing')) {
              problems.push('mancante in ' + ['IT','ES','FR','DE'][i-1]);
            }
          }
        }
        if (isUnused) problems.push('inutilizzata');

        const vals = [];
        for (let i = 1; i < cells.length - 1; i++) {
          vals.push(cells[i].querySelector('.status-ok') ? 'SI' : 'NO');
        }
        csv.push(['"' + key.replace(/"/g, '""') + '"', ...vals, '"' + problems.join('; ').replace(/"/g, '""') + '"'].join(','));
      });

      const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = 'locales-report.csv';
      link.click();
    });
  </script>
</body>
</html>
