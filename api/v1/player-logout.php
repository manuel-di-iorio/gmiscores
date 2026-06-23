<?php
require_once("../../lib/db.php");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

setcookie("user", "", time() - 3600, "/", "", false, true);

echo json_encode(["status" => 200, "message" => "Logged out"]);
