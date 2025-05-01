<?php
// admin/auth/logout.php
require_once '../includes/functions.php';

// সেশন ডেটা মুছে ফেলা
$_SESSION = array();

// সেশন কুকি ডিলিট করা (যদি থাকে)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// সেশন ধ্বংস করা
session_destroy();

// লগইন পেজে রিডাইরেক্ট
header('Location: login.php');
exit;
?>