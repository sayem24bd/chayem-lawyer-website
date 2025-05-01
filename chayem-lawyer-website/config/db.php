<?php
// config/db.php

// ডেটাবেস কানেকশন প্যারামিটার
define('DB_HOST', 'localhost'); // আপনার ডেটাবেস হোস্ট (সাধারণত localhost)
define('DB_NAME', 'config_db'); // আপনার ডেটাবেসের নাম
define('DB_USER', 'root'); // আপনার ডেটাবেস ইউজারনেম
define('DB_PASS', ''); // আপনার ডেটাবেস পাসওয়ার্ড
define('DB_CHARSET', 'utf8mb4');

// PDO DSN (Data Source Name)
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// PDO অপশনস
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // এরর মোড এক্সেপশন
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // ডিফল্ট ফেচ মোড অ্যাসোসিয়েটিভ অ্যারে
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Emulated prepares বন্ধ করা ভালো
];

// PDO কানেকশন তৈরির চেষ্টা
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (\PDOException $e) {
    // কানেকশন ফেইল হলে এরর দেখানো (ডেভেলপমেন্টে)
    // প্রোডাকশনে এরর লগ করা উচিত, সরাসরি দেখানো নয়
    error_log("Database Connection Error: " . $e->getMessage());
     // ব্যবহারকারীকে একটি সাধারণ বার্তা দেখানো যেতে পারে
     die("দুঃখিত, ডেটাবেসের সাথে সংযোগ স্থাপন করা সম্ভব হচ্ছে না। অনুগ্রহ করে稍后再 চেষ্টা করুন।");
    // throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// $pdo ভ্যারিয়েবলটি এখন অন্য ফাইলে require করে ব্যবহার করা যাবে
?>