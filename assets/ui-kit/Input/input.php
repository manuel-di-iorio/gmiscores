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

  $labelClass = 'ui-label';
  $inputClass = $isSelect ? 'ui-select' : 'ui-input';
  if ($error) {
    $inputClass .= ' ui-input--error';
  }
  if ($class) {
    $inputClass .= ' ' . htmlspecialchars($class);
  }
  if ($required) {
    $labelClass .= ' ui-label--required';
  }

  $disabledAttr = $disabled ? ' disabled' : '';
  $requiredAttr = $required ? ' required' : '';
  $idAttr = ' id="' . htmlspecialchars($id) . '"';
  $nameAttr = ' name="' . htmlspecialchars($name) . '"';

  $html = '<div class="ui-input-group">';

  if ($label) {
    $html .= '<label class="' . $labelClass . '" for="' . htmlspecialchars($id) . '">' . htmlspecialchars($label) . '</label>';
  }

  if ($isCheckbox) {
    $checked = $value ? ' checked' : '';
    $html .= '<label class="ui-checkbox">';
    $html .= '<input type="checkbox"' . $idAttr . $nameAttr . $checked . $disabledAttr . $requiredAttr . '>';
    if ($placeholder) {
      $html .= htmlspecialchars($placeholder);
    }
    $html .= '</label>';
  } elseif ($isSelect) {
    $html .= '<select class="' . $inputClass . '"' . $idAttr . $nameAttr . $disabledAttr . $requiredAttr . '>';
    if (isset($options['placeholderOption'])) {
      $html .= '<option value="">' . htmlspecialchars($options['placeholderOption']) . '</option>';
    }
    foreach ($options['options'] as $optValue => $optLabel) {
      $selected = ((string)$optValue === (string)$value) ? ' selected' : '';
      $html .= '<option value="' . htmlspecialchars($optValue) . '"' . $selected . '>' . htmlspecialchars($optLabel) . '</option>';
    }
    $html .= '</select>';
  } elseif ($isTextarea) {
    $html .= '<textarea class="' . $inputClass . '"' . $idAttr . $nameAttr . $disabledAttr . $requiredAttr . ' placeholder="' . htmlspecialchars($placeholder) . '">' . htmlspecialchars($value) . '</textarea>';
  } else {
    $htmlType = $type;
    if ($type === 'number') { $htmlType = 'number'; }
    if ($type === 'password') { $htmlType = 'password'; }
    if ($type === 'email') { $htmlType = 'email'; }
    if ($type === 'date') { $htmlType = 'date'; }
    $html .= '<input type="' . $htmlType . '" class="' . $inputClass . '"' . $idAttr . $nameAttr . ' value="' . htmlspecialchars($value) . '" placeholder="' . htmlspecialchars($placeholder) . '"' . $disabledAttr . $requiredAttr . '>';
  }

  if ($hint) {
    $html .= '<div class="ui-hint">' . htmlspecialchars($hint) . '</div>';
  }
  if ($error) {
    $html .= '<div class="ui-error">' . htmlspecialchars($error) . '</div>';
  }

  $html .= '</div>';

  return $html;
}
