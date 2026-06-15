<?php
function check_maintenance() {
    global $config;
    if (!empty($config["maintenance"])) {
        $msg = isset($config["maintenanceMessage"]) ? $config["maintenanceMessage"] : "Il portale è in manutenzione.";
        header("Location: games.php?error=" . urlencode($msg));
        exit;
    }
}
