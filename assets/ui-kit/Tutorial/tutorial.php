<?php

require_once __DIR__ . '/config.php';

function ui_tutorial_render() {
  global $user, $view;

  if (!isset($user) || empty($user)) {
    return '';
  }

  $tutorialConfig = require __DIR__ . '/config.php';
  $steps = $tutorialConfig['steps'];
  $totalSteps = count($steps);
  $currentStepId = $user['tutorial_progress'] ?? null;
  $tutorialSkipped = (int)($user['tutorial_skipped'] ?? 0);

  if ($tutorialSkipped || ($currentStepId === '__complete__')) {
    return '';
  }

  $currentPage = $view ?? '';
  $currentStepIndex = -1;

  if ($currentStepId) {
    foreach ($steps as $i => $step) {
      if ($step['id'] === $currentStepId) {
        $currentStepIndex = $i;
        break;
      }
    }
  } else {
    $currentStepIndex = 0;
  }

  $html = '<div id="ui-tutorial-root" data-tutorial-steps="' . htmlspecialchars(json_encode(array_map(function($s) {
    return [
      'id'     => $s['id'],
      'page'   => $s['page'],
      'target' => $s['target'],
      'title'  => $s['title'],
      'desc'   => $s['desc'],
      'pos'    => $s['pos'],
      'arrow'  => $s['arrow'] ?? true,
      'final'  => $s['final'] ?? false,
    ];
  }, $steps))) . '" '
  . 'data-tutorial-current="' . htmlspecialchars($currentStepId ?? '') . '" '
  . 'data-tutorial-page="' . htmlspecialchars($currentPage) . '" '
  . 'data-tutorial-total="' . $totalSteps . '" '
  . 'data-tutorial-index="' . $currentStepIndex . '" '
  . 'data-tutorial-active="' . ($currentStepIndex >= 0 ? '1' : '0') . '" '
  . 'data-tutorial-skip="' . htmlspecialchars(__('tutorial_skip')) . '" '
  . 'data-tutorial-back="' . htmlspecialchars(__('tutorial_back')) . '" '
  . 'data-tutorial-next="' . htmlspecialchars(__('tutorial_next')) . '" '
  . 'data-tutorial-finish="' . htmlspecialchars(__('tutorial_finish')) . '" '
  . 'data-tutorial-get-started="' . htmlspecialchars(__('tutorial_get_started')) . '" '
  . 'data-tutorial-waiting-title="' . htmlspecialchars(__('tutorial_waiting_title')) . '" '
  . 'data-tutorial-waiting-desc="' . htmlspecialchars(__('tutorial_waiting_desc')) . '" '
  . 'data-csrf="' . htmlspecialchars(csrf_token()) . '" '
  . '></div>';

  return $html;
}

function ui_tutorial_should_start() {
  global $user, $view;

  if (!isset($user) || empty($user)) {
    return false;
  }

  $tutorialSkipped = (int)($user['tutorial_skipped'] ?? 0);
  $currentStepId = $user['tutorial_progress'] ?? null;

  if ($tutorialSkipped || ($currentStepId === '__complete__')) {
    return false;
  }

  return true;
}
