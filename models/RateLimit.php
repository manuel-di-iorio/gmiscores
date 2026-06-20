<?php

class RateLimit {
  public static array $schema = [
    'table'      => 'api_rate_limits',
    'primaryKey' => 'id',
    'timestamps' => false,
    'columns'    => [
      'id'          => ['type' => 'int',     'auto' => true],
      'identifier'  => ['type' => 'string'],
      'endpoint'    => ['type' => 'string'],
      'requested_at'=> ['type' => 'datetime'],
    ],
    'indexes'    => [
      ['columns' => ['endpoint', 'requested_at']],
      ['columns' => ['identifier', 'endpoint', 'requested_at']],
    ],
  ];
}
