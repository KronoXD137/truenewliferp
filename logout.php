<?php
// logout.php
session_start();
require_once 'config.php';

if (isset($_SESSION['user_uid'])) {
    // marque offline no BD
    $stmt = $pdo->prepare('UPDATE TC_accounts SET isOnline = 0 WHERE UID = :uid');
    $stmt->execute([':uid' => $_SESSION['user_uid']]);
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
header('Location: login.html');
exit;
