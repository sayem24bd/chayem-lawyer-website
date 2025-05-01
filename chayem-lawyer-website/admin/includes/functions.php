<?php
// admin/includes/functions.php

// --- সেশন শুরু ---
// সেশন শুরু করা (নিরাপত্তার জন্য) - এটি অবশ্যই অন্য কোনো আউটপুটের আগে হতে হবে
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true, // JS থেকে কুকি অ্যাক্সেস বন্ধ
        // 'cookie_secure' => isset($_SERVER['HTTPS']), // শুধুমাত্র HTTPS এ কুকি পাঠাবে (প্রোডাকশনে দরকার)
        'use_strict_mode' => true // Strict mode সেশন আইডি গ্রহণ করবে
    ]);
}

// --- Helper ফাংশন ---

// লগইন স্ট্যাটাস চেক ফাংশন
function is_admin_logged_in(): bool {
    // সেশনে ভ্যারিয়েবলটি সেট আছে কিনা এবং তার মান true কিনা চেক করা
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// সুরক্ষিত পেজের শুরুতে কল করার ফাংশন
function require_admin_login(): void {
    // যদি লগইন করা না থাকে
    if (!is_admin_logged_in()) {
        // লগইন পেজের পাথ নির্ধারণ (আপনার ফোল্ডার স্ট্রাকচার অনুযায়ী অ্যাডজাস্ট করুন)
        // এই পাথটি functions.php ফাইলের লোকেশনের সাপেক্ষে
        $login_page = '../auth/login.php'; // functions.php থেকে এক ধাপ উপরে গিয়ে auth ফোল্ডারের login.php

        // লগইন পেজে রিডাইরেক্ট করা
        header('Location: ' . $login_page);
        // রিডাইরেক্টের পর স্ক্রিপ্ট এক্সিকিউশন বন্ধ করা অপরিহার্য
        exit;
    }
}

// CSRF টোকেন তৈরি করার ফাংশন
function generate_csrf_token(): string {
    // যদি সেশনে টোকেন না থাকে বা খালি থাকে, তবে নতুন তৈরি করা
    if (empty($_SESSION['csrf_token'])) {
        try {
            // ক্রিপ্টোগ্রাফিক্যালি নিরাপদ র‍্যান্ডম বাইট তৈরি করে হেক্সাডেসিমেলে রূপান্তর
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (Exception $e) {
            // random_bytes ফেইল করলে এরর লগ করা (খুব বিরল)
            error_log("Error generating CSRF token: " . $e->getMessage());
            // একটি ফলব্যাক বা এরর হ্যান্ডেল করা যেতে পারে
            // আপাতত খালি স্ট্রিং রিটার্ন করা হচ্ছে, যা ভেরিফিকেশনে ফেইল করবে
             return '';
        }
    }
    // সেশনে থাকা টোকেন রিটার্ন করা
    return $_SESSION['csrf_token'];
}

// CSRF টোকেন ভেরিফাই করার ফাংশন
function verify_csrf_token(string $token): bool {
    // ব্যবহারকারীর দেওয়া টোকেন বা সেশনে থাকা টোকেন খালি কিনা চেক করা
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false; // টোকেন না থাকলে ভেরিফিকেশন ফেইল
    }
    // Timing Attack প্রতিরোধের জন্য hash_equals ব্যবহার করে টোকেন মেলানো
    return hash_equals($_SESSION['csrf_token'], $token);
}

// HTML আউটপুট পরিষ্কার করার ফাংশন (XSS প্রতিরোধ)
function e(string $string): string {
    // htmlspecialchars ব্যবহার করে বিশেষ HTML ক্যারেক্টারগুলোকে এনটিটিতে রূপান্তর করা
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// এই ফাইলের শেষে ?> ট্যাগ নেই, যা ভালো প্র্যাকটিস।