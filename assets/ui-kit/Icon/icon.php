<?php

function ui_icon($name, $options = []) {
  $size = $options['size'] ?? null;
  $color = $options['color'] ?? null;
  $class = $options['class'] ?? '';
  $attrs = $options['attrs'] ?? [];

  $classes = ['icon'];

  if ($size) $classes[] = 'icon--' . $size;
  if ($color) $classes[] = 'icon--' . $color;

  $classes[] = $class;

  $attrStr = '';
  foreach ($attrs as $key => $value) {
    $attrStr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
  }

  $classStr = htmlspecialchars(implode(' ', array_filter($classes)));
  return "<i class=\"$name $classStr\"$attrStr></i>";
}
