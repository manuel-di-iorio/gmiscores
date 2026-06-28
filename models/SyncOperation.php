<?php

class SyncOperation {
  public static array $schema = [
    'table'       => 'sync_operations',
    'primaryKey'  => 'id',
    'timestamps'  => true,
    'columns'     => [
      'id'           => ['type' => 'int',      'auto' => true],
      'operation_id' => ['type' => 'string'],
      'game_id'      => ['type' => 'int'],
      'player_id'    => ['type' => 'int',      'nullable' => true],
      'type'         => ['type' => 'string'],
      'status'       => ['type' => 'string'],
      'result'       => ['type' => 'text',     'nullable' => true],
      'created_at'   => ['type' => 'datetime'],
      'updated_at'   => ['type' => 'datetime'],
    ],
    'indexes'     => [
      ['columns' => ['game_id', 'operation_id'], 'unique' => true],
      ['columns' => ['game_id', 'player_id']],
      ['columns' => ['created_at']],
    ],
  ];

  public static function findByOpId(int $gameId, string $opId): ?array {
    global $dbTableSyncOperations;
    $sql = "SELECT * FROM $dbTableSyncOperations WHERE game_id=? AND operation_id=? LIMIT 1";
    $result = exec_query($sql, ["is", $gameId, $opId]);
    return $result->num_rows ? $result->fetch_assoc() : null;
  }

  public static function record(int $gameId, string $opId, ?int $playerId, string $type, string $status, ?string $result = null): void {
    global $dbTableSyncOperations;
    $sql = "INSERT INTO $dbTableSyncOperations (game_id, operation_id, player_id, type, status, result, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE status=VALUES(status), result=VALUES(result), updated_at=NOW()";
    exec_query($sql, ["isisss", $gameId, $opId, $playerId, $type, $status, $result]);
  }

  public static function deleteOld(int $days = 30): void {
    global $dbTableSyncOperations;
    $cutoff = date('Y-m-d H:i:s', time() - ($days * 86400));
    exec_query("DELETE FROM $dbTableSyncOperations WHERE created_at < ?", ["s", $cutoff]);
  }
}
