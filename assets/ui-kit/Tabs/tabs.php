<?php

function ui_tabs($tabs, $options = []) {
  $class = $options['class'] ?? '';
  $activeTab = $options['active'] ?? ($tabs[0]['id'] ?? '');

  $html = '<div class="ui-tabs flex flex-col gap-0 ' . htmlspecialchars($class) . '">';
  $html .= '<div class="ui-tabs__nav flex gap-0.5 overflow-x-auto relative" role="tablist">';

  foreach ($tabs as $i => $tab) {
    $tabId = $tab['id'] ?? 'tab-' . $i;
    $isActive = $tabId === $activeTab;
    $icon = isset($tab['icon']) ? '<i class="' . htmlspecialchars($tab['icon']) . '"></i>' : '';

    $html .= '<button class="ui-tabs__btn inline-flex items-center gap-2 px-5 py-3 text-sm font-medium bg-transparent border-none cursor-pointer whitespace-nowrap transition-colors duration-200 hover:bg-surface-offset relative font-inherit' . ($isActive ? ' is-active' : '') . '"';
    $html .= ' role="tab" aria-selected="' . ($isActive ? 'true' : 'false') . '"';
    $html .= ' data-tab="' . htmlspecialchars($tabId) . '">';
    $html .= $icon . '<span>' . htmlspecialchars($tab['label'] ?? 'Tab') . '</span>';
    $html .= '</button>';
  }

  $html .= '</div>';
  $html .= '<div class="ui-tabs__panels pt-5">';

  foreach ($tabs as $i => $tab) {
    $tabId = $tab['id'] ?? 'tab-' . $i;
    $isActive = $tabId === $activeTab;

    $html .= '<div class="ui-tabs__panel' . ($isActive ? ' is-active' : '') . '"';
    $html .= ' role="tabpanel" id="panel-' . htmlspecialchars($tabId) . '"';
    if (isset($tab['url'])) {
      $html .= ' data-url="' . htmlspecialchars($tab['url']) . '"';
      $html .= ' data-loaded="false"';
    }
    $html .= '>';
    $html .= $tab['content'] ?? '';
    $html .= '</div>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}
