<?php

function ui_modal($id, $options = []) {
  $title = $options['title'] ?? '';
  $content = $options['content'] ?? '';
  $footer = $options['footer'] ?? '';
  $size = $options['size'] ?? 'md';
  $class = $options['class'] ?? '';
  $closeButton = $options['close_button'] ?? true;

  $sizeClass = 'ui-modal--' . $size;

  $html = '<div id="' . htmlspecialchars($id) . '" class="ui-modal-overlay" onclick="if (event.target === this) closeModal(\'' . htmlspecialchars($id) . '\')">';
  $html .= '<div class="ui-modal ' . $sizeClass . ' ' . htmlspecialchars($class) . '">';

  if ($title) {
    $html .= '<div class="ui-modal__header">';
    $html .= '<h3 class="ui-modal__title">' . htmlspecialchars($title) . '</h3>';
    if ($closeButton) {
      $html .= '<button type="button" class="ui-modal__close" onclick="closeModal(\'' . htmlspecialchars($id) . '\')">&times;</button>';
    }
    $html .= '</div>';
  }

  $html .= '<div class="ui-modal__body">' . $content . '</div>';

  if ($footer) {
    $footerClass = 'ui-modal__footer';
    if (!empty($options['footer_right'])) {
      $footerClass .= ' ui-modal__footer--right';
    }
    $html .= '<div class="' . $footerClass . '">' . $footer . '</div>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}
