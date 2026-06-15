<?php
require_once("lib/db.php");

http_response_code(404);

$view = "not-found";
$pageName = "404 - Pagina non trovata";
require_once("includes/layout.php");
