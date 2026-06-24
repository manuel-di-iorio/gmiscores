<?php
require_once("lib/db.php");
$view = "documentation";
$pageName = __('docs_page_title');
$baseApiPath = $config["host"] . "/api/v1";
require_once("includes/layout.php");
