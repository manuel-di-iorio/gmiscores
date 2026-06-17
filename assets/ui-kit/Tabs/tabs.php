<?php

function ui_tabs($tabs, $options = []) {
  $class = $options['class'] ?? '';
  $activeTab = $options['active'] ?? ($tabs[0]['id'] ?? '');

  $html = '<div class="ui-tabs ' . htmlspecialchars($class) . '">';
  $html .= '<div class="ui-tabs__nav" role="tablist">';

  foreach ($tabs as $i => $tab) {
    $tabId = $tab['id'] ?? 'tab-' . $i;
    $isActive = $tabId === $activeTab;
    $icon = isset($tab['icon']) ? '<i class="' . htmlspecialchars($tab['icon']) . '"></i>' : '';

    $html .= '<button class="ui-tabs__btn' . ($isActive ? ' is-active' : '') . '"';
    $html .= ' role="tab" aria-selected="' . ($isActive ? 'true' : 'false') . '"';
    $html .= ' data-tab="' . htmlspecialchars($tabId) . '">';
    $html .= $icon . '<span>' . htmlspecialchars($tab['label'] ?? 'Tab') . '</span>';
    $html .= '</button>';
  }

  $html .= '</div>';
  $html .= '<div class="ui-tabs__panels">';

  foreach ($tabs as $i => $tab) {
    $tabId = $tab['id'] ?? 'tab-' . $i;
    $isActive = $tabId === $activeTab;

    $html .= '<div class="ui-tabs__panel' . ($isActive ? ' is-active' : '') . '"';
    $html .= ' role="tabpanel" id="panel-' . htmlspecialchars($tabId) . '">';
    $html .= $tab['content'] ?? '';
    $html .= '</div>';
  }

  $html .= '</div>';
  $html .= '</div>';

  return $html;
}
