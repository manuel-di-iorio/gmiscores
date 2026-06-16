<?php

function ui_card($content, $options = []) {
  $class = $options['class'] ?? '';
  $padding = $options['padding'] ?? 'md';
  $variant = $options['variant'] ?? 'default';
  $title = $options['title'] ?? null;
  $header = $options['header'] ?? null;
  $footer = $options['footer'] ?? null;
  $id = isset($options['id']) ? ' id="' . htmlspecialchars($options['id']) . '"' : '';

  $variantClass = 'ui-card--' . $variant;
  $paddingClass = 'ui-card--padding-' . $padding;

  $html = '<div class="ui-card ' . $variantClass . ' ' . $paddingClass . ' ' . htmlspecialchars($class) . '"' . $id . '>';

  if ($header) {
    $html .= '<div class="ui-card__header">' . $header . '</div>';
  } elseif ($title) {
    $html .= '<div class="ui-card__header">' . htmlspecialchars($title) . '</div>';
  }

  $html .= '<div class="ui-card__body">' . $content . '</div>';

  if ($footer) {
    $html .= '<div class="ui-card__footer">' . $footer . '</div>';
  }

  $html .= '</div>';

  return $html;
}
