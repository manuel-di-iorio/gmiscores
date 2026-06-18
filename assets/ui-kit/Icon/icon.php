<?php

function ui_icon($name, $options = []) {
  $size = $options['size'] ?? null;
  $color = $options['color'] ?? null;
  $class = $options['class'] ?? '';
  $attrs = $options['attrs'] ?? [];

  $classes = ['inline-flex items-center justify-center'];

  $sizes = ['sm' => 'text-sm', 'md' => 'text-base', 'lg' => 'text-lg', 'xl' => 'text-2xl'];
  $colors = [
    'primary'   => 'text-primary-color',
    'secondary' => 'text-secondary',
    'success'   => 'text-emerald-500',
    'danger'    => 'text-red-500',
    'warning'   => 'text-amber-500',
    'muted'     => 'text-gray-400',
  ];

  if ($size && isset($sizes[$size])) $classes[] = $sizes[$size];
  if ($color && isset($colors[$color])) $classes[] = $colors[$color];

  $classes[] = $class;

  $attrStr = '';
  foreach ($attrs as $key => $value) {
    $attrStr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
  }

  $classStr = htmlspecialchars(implode(' ', array_filter($classes)));
  return "<i class=\"$name $classStr\"$attrStr></i>";
}
