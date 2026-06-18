<?php

function ui_toggle($active, $url, $options = []) {
  $labelOn = $options['labelOn'] ?? 'Enable';
  $labelOff = $options['labelOff'] ?? 'Disable';
  $size = $options['size'] ?? 'md';
  $class = $options['class'] ?? '';

  $base = 'inline-flex items-center justify-center no-underline transition-all duration-150 rounded-md hover:scale-110';

  $sizes = [
    'sm' => 'text-xl px-1.5 py-1',
    'md' => 'text-2xl px-1.5 py-1',
    'lg' => 'text-3xl px-2 py-1',
  ];

  $sizeClass = $sizes[$size] ?? $sizes['md'];
  $colorClass = $active ? 'text-emerald-500' : 'text-gray-400';
  $icon = $active ? 'fa-toggle-on' : 'fa-toggle-off';

  $classes = trim("$base $sizeClass $colorClass " . htmlspecialchars($class));

  return '<a href="' . htmlspecialchars($url) . '" title="' . ($active ? htmlspecialchars($labelOn) : htmlspecialchars($labelOff)) . '" class="' . $classes . '"><i class="fas ' . $icon . '"></i></a>';
}
