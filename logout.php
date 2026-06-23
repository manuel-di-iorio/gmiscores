<?php
require_once("lib/config.php");
session_start();
session_destroy();
setcookie("user", "", time() - 3600, "/", "", false, true);
setcookie("selected_team_id", "", time() - 3600, "/");
header("Location: /");
