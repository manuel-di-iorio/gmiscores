<?php
require_once("lib/db.php");

if (!isset($user) || !isset($user["admin"]) || (int)$user["admin"] !== 1) {
  header('Content-Type: application/json');
  echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
  exit;
}

require_once("models/User.php");
require_once("models/Game.php");
require_once("models/Score.php");
require_once("models/Player.php");
require_once("models/Team.php");
require_once("models/Ban.php");
require_once("models/Leaderboard.php");
require_once("models/RateLimit.php");

$allSchemas = [
  User::$schema,
  Game::$schema,
  Score::$schema,
  Player::$schema,
  Team::$schema,
  Team::$teamMembersSchema,
  Ban::$schema,
  Leaderboard::$schema,
  RateLimit::$schema,
];

// Build expected index map: tableName => [indexName => columns]
$expected = [];
foreach ($allSchemas as $schema) {
  $table = $schema['table'];
  $expected[$table] = [];
  foreach (($schema['indexes'] ?? []) as $index) {
    $columns = $index['columns'];
    $isUnique = !empty($index['unique']);
    $indexName = $table . '_' . implode('_', $columns) . ($isUnique ? '_uniq' : '_index');
    $expected[$table][$indexName] = $columns;
  }
}

// Get all existing non-primary indexes from DB
$existing = [];
$result = exec_query(
  "SELECT TABLE_NAME, INDEX_NAME, COLUMN_NAME
   FROM INFORMATION_SCHEMA.STATISTICS
   WHERE TABLE_SCHEMA = DATABASE()
     AND INDEX_NAME != 'PRIMARY'
     AND TABLE_NAME IN ('" . implode("','", array_keys($expected)) . "')"
);
while ($row = $result->fetch_assoc()) {
  $t = $row['TABLE_NAME'];
  $i = $row['INDEX_NAME'];
  if (!isset($existing[$t])) $existing[$t] = [];
  if (!isset($existing[$t][$i])) $existing[$t][$i] = [];
  $existing[$t][$i][] = $row['COLUMN_NAME'];
}

$created = [];
$dropped = [];
$skipped = [];
$errors = [];

foreach ($expected as $table => $indexes) {
  // Create missing indexes
  foreach ($indexes as $indexName => $columns) {
    if (isset($existing[$table][$indexName])) {
      $skipped[] = "$table: $indexName (exists)";
      continue;
    }

    $colList = implode('`, `', $columns);
    $isUnique = str_ends_with($indexName, '_uniq');
    $uniqueStr = $isUnique ? 'UNIQUE ' : '';
    $sql = "ALTER TABLE `$table` ADD {$uniqueStr}INDEX `$indexName` (`$colList`)";

    try {
      exec_query($sql);
      $created[] = "$table: $indexName ($colList)";
    } catch (Exception $e) {
      $errors[] = "$table: $indexName — " . $e->getMessage();
    }
  }

  // Drop extra indexes not in schema
  if (isset($existing[$table])) {
    foreach ($existing[$table] as $indexName => $cols) {
      if (isset($expected[$table][$indexName])) continue;

      $sql = "ALTER TABLE `$table` DROP INDEX `$indexName`";
      try {
        exec_query($sql);
        $dropped[] = "$table: $indexName (" . implode(',', $cols) . ")";
      } catch (Exception $e) {
        $errors[] = "$table: DROP $indexName — " . $e->getMessage();
      }
    }
  }
}

header('Content-Type: application/json');
echo json_encode([
  'ok' => empty($errors),
  'created' => $created,
  'dropped' => $dropped,
  'skipped' => $skipped,
  'errors' => $errors,
]);
