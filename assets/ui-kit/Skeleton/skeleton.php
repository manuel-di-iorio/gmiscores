<?php

function ui_skeleton($type = 'text', $count = 1, $options = []) {
  $variants = [
    'text'  => '<div class="ui-skeleton ui-skeleton--text h-3.5 w-full rounded-md"></div>',
    'title' => '<div class="ui-skeleton ui-skeleton--title h-5 w-3/5 rounded-md"></div>',
    'avatar' => '<div class="ui-skeleton ui-skeleton--avatar w-11 h-11 rounded-full"></div>',
    'stat'  => '<div class="ui-skeleton--stat flex items-center gap-4 p-5 bg-surface-card border border-solid border-border-color rounded-xl"><div class="ui-skeleton--stat-icon w-11 h-11 rounded-xl flex-shrink-0"></div><div class="flex flex-col gap-2 flex-1"><div class="ui-skeleton--title h-5 w-1/2 rounded-md"></div><div class="ui-skeleton--text h-3.5 w-7/10 rounded-md"></div></div></div>',
    'chart' => '<div class="ui-skeleton ui-skeleton--chart h-64 w-full rounded-lg border-0"></div>',
    'table-row' => '<div class="flex gap-4 px-4 py-3 items-center bg-transparent"><div class="ui-skeleton--table-cell h-3 rounded" style="width:6%"></div><div class="ui-skeleton--table-cell h-3 rounded" style="width:22%"></div><div class="ui-skeleton--table-cell h-3 rounded" style="width:16%"></div><div class="ui-skeleton--table-cell h-3 rounded" style="width:14%"></div><div class="ui-skeleton--table-cell h-3 rounded" style="width:18%"></div><div class="ui-skeleton--table-cell h-3 rounded" style="width:8%"></div></div>',
  ];

  if (!isset($variants[$type])) $type = 'text';

  $class = $options['class'] ?? '';
  $html = '<div class="flex flex-col gap-3 py-2 ' . htmlspecialchars($class) . '">';
  for ($i = 0; $i < $count; $i++) {
    $html .= $variants[$type];
  }
  $html .= '</div>';
  return $html;
}
