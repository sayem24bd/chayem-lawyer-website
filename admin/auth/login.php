<?php
// admin/auth/login.php

// functions.php লোড করা (পাথ আপনার স্ট্রাকচার অনুযায়ী ঠিক করুন)
// __DIR__ ব্যবহার করলে বর্তমান ফাইলের ডিরেক্টরি থেকে পাথ শুরু হয়
require_once __DIR__ . '/../includes/functions.php';

// যদি লগইন করা থাকে, ড্যাশবোর্ডে পাঠাও
if (is_admin_logged_in()) {
    // Absolute path ব্যবহার করা ভালো হতে পারে যদি Base URL সেট করা থাকে
    // অথবা রিলেটিভ পাথ ঠিকভাবে কাজ করলে সেটিই রাখুন
    header('Location: /chayem-lawyer-website/admin/index.php'); // Root থেকে পাথ শুরু
    exit;
}

// সেশন থেকে লগইন এরর মেসেজ নেওয়া (যদি থাকে)
$login_error = $_SESSION['login_error'] ?? '';
// মেসেজ দেখানোর পর সেশন থেকে মুছে ফেলা আবশ্যক
if (isset($_SESSION['login_error'])) {
    unset($_SESSION['login_error']);
}

// CSRF টোকেন তৈরি করা
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>অ্যাডমিন লগইন</title>
    <!-- CSS ফাইলের পাথ নিশ্চিত করুন -->
    <link rel="stylesheet" href="/admin/assets/css/admin_style.css"> <!-- Root থেকে পাথ -->
    <style>
        /* লগইন পেজের স্টাইলগুলো admin_style.css এ সরানো ভালো */
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f2f5; }
        .login-container { background: #fff; padding: 2rem 2.5rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .login-container h1 { text-align: center; margin-bottom: 1.5rem; color: #333; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input { width: 100%; padding: 0.7rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; box-sizing: border-box;}
        .login-button { display: block; width: 100%; padding: 0.8rem; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 1.1rem; cursor: pointer; transition: background-color 0.3s ease; }
        .login-button:hover { background-color: #0056b3; }
        .error-message { color: #dc3545; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 0.7rem; border-radius: 4px; margin-bottom: 1rem; text-align: center; font-size: 0.9rem;}
    </style>
</head>
<body>
    <div class="login-container">
        <h1>অ্যাডমিন লগইন</h1>
        <?php if (!empty($login_error)): ?>
            <div class="error-message"><?= e($login_error) // htmlspecialchars ব্যবহার ?></div>
        <?php endif; ?>
        <!-- ফর্ম সাবমিট হবে process_login.php তে (একই ফোল্ডারে) -->
        <form method="POST" action="process_login.php">
            <!-- CSRF টোকেন -->
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) // htmlspecialchars ব্যবহার ?>">

            <div class="form-group">
                <label for="username">ইউজারনেম:</label>
                <input type="text" id="username" name="username" required autocomplete="username"> <!-- autocomplete যোগ করা -->
            </div>
            <div class="form-group">
                <label for="password">পাসওয়ার্ড:</label>
                <input type="password" id="password" name="password" required autocomplete="current-password"> <!-- autocomplete যোগ করা -->
            </div>
            <button type="submit" class="login-button">লগইন করুন</button>
        </form>
    </div>
</body>
</html>