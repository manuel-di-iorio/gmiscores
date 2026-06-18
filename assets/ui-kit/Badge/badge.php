<?php

function ui_badge($label, $variant = 'default', $options = []) {
  $icon = $options['icon'] ?? null;
  $class = $options['class'] ?? '';
  $pill = $options['pill'] ?? true;

  $base = 'inline-flex items-center gap-1 text-[0.82em] font-semibold leading-none';

  $variants = [
    'default' => 'bg-gray-500/10 text-gray-500',
    'success' => 'bg-emerald-500/10 text-emerald-500',
    'danger'  => 'bg-red-500/10 text-red-500',
    'warning' => 'bg-amber-500/10 text-amber-500',
    'info'    => 'bg-blue-500/10 text-blue-500',
  ];

  $shapeClass = $pill ? 'rounded-full px-2.5 py-[5px]' : '';

  $variantClass = $variants[$variant] ?? $variants['default'];

  $iconHtml = $icon ? '<i class="' . htmlspecialchars($icon) . '"></i> ' : '';

  return '<span class="' . trim("$base $variantClass $shapeClass " . htmlspecialchars($class)) . '">' . $iconHtml . htmlspecialchars($label) . '</span>';
}
