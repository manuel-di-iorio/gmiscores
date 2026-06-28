<?php
require_once("../../lib/insertScore.php");

$syncHandlers = [
  'score.submit' => 'process_score_submission',
];
