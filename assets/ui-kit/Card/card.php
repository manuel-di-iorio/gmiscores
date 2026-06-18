<?php

function ui_card($content, $options = []) {
  $class = $options['class'] ?? '';
  $padding = $options['padding'] ?? 'md';
  $variant = $options['variant'] ?? 'default';
  $title = $options['title'] ?? null;
  $header = $options['header'] ?? null;
  $footer = $options['footer'] ?? null;
  $id = isset($options['id']) ? ' id="' . htmlspecialchars($options['id']) . '"' : '';

  $base = 'bg-surface-card border border-border-color rounded-xl shadow-sm overflow-hidden';

  $variants = [
    'default'     => '',
    'flat'        => 'shadow-none',
    'outlined'    => 'border-2 shadow-none',
    'interactive' => 'cursor-pointer transition-all duration-200 hover:shadow-card-prominent hover:-translate-y-0.5 active:translate-y-0',
    'elevated'    => 'shadow-md hover:shadow-lg',
  ];

  $bodyPaddings = [
    'sm' => 'p-3',
    'md' => 'p-5',
    'lg' => 'p-7',
  ];

  $classes = trim("$base {$variants[$variant]} " . htmlspecialchars($class));
  $bodyPad = $bodyPaddings[$padding] ?? $bodyPaddings['md'];

  $html = '<div class="' . $classes . '"' . $id . '>';

  if ($header) {
    $html .= '<div class="px-5 pt-4 font-semibold text-headings flex items-center gap-2">' . $header . '</div>';
  } elseif ($title) {
    $html .= '<div class="px-5 pt-4 font-semibold text-headings flex items-center gap-2">' . htmlspecialchars($title) . '</div>';
  }

  $html .= '<div class="' . $bodyPad . '">' . $content . '</div>';

  if ($footer) {
    $html .= '<div class="px-5 py-3 border-t border-border-color flex items-center gap-2">' . $footer . '</div>';
  }

  $html .= '</div>';

  return $html;
}
