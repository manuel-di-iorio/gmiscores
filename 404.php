<?php
require_once("lib/db.php");

http_response_code(404);

$view = "not-found";
$pageName = __("not_found_title");
$pageDesc = __("not_found_desc");
require_once("includes/layout.php");
