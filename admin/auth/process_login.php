<?php
// admin/auth/process_login.php
require_once '../includes/functions.php';
require_once '../../config/db.php'; // ডেটাবেস কানেকশন

// Method ও CSRF টোকেন চেক
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['login_error'] = 'অবৈধ অনুরোধ।';
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? ''; // পাসওয়ার্ড ট্রিম করা উচিত নয়

if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = 'ইউজারনেম এবং পাসওয়ার্ড পূরণ করুন।';
    header('Location: login.php');
    exit;
}

try {
    $sql = "SELECT id, username, password_hash FROM admins WHERE username = :username LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $admin = $stmt->fetch();

    // অ্যাডমিন পাওয়া গেলে এবং পাসওয়ার্ড মিললে
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // পাসওয়ার্ড সঠিক! সেশন তৈরি করা
        session_regenerate_id(true); // **অত্যন্ত গুরুত্বপূর্ণ**
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        unset($_SESSION['csrf_token']); // লগইন সফল হলে টোকেন রিসেট

        // ড্যাশবোর্ডে রিডাইরেক্ট
        header('Location:/chayem-lawyer-website/admin/index.php'); // পাথ সঠিক করুন
        exit;
    } else {
        // লগইন ব্যর্থ
        $_SESSION['login_error'] = 'ইউজারনেম বা পাসওয়ার্ড সঠিক নয়।';
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    error_log("Login Processing Error: " . $e->getMessage());
    $_SESSION['login_error'] = 'লগইন করার সময় একটি অপ্রত্যাশিত সমস্যা হয়েছে।';
    header('Location: login.php');
    exit;
}
?>