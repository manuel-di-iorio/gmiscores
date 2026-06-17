<?php

function ui_skeleton($type = 'text', $count = 1, $options = []) {
  $variants = [
    'text'  => '<div class="ui-skeleton ui-skeleton--text"></div>',
    'title' => '<div class="ui-skeleton ui-skeleton--title"></div>',
    'avatar' => '<div class="ui-skeleton ui-skeleton--avatar"></div>',
    'stat'  => '<div class="ui-skeleton ui-skeleton--stat"><div class="ui-skeleton--stat-icon"></div><div class="ui-skeleton--stat-lines"><div class="ui-skeleton--title" style="width:50%"></div><div class="ui-skeleton--text" style="width:70%"></div></div></div>',
    'chart' => '<div class="ui-skeleton ui-skeleton--chart"></div>',
    'table-row' => '<div class="ui-skeleton ui-skeleton--table-row"><div class="ui-skeleton--table-cell" style="width:6%"></div><div class="ui-skeleton--table-cell" style="width:22%"></div><div class="ui-skeleton--table-cell" style="width:16%"></div><div class="ui-skeleton--table-cell" style="width:14%"></div><div class="ui-skeleton--table-cell" style="width:18%"></div><div class="ui-skeleton--table-cell" style="width:8%"></div></div>',
  ];

  if (!isset($variants[$type])) $type = 'text';

  $class = $options['class'] ?? '';
  $html = '<div class="ui-skeleton-wrapper ' . htmlspecialchars($class) . '">';
  for ($i = 0; $i < $count; $i++) {
    $html .= $variants[$type];
  }
  $html .= '</div>';
  return $html;
}
