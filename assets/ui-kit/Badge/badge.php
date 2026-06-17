<?php

function ui_badge($label, $variant = 'default', $options = []) {
  $icon = $options['icon'] ?? null;
  $class = $options['class'] ?? '';
  $pill = $options['pill'] ?? true;

  $variants = [
    'default' => 'ui-badge--default',
    'success' => 'ui-badge--success',
    'danger'  => 'ui-badge--danger',
    'warning' => 'ui-badge--warning',
    'info'    => 'ui-badge--info',
  ];

  $variantClass = $variants[$variant] ?? $variants['default'];
  $shapeClass = $pill ? 'ui-badge--pill' : '';

  $iconHtml = $icon ? '<i class="' . htmlspecialchars($icon) . '"></i> ' : '';

  return '<span class="ui-badge ' . $variantClass . ' ' . $shapeClass . ' ' . htmlspecialchars($class) . '">' . $iconHtml . htmlspecialchars($label) . '</span>';
}
