<?php
function ui_button($label, $variant = 'primary', $size = 'md', $options = []) {
  $icon = $options['icon'] ?? null;
  $iconRight = $options['iconRight'] ?? null;
  $disabled = $options['disabled'] ?? false;
  $loading = $options['loading'] ?? false;
  $type = $options['type'] ?? 'button';
  $href = $options['href'] ?? null;
  $class = $options['class'] ?? '';
  $attrs = $options['attrs'] ?? [];
  $full = $options['full'] ?? false;

  $base = 'ui-btn inline-flex items-center justify-center font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none';

  $variants = [
    'primary'   => 'bg-blue-600 text-white hover:bg-blue-700 hover:text-white active:bg-blue-800 active:text-white focus:ring-blue-500',
    'secondary' => 'bg-gray-200 text-gray-900 hover:bg-gray-300 hover:text-gray-900 active:bg-gray-400 active:text-gray-900 focus:ring-gray-400',
    'danger'    => 'bg-red-600 text-white hover:bg-red-700 hover:text-white active:bg-red-800 active:text-white focus:ring-red-500',
    'ghost'     => 'bg-transparent text-gray-700 hover:bg-gray-100 hover:text-gray-700 active:bg-gray-200 active:text-gray-700 focus:ring-gray-400',
    'success'   => 'bg-green-600 text-white hover:bg-green-700 hover:text-white active:bg-green-800 active:text-white focus:ring-green-500',
    'warning'   => 'bg-amber-500 text-white hover:bg-amber-600 hover:text-white active:bg-amber-700 active:text-white focus:ring-amber-400',
  ];

  $sizes = [
    'sm' => 'px-3 py-1.5 text-sm gap-1.5',
    'md' => 'px-4 py-2 text-base gap-2',
    'lg' => 'px-6 py-3 text-lg gap-2.5',
  ];

  $classes = trim("$base {$variants[$variant]} {$sizes[$size]} " . ($full ? 'w-full' : '') . " $class");

  $loadingHtml = $loading ? '<svg class="animate-spin -ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> ' : '';

  $iconHtml = $icon ? "<i class=\"$icon\"></i> " : '';
  $iconRightHtml = $iconRight ? " <i class=\"$iconRight\"></i>" : '';

  $content = $loadingHtml . $iconHtml . '<span>' . htmlspecialchars($label) . '</span>' . $iconRightHtml;

  $attrStr = '';
  foreach ($attrs as $key => $value) {
    $attrStr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
  }

  if ($disabled) {
    $attrStr .= ' disabled';
  }

  if ($href && !$disabled) {
    return "<a href=\"" . htmlspecialchars($href) . "\" class=\"$classes\"$attrStr>$content</a>";
  }

  return "<button type=\"" . htmlspecialchars($type) . "\" class=\"$classes\"$attrStr>$content</button>";
}
