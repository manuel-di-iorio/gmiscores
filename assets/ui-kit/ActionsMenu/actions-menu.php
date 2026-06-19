<?php

function ui_actions_menu($actions, $options = []) {
  $id = $options['id'] ?? 'actions-menu-' . uniqid();
  $class = $options['class'] ?? '';
  $triggerIcon = $options['triggerIcon'] ?? 'fas fa-ellipsis-v';
  $triggerLabel = $options['triggerLabel'] ?? '';

  $html = '<div id="' . htmlspecialchars($id) . '" class="ui-actions-menu ' . htmlspecialchars($class) . '">';
  $html .= '<button type="button" class="ui-actions-menu__trigger"' . ($triggerLabel ? ' aria-label="' . htmlspecialchars($triggerLabel) . '"' : '') . ($triggerLabel ? ' data-tippy-content="' . htmlspecialchars($triggerLabel) . '"' : '') . '>';
  $html .= '<i class="' . htmlspecialchars($triggerIcon) . '"></i>';
  $html .= '</button>';
  $html .= '<div class="ui-actions-menu__dropdown">';

  foreach ($actions as $action) {
    if (isset($action['divider']) && $action['divider']) {
      $html .= '<div class="ui-actions-menu__divider"></div>';
      continue;
    }

    $label = $action['label'] ?? '';
    $icon = $action['icon'] ?? '';
    $variant = $action['variant'] ?? 'default';
    $href = $action['href'] ?? null;
    $onclick = $action['onclick'] ?? '';
    $disabled = $action['disabled'] ?? false;
    $tooltip = $action['tooltip'] ?? '';
    $itemClass = isset($action['class']) ? ' ' . $action['class'] : '';
    $attrs = $action['attrs'] ?? [];

    $itemClasses = 'ui-actions-menu__item';
    if ($variant === 'danger') {
      $itemClasses .= ' ui-actions-menu__item--danger';
    }
    $itemClasses .= $itemClass;

    $attrStr = '';
    foreach ($attrs as $key => $value) {
      $attrStr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
    }

    $iconHtml = $icon ? '<i class="' . htmlspecialchars($icon) . '"></i>' : '';
    $labelHtml = '<span>' . htmlspecialchars($label) . '</span>';
    $tipAttr = $tooltip ? ' data-tippy-content="' . htmlspecialchars($tooltip) . '"' : '';

    if ($disabled) {
      $html .= '<button type="button" class="' . $itemClasses . '" disabled style="opacity:0.5;cursor:not-allowed"' . $tipAttr . $attrStr . '>' . $iconHtml . $labelHtml . '</button>';
    } elseif ($href) {
      $html .= '<a href="' . htmlspecialchars($href) . '" class="' . $itemClasses . '"' . $tipAttr . $attrStr . '>' . $iconHtml . $labelHtml . '</a>';
    } else {
      $html .= '<button type="button" class="' . $itemClasses . '" onclick="' . htmlspecialchars($onclick) . '"' . $tipAttr . $attrStr . '>' . $iconHtml . $labelHtml . '</button>';
    }
  }

  $html .= '</div></div>';

  return $html;
}
