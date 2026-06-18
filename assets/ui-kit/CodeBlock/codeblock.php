<?php

function ui_code($code, $options = []) {
  $class = $options['class'] ?? '';
  return '<pre class="bg-surface-code text-text-code rounded-xl overflow-x-auto text-sm leading-relaxed p-4 ' . htmlspecialchars($class) . '"><code>' . $code . '</code></pre>';
}
