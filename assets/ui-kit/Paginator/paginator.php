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

  $html = '<nav class="pagination-bar ' . htmlspecialchars($class) . '">';

  // First page
  if ($showFirstLast && $currentPage > 0) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', '0', $urlPattern)) . '" class="pagination-btn" title="' . htmlspecialchars($firstLabel) . '">' . $firstLabel . '</a>';
  }

  // Previous page
  if ($currentPage > 0) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $currentPage - 1, $urlPattern)) . '" class="pagination-btn" rel="prev">' . $prevLabel . '</a>';
  } else {
    $html .= '<span class="pagination-btn pagination-disabled">' . $prevLabel . '</span>';
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
      $html .= '<button class="pagination-btn pagination-active">1</button>';
    } else {
      $html .= '<a href="' . htmlspecialchars(str_replace('{page}', '0', $urlPattern)) . '" class="pagination-btn">1</a>';
    }
    if ($start > 1) {
      $html .= '<span class="pagination-ellipsis">&hellip;</span>';
    }
  }

  // Middle pages
  for ($i = $start; $i <= $end; $i++) {
    $pageUrl = str_replace('{page}', $i, $urlPattern);
    if ($i == $currentPage) {
      $html .= '<button class="pagination-btn pagination-active">' . ($i + 1) . '</button>';
    } else {
      $html .= '<a href="' . htmlspecialchars($pageUrl) . '" class="pagination-btn">' . ($i + 1) . '</a>';
    }
  }

  // Last page (if not already in range)
  if ($end < $totalPages - 1) {
    if ($end < $totalPages - 2) {
      $html .= '<span class="pagination-ellipsis">&hellip;</span>';
    }
    if ($currentPage == $totalPages - 1) {
      $html .= '<button class="pagination-btn pagination-active">' . $totalPages . '</button>';
    } else {
      $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $totalPages - 1, $urlPattern)) . '" class="pagination-btn">' . $totalPages . '</a>';
    }
  }

  // Next page
  if ($currentPage < $totalPages - 1) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $currentPage + 1, $urlPattern)) . '" class="pagination-btn" rel="next">' . $nextLabel . '</a>';
  } else {
    $html .= '<span class="pagination-btn pagination-disabled">' . $nextLabel . '</span>';
  }

  // Last page button
  if ($showFirstLast && $currentPage < $totalPages - 1) {
    $html .= '<a href="' . htmlspecialchars(str_replace('{page}', $totalPages - 1, $urlPattern)) . '" class="pagination-btn" title="' . htmlspecialchars($lastLabel) . '">' . $lastLabel . '</a>';
  }

  $html .= '</nav>';
  return $html;
}
