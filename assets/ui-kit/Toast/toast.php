<?php

function ui_toast($title, $options = []) {
  $message = $options['message'] ?? '';
  $variant = $options['variant'] ?? 'info';
  $closable = $options['closable'] ?? true;
  $duration = $options['duration'] ?? 5000;
  $id = $options['id'] ?? ('toast-' . substr(md5(uniqid('', true)), 0, 8));
  $class = $options['class'] ?? '';

  $icons = [
    'success' => 'fa-check-circle',
    'error'   => 'fa-times-circle',
    'warning' => 'fa-exclamation-triangle',
    'info'    => 'fa-info-circle',
  ];

  $iconClass = $icons[$variant] ?? $icons['info'];

  $html = '<div id="' . htmlspecialchars($id) . '" class="ui-toast ui-toast--' . htmlspecialchars($variant) . ' ' . htmlspecialchars($class) . '" role="alert">';
  $html .= '<div class="ui-toast__icon"><i class="fas ' . $iconClass . '"></i></div>';
  $html .= '<div class="ui-toast__body">';
  $html .= '<div class="ui-toast__title">' . htmlspecialchars($title) . '</div>';
  if ($message) {
    $html .= '<div class="ui-toast__message">' . htmlspecialchars($message) . '</div>';
  }
  $html .= '</div>';

  if ($closable) {
    $html .= '<button type="button" class="ui-toast__close" onclick="uiToastDismiss(\'' . htmlspecialchars($id) . '\')" aria-label="Close">&times;</button>';
  }

  if ($duration > 0) {
    $html .= '<div class="ui-toast__progress" style="width:100%;transition-duration:' . intval($duration) . 'ms"></div>';
  }

  $html .= '</div>';

  if ($duration > 0) {
    $html .= '<script>(function(){var t=document.getElementById("' . htmlspecialchars($id) . '");if(!t)return;requestAnimationFrame(function(){var p=t.querySelector(".ui-toast__progress");if(p)p.style.width="0%";});setTimeout(function(){uiToastDismiss("' . htmlspecialchars($id) . '");},' . intval($duration) . ');})();</script>';
  }

  return $html;
}

function ui_toast_container() {
  return '<div id="ui-toast-container" class="ui-toast-container"></div>';
}
