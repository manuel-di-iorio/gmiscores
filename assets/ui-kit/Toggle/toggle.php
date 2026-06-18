<?php

function ui_toggle($active, $url, $options = []) {
  $labelOn = $options['labelOn'] ?? 'Enable';
  $labelOff = $options['labelOff'] ?? 'Disable';
  $class = $options['class'] ?? '';

  $size = $options['size'] ?? 'md';
  $sizes = [
    'sm' => ['w-[30px] h-[17px]', 'w-[16px] h-[16px] top-[0.5px]', 'left-[1px]', 'translate-x-[13px]'],
    'md' => ['w-[35px] h-[19px]', 'w-[18px] h-[18px] top-[0.5px]', 'left-[1px]', 'translate-x-[15px]'],
    'lg' => ['w-[42px] h-[23px]', 'w-[22px] h-[22px] top-[0.5px]', 'left-[1px]', 'translate-x-[19px]'],
  ];
  list($wh, $knobSize, $knobPos, $translate) = $sizes[$size] ?? $sizes['md'];

  $bg = $active ? 'bg-[var(--toggle-bg--checked,#2196F3)]' : 'bg-[var(--toggle-bg,#ccc)]';
  $knobActive = $active ? $translate : 'translate-x-0';

  $html = '<a href="' . htmlspecialchars($url) . '" title="' . ($active ? htmlspecialchars($labelOn) : htmlspecialchars($labelOff)) . '" class="ui-toggle inline-flex items-center justify-center no-underline ' . htmlspecialchars($class) . '">';
  $html .= '<span class="relative inline-block ' . $wh . ' ' . $bg . ' rounded-full transition-colors duration-[400ms]">';
  $html .= '<span class="absolute ' . $knobSize . ' ' . $knobPos . ' rounded-full bg-white shadow-sm transition-transform duration-[400ms] ' . $knobActive . '"></span>';
  $html .= '</span>';
  $html .= '</a>';

  return $html;
}
