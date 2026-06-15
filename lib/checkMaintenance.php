<?php
if (!empty($config["maintenance"])) {
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
        // Allow GET requests to pass through, we just show the banner
        // But POST requests and destructive actions are blocked
    } else {
        $msg = isset($config["maintenanceMessage"]) ? $config["maintenanceMessage"] : "Il portale è in manutenzione.";
        header("Location: games.php?error=" . urlencode($msg));
        exit;
    }
}
