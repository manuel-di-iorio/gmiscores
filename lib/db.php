<?php
require_once("config.php");
require_once("crypt.php");
require_once("getTheme.php");
require_once("getLang.php");
require_once("apiReplyError.php");
require_once("tables.php");
require_once(__DIR__ . "/../models/User.php");

// Enable the MYSQL Error strict reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Connect to the database
try {
    $db = new mysqli($config["dbHost"], $config["dbUsername"], $config["dbPassword"], $config["dbDatabase"]);
} catch (Exception $e ) {
    api_reply_error("Error connecting to the database.", "DatabaseConnectionError", 500);
}

$db->set_charset("utf8mb4");

// Get the logged in user from the remember me cookie (skip for API endpoints)
if (!isset($apiMode) && !isset($user) && isset($_COOKIE["user"])) {
    try {
        $cookieUser = json_decode(aes_decrypt($_COOKIE["user"], true), true);
        $user = User::getById($cookieUser["id"])->fetch_assoc();
        
        // Logout the user if it does not exists anymore
        if (is_null($user)) {
            header("Location: /logout.php");
            exit;
        }

        // Approval check
        $uri = $_SERVER["REQUEST_URI"];
        $uriPath = parse_url($uri, PHP_URL_PATH);
        if (!$user["approved"]) {
            if ($uriPath !== "/approval.php" && $uriPath !== "/" && $uriPath !== "/documentation.php" && $uriPath !== "/admin.php") {
                header("Location: approval.php");
                exit;
            }
        } else {
            if ($uriPath === "/approval.php") {
                header("Location: /home.php");
                exit;
            }
        }
    } catch (Exception $e) {
        setcookie("user", "", time() - 3600, "/", "", false, true);
        exit("Session error.");
    }
}

/** Execute a prepared statement query */
function exec_query(string $sql, $params=NULL) {
    global $db;
    
    try {  
        if (is_null($params)) {
            $result = $db->query($sql);
        } else {
            $stmt = $db->prepare($sql);

            // Bind the params
            $refs = array();
            foreach ($params as $key => $value) {
                $refs[$key] = &$params[$key];
            }
            call_user_func_array(array($stmt, 'bind_param'), $refs);

            $stmt->execute(); 
            $result = $stmt->get_result();
            $stmt->close();
        }
        
        return $result;

    } catch (Exception $e) {
      if (isset($migrationMode)) {
        throw $e;
      }
      if (isset($config) && $config["appEnv"] !== "local") {
        api_reply_error("An error occured while processing the request.", "InternalServerError", 500);
      } else {
        api_reply_error($e->getMessage(), "InternalServerError", 500);
      }
    }
}

/**
 * Escape a string to be rendered inside a js function call
 */
function escapeChars(string $string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
