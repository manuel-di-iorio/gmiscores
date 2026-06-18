<?php

function ui_paginator($currentPage, $totalPages, $options = []) {
  if ($totalPages <= 1) return '';

  $urlPattern = $options['url'] ?? '?page={page}';
  $prevLabel = $options['prevLabel'] ?? '&laquo;';
  $nextLabel = $options['nextLabel'] ?? '&raquo;';
  $firstLabel = $options['firstLabel'] ?? '&laquo;&laquo;';
  $lastLabel = $options['lastLabel'] ?? '&raquo;&raquo;';
  $maxButtons = $options['maxButtons'] ?? 3;
  $showFirstLast = $options['showFirstLast'] ?? true;
  $class = $options['class'] ?? '';

  $btnBase = 'inline-flex items-center justify-center min-w-[36px] px-3 py-2 border border-solid border-border-color rounded-md bg-input-bg text-sm text-[var(--text-color)] no-underline cursor-pointer select-none transition-colors duration-150';
  $btnHover = 'hover:bg-surface-offset hover:border-[var(--primary-color)]';
  $btnActive = '!bg-[var(--primary-color)] !text-white !border-[var(--primary-color)] font-semibold';
  $btnDisabled = '!bg-transparent !border-transparent !text-[var(--text-color-secondary)] cursor-not-allowed opacity-60';
  $ellipsis = 'px-1 text-[var(--text-color-secondary)]';

  $html = '<nav class="inline-flex items-center gap-1 flex-wrap ' . htmlspecialchars($class) . '">';

  // First page
  if ($showFirstLast && $currentPage > 0) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', '0', $urlPattern)) . '" class="' . $btnBase . ' ' . $btnHover . '" title="' . htmlspecialchars($firstLabel) . '">' . $firstLabel . '</a>';
  }

  // Previous page
  if ($currentPage > 0) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $currentPage - 1, $urlPattern)) . '" class="' . $btnBase . ' ' . $btnHover . '" rel="prev">' . $prevLabel . '</a>';
  } else {
    $html .= '<span class="' . $btnBase . ' ' . $btnDisabled . '">' . $prevLabel . '</span>';
  }

  // Build visible page range (0-indexed)
  $half = floor(($maxButtons - 1) / 2);
  $start = max(1, $currentPage - $half);
  $end = min($totalPages - 1, $currentPage + $half);

  // Adjust if we're near the edges to fill maxButtons slots
  $visibleCount = $end - $start + 1;
  if ($visibleCount < $maxButtons && $start > 1) {
    $start = max(1, $start - ($maxButtons - $visibleCount));
    $end = min($totalPages - 1, $start + $maxButtons - 1);
    $start = max(1, $end - $maxButtons + 1);
  } elseif ($visibleCount < $maxButtons && $end < $totalPages - 1) {
    $end = min($totalPages - 1, $end + ($maxButtons - $visibleCount));
    $start = max(1, $end - $maxButtons + 1);
  }

  // Page 1 (if not already in range)
  if ($start > 0) {
    if ($currentPage == 0) {
      $html .= '<button class="' . $btnBase . ' ' . $btnActive . '">1</button>';
    } else {
      $html .= '<a href="' . htmlspecialchars(str_replace('{page}', '0', $urlPattern)) . '" class="' . $btnBase . ' ' . $btnHover . '">1</a>';
    }
    if ($start > 1) {
      $html .= '<span class="' . $ellipsis . '">&hellip;</span>';
    }
  }

  // Middle pages
  for ($i = $start; $i <= $end; $i++) {
    $pageUrl = str_replace('{page}', $i, $urlPattern);
    if ($i == $currentPage) {
      $html .= '<button class="' . $btnBase . ' ' . $btnActive . '">' . ($i + 1) . '</button>';
    } else {
      $html .= '<a href="' . htmlspecialchars($pageUrl) . '" class="' . $btnBase . ' ' . $btnHover . '">' . ($i + 1) . '</a>';
    }
  }

  // Last page (if not already in range)
  if ($end < $totalPages - 1) {
    if ($end < $totalPages - 2) {
      $html .= '<span class="' . $ellipsis . '">&hellip;</span>';
    }
    if ($currentPage == $totalPages - 1) {
      $html .= '<button class="' . $btnBase . ' ' . $btnActive . '">' . $totalPages . '</button>';
    } else {
      $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $totalPages - 1, $urlPattern)) . '" class="' . $btnBase . ' ' . $btnHover . '">' . $totalPages . '</a>';
    }
  }

  // Next page
  if ($currentPage < $totalPages - 1) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $currentPage + 1, $urlPattern)) . '" class="' . $btnBase . ' ' . $btnHover . '" rel="next">' . $nextLabel . '</a>';
  } else {
    $html .= '<span class="' . $btnBase . ' ' . $btnDisabled . '">' . $nextLabel . '</span>';
  }

  // Last page button
  if ($showFirstLast && $currentPage < $totalPages - 1) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $totalPages - 1, $urlPattern)) . '" class="' . $btnBase . ' ' . $btnHover . '" title="' . htmlspecialchars($lastLabel) . '">' . $lastLabel . '</a>';
  }

  $html .= '</nav>';
  return $html;
}
