<?php

function ui_modal($id, $options = []) {
  $title = $options['title'] ?? '';
  $content = $options['content'] ?? '';
  $footer = $options['footer'] ?? '';
  $size = $options['size'] ?? 'md';
  $class = $options['class'] ?? '';
  $closeButton = $options['close_button'] ?? true;

  $sizeWidths = ['sm' => 'max-w-[400px]', 'md' => 'max-w-[540px]', 'lg' => 'max-w-[700px]', 'xl' => 'max-w-[900px]'];

  $widthClass = $sizeWidths[$size] ?? $sizeWidths['md'];

  $html = '<div id="' . htmlspecialchars($id) . '" class="ui-modal-overlay items-center justify-center bg-black/50" onclick="if (event.target === this) closeModal(\'' . htmlspecialchars($id) . '\')">';
  $html .= '<div class="ui-modal bg-surface-card rounded-2xl shadow-2xl w-full ' . $widthClass . ' flex flex-col overflow-hidden ' . htmlspecialchars($class) . '">';

  if ($title) {
    $html .= '<div class="flex items-center justify-between px-6 py-4 border-0 border-b border-solid border-border-color flex-shrink-0">';
    $html .= '<h3 class="font-semibold text-[1.1rem] text-headings m-0">' . htmlspecialchars($title) . '</h3>';
    if ($closeButton) {
      $html .= '<button type="button" class="w-9 h-9 flex items-center justify-center border-none bg-transparent text-[1.6rem] text-[var(--text-color-secondary)] cursor-pointer rounded-lg transition-colors duration-150 hover:bg-surface-offset hover:text-[var(--text-color)] p-0 leading-none" onclick="closeModal(\'' . htmlspecialchars($id) . '\')">&times;</button>';
    }
    $html .= '</div>';
  }

  $html .= '<div class="p-6 overflow-y-auto overflow-x-hidden flex-1 text-[var(--text-color)]">' . $content . '</div>';

  if ($footer) {
    $footerClasses = 'flex items-center gap-3 px-6 py-4 border-0 border-t border-solid border-border-color flex-shrink-0' . (!empty($options['footer_right']) ? ' justify-end' : '');
    $html .= '<div class="' . $footerClasses . '">' . $footer . '</div>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}
