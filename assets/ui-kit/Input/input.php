<?php

function ui_input($name, $options = []) {
  $type = $options['type'] ?? 'text';
  $label = $options['label'] ?? '';
  $value = $options['value'] ?? '';
  $placeholder = $options['placeholder'] ?? '';
  $required = $options['required'] ?? false;
  $disabled = $options['disabled'] ?? false;
  $class = $options['class'] ?? '';
  $hint = $options['hint'] ?? '';
  $error = $options['error'] ?? '';
  $id = $options['id'] ?? $name;
  $isSelect = ($type === 'select' && isset($options['options']) && is_array($options['options']));
  $isCheckbox = ($type === 'checkbox');
  $isTextarea = ($type === 'textarea');

  $inputBase = 'w-full px-3.5 py-2.5 border border-solid rounded-lg text-[0.95rem] leading-normal transition-colors duration-200 box-border';
  $inputState = 'border-[var(--border-color)] bg-input-bg text-input-text placeholder:text-[var(--text-color-secondary)]';
  $inputFocus = 'focus:border-[var(--primary-color)] focus:outline-none focus:shadow-[0_0_0_3px_rgba(99,102,241,0.12)]';
  $inputError = 'border-red-600 focus:border-red-600 focus:shadow-[0_0_0_3px_rgba(220,38,38,0.12)]';
  $inputDisabled = 'disabled:bg-input-bg-disabled disabled:text-input-text-disabled disabled:cursor-not-allowed';
  $textareaExtra = 'min-h-[80px] resize-y';

  $base = trim("$inputBase $inputState $inputFocus $inputDisabled");
  if ($isTextarea) $base .= " $textareaExtra";
  if ($error) $base .= " $inputError";

  if ($class) $base .= ' ' . htmlspecialchars($class);

  $requiredMark = $required ? ' after:content-["_*"] after:text-red-600' : '';

  $disabledAttr = $disabled ? ' disabled' : '';
  $requiredAttr = $required ? ' required' : '';
  $idAttr = ' id="' . htmlspecialchars($id) . '"';
  $nameAttr = ' name="' . htmlspecialchars($name) . '"';

  $html = '<div class="mb-4">';

  if ($label) {
    $html .= '<label class="block font-semibold mb-1.5 text-sm text-[var(--text-color)]' . $requiredMark . '" for="' . htmlspecialchars($id) . '">' . htmlspecialchars($label) . '</label>';
  }

  if ($isCheckbox) {
    $checked = $value ? ' checked' : '';
    $html .= '<label class="inline-flex items-center gap-2 cursor-pointer text-sm text-[var(--text-color)]">';
    $html .= '<input type="checkbox" class="w-[18px] h-[18px] accent-[var(--primary-color)] cursor-pointer"' . $idAttr . $nameAttr . $checked . $disabledAttr . $requiredAttr . '>';
    if ($placeholder) {
      $html .= htmlspecialchars($placeholder);
    }
    $html .= '</label>';
  } elseif ($isSelect) {
    $html .= '<select class="' . $base . ' cursor-pointer appearance-auto"' . $idAttr . $nameAttr . $disabledAttr . $requiredAttr . '>';
    if (isset($options['placeholderOption'])) {
      $html .= '<option value="">' . htmlspecialchars($options['placeholderOption']) . '</option>';
    }
    foreach ($options['options'] as $optValue => $optLabel) {
      $selected = ((string)$optValue === (string)$value) ? ' selected' : '';
      $html .= '<option value="' . htmlspecialchars($optValue) . '"' . $selected . '>' . htmlspecialchars($optLabel) . '</option>';
    }
    $html .= '</select>';
  } elseif ($isTextarea) {
    $html .= '<textarea class="' . $base . '"' . $idAttr . $nameAttr . $disabledAttr . $requiredAttr . ' placeholder="' . htmlspecialchars($placeholder) . '">' . htmlspecialchars($value) . '</textarea>';
  } else {
    $htmlType = $type;
    if ($type === 'number') { $htmlType = 'number'; }
    if ($type === 'password') { $htmlType = 'password'; }
    if ($type === 'email') { $htmlType = 'email'; }
    if ($type === 'date') { $htmlType = 'date'; }
    $html .= '<input type="' . $htmlType . '" class="' . $base . '"' . $idAttr . $nameAttr . ' value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '"' . $disabledAttr . $requiredAttr . '>';
  }

  if ($hint) {
    $html .= '<div class="text-xs text-[var(--text-color-secondary)] mt-1">' . htmlspecialchars($hint) . '</div>';
  }
  if ($error) {
    $html .= '<div class="text-xs text-red-600 mt-1">' . htmlspecialchars($error) . '</div>';
  }

  $html .= '</div>';

  return $html;
}
