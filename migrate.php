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

$migrationsDir = __DIR__ . '/migrations';
$dbTableMigrations = 'migrations';

exec_query("CREATE TABLE IF NOT EXISTS $dbTableMigrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  migration VARCHAR(255) NOT NULL UNIQUE,
  applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$applied = [];
$result = exec_query("SELECT migration FROM $dbTableMigrations");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $applied[$row['migration']] = true;
  }
}

$files = glob($migrationsDir . '/*.php');
sort($files);

$applied_count = 0;
$skipped = 0;
$errors = [];

foreach ($files as $file) {
  $name = basename($file);
  if (isset($applied[$name])) {
    $skipped++;
    continue;
  }

  $data = include $file;
  $sqls = is_array($data['sql']) ? $data['sql'] : [$data['sql']];

  $success = true;
  foreach ($sqls as $sql) {
    try {
      exec_query($sql);
    } catch (Exception $e) {
      $errors[] = "$name: " . $e->getMessage();
      $success = false;
      break;
    }
  }

  if ($success) {
    exec_query("INSERT INTO $dbTableMigrations (migration) VALUES (?)", ["s", $name]);
    $applied_count++;
  }
}

if (!empty($errors)) {
  echo "ERROR: " . implode('; ', $errors);
} else {
  echo "OK";
  if ($applied_count > 0) {
    echo " ($applied_count applied, $skipped skipped)";
  } else {
    echo " (all already applied)";
  }
}
