<?php
// partials/header.php

// $pageTitle, $currentPage ভ্যারিয়েবল আগে সেট করতে হবে
if (!isset($pageTitle)) { $pageTitle = "মোঃ ছায়েম - আইনজীবী"; }
if (!isset($currentPage)) { $currentPage = "home"; }
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle); ?></title>

    <!-- Base CSS -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Page Specific CSS -->
    <?php
    $page_css_file = 'css/' . $currentPage . '.css';
    if (file_exists($page_css_file)) {
        echo '<link rel="stylesheet" href="' . $page_css_file . '">';
    }
    ?>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

</head>
<body>
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php">মোঃ ছায়েম <small>(আইনজীবী)</small></a>
            </div>
            <nav class="navbar" id="main-nav">
                <ul>
                    <li><a href="index.php" class="<?= ($currentPage == 'home') ? 'active' : '' ?>">হোম পেজ</a></li>
                    <li><a href="services.php" class="<?= ($currentPage == 'services') ? 'active' : '' ?>">সেবাসমূহ</a></li>
                    <li><a href="rulings.php" class="<?= ($currentPage == 'rulings') ? 'active' : '' ?>">আদালতের সিদ্ধান্ত</a></li>
                    <li><a href="faq.php" class="<?= ($currentPage == 'faq') ? 'active' : '' ?>">প্রশ্নাবলী</a></li>
                    <li><a href="about.php" class="<?= ($currentPage == 'about') ? 'active' : '' ?>">আমাদের সম্পর্কে</a></li>
                    <li><a href="contact.php" class="<?= ($currentPage == 'contact') ? 'active' : '' ?>">যোগাযোগ</a></li>
                </ul>
            </nav>
            <button class="mobile-menu-toggle" id="mobile-menu-toggle-btn" aria-label="মেনু খুলুন" aria-expanded="false">
                <span class="bar"></span><span class="bar"></span><span class="bar"></span>
            </button>
        </div>
    </header>
    <main class="main-content">