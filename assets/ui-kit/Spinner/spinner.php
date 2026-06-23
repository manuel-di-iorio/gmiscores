<?php
function ui_spinner(string $size = 'md', array $attrs = []): string {
  $sizeClass = '';
  if ($size === 'sm') $sizeClass = ' ui-spinner--sm';
  elseif ($size === 'lg') $sizeClass = ' ui-spinner--lg';
  elseif ($size === 'xl') $sizeClass = ' ui-spinner--xl';
  $attrStr = '';
  foreach ($attrs as $k => $v) {
    $attrStr .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
  }
  return '<span class="ui-spinner' . $sizeClass . '"' . $attrStr . '></span>';
}

function ui_spinner_block(string $label = '', string $size = 'xl', array $attrs = []): string {
  $attrStr = '';
  foreach ($attrs as $k => $v) {
    $attrStr .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
  }
  $labelHtml = $label ? '<span class="ui-spinner-block__label">' . htmlspecialchars($label) . '</span>' : '';
  return '<div class="ui-spinner-block"' . $attrStr . '>' . ui_spinner($size) . $labelHtml . '</div>';
}
