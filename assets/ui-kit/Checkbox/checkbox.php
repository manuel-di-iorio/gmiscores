<?php

function ui_checkbox($name, $checked, $options = []) {
  $label = $options['label'] ?? '';
  $description = $options['description'] ?? '';
  $icon = $options['icon'] ?? '';
  $value = $options['value'] ?? '1';
  $disabled = $options['disabled'] ?? false;
  $class = $options['class'] ?? '';

  $checkedAttr = $checked ? ' checked' : '';
  $disabledAttr = $disabled ? ' disabled' : '';

  $html = '<div class="mb-4 ' . htmlspecialchars($class) . '">';
  $html .= '<label class="flex items-start gap-2 cursor-pointer select-none">';
  $html .= '<input type="checkbox" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars($value) . '" class="mt-0.5 w-[18px] h-[18px] accent-[var(--primary-color)] cursor-pointer shrink-0"' . $checkedAttr . $disabledAttr . '>';

  if ($label || $description) {
    $html .= '<div>';
    if ($label) {
      $html .= '<b>' . htmlspecialchars($label) . '</b>';
    }
    if ($description) {
      $html .= '<small class="block font-normal text-[var(--text-muted,#666)] text-[0.85em] leading-relaxed">';
      if ($icon) {
        $html .= '<i class="' . htmlspecialchars($icon) . '"></i> ';
      }
      $html .= htmlspecialchars($description) . '</small>';
    }
    $html .= '</div>';
  }

  $html .= '</label>';
  $html .= '</div>';

  return $html;
}
