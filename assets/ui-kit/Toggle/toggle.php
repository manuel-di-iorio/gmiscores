<?php

function ui_toggle($active, $url, $options = []) {
  $labelOn = $options['labelOn'] ?? 'Enable';
  $labelOff = $options['labelOff'] ?? 'Disable';
  $size = $options['size'] ?? 'md';
  $class = $options['class'] ?? '';

  $sizes = [
    'sm' => 'ui-toggle--sm',
    'md' => 'ui-toggle--md',
    'lg' => 'ui-toggle--lg',
  ];

  $sizeClass = $sizes[$size] ?? $sizes['md'];

  $icon = $active ? 'fa-toggle-on' : 'fa-toggle-off';
  $color = $active ? 'style="color:#10b981"' : 'style="color:#9ca3af"';

  return '<a href="' . htmlspecialchars($url) . '" title="' . ($active ? htmlspecialchars($labelOn) : htmlspecialchars($labelOff)) . '" class="ui-toggle ' . $sizeClass . ' ' . htmlspecialchars($class) . '" ' . $color . '><i class="fas ' . $icon . '"></i></a>';
}
