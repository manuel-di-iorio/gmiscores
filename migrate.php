<?php
require_once("lib/db.php");
require_once("lib/checkSession.php");

// Check admin privileges
$adminRow = exec_query("SELECT admin FROM users WHERE id = ?", ["i", $user['id']])->fetch_assoc();
$isAdmin = $adminRow && (int)$adminRow['admin'] === 1;

if (!$isAdmin) {
  header("Location: /");
  exit;
}

$migrationsDir = __DIR__ . '/migrations';
$dbTableMigrations = 'migrations';

// Ensure migrations table exists
exec_query("CREATE TABLE IF NOT EXISTS $dbTableMigrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  migration VARCHAR(255) NOT NULL UNIQUE,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Collect applied migrations
$applied = [];
$result = exec_query("SELECT migration, applied_at FROM $dbTableMigrations ORDER BY migration");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $applied[$row['migration']] = $row['applied_at'];
  }
}

// Collect available migration files and preload metadata
$files = glob($migrationsDir . '/*.php');
sort($files);

$migrations = [];
foreach ($files as $file) {
  $name = basename($file);
  $data = include $file;
  $migrations[] = [
    'file' => $file,
    'name' => $name,
    'description' => is_array($data) && isset($data['description']) ? $data['description'] : 'N/A',
    'sql' => is_array($data) && isset($data['sql']) ? (is_array($data['sql']) ? $data['sql'] : [$data['sql']]) : [],
    'is_applied' => isset($applied[$name]),
  ];
}

// Run migrations if requested
$run = isset($_POST['run']) && $_POST['run'] === '1';
$output = [];

if ($run) {
  foreach ($migrations as $m) {
    if ($m['is_applied']) continue;

    $success = true;
    foreach ($m['sql'] as $sql) {
      try {
        exec_query($sql);
      } catch (Exception $e) {
        $output[] = "ERROR {$m['name']}: " . $e->getMessage();
        $success = false;
        break;
      }
    }

    if ($success) {
      exec_query("INSERT INTO $dbTableMigrations (migration) VALUES (?)", ["s", $m['name']]);
      $output[] = "OK {$m['name']}";
      $applied[$m['name']] = date('Y-m-d H:i:s');
    } else {
      $output[] = "FAIL {$m['name']}";
    }
  }

  // Update is_applied after running
  foreach ($migrations as &$m) {
    $m['is_applied'] = isset($applied[$m['name']]);
  }
  unset($m);
}

$view = "migrate";
$pageName = "Migration";
require_once("includes/layout.php");
