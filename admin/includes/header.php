<?php
// admin/includes/header.php

require_once __DIR__ . '/functions.php';
require_admin_login();

global $adminPageTitle, $currentPage;

if (!isset($adminPageTitle)) { $adminPageTitle = 'অ্যাডমিন ড্যাশবোর্ড'; }
if (!isset($currentPage)) { $currentPage = ''; }

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="bn" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminPageTitle) ?> - অ্যাডমিন প্যানেল</title>

    <!-- CSS Links -->
    <link rel="stylesheet" href="/chayem-lawyer-website/admin/assets/css/admin_style.css">
    <?php if (isset($currentPage) && $currentPage === 'rulings' && basename($_SERVER['PHP_SELF']) === 'edit.php'): ?>
        <link rel="stylesheet" href="../css/ruling-edit-style.css">
    <?php endif; ?>
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <?= $additional_head_content ?? '' ?>
</head>
<body>
    <div class="admin-wrapper">
        <header class="admin-header">
            <div class="header-container">
                <div class="logo">অ্যাডমিন প্যানেল</div>
                <div class="header-controls">
                    <div class="theme-toggle">
                        <button id="themeToggle" class="btn btn-sm btn-theme">
                            <i class="fas fa-moon"></i>
                        </button>
                    </div>
                    <div class="user-info">
                        স্বাগতম, <?= e($admin_username) ?> |
                        <a href="/chayem-lawyer-website/admin/auth/logout.php">লগআউট</a>
                    </div>
                </div>
            </div>
        </header>
        
        <nav class="admin-nav">
            <ul>
                <li><a href="/chayem-lawyer-website/admin/index.php" class="<?= ($currentPage == 'dashboard') ? 'active' : '' ?>">ড্যাশবোর্ড</a></li>
                <li><a href="/chayem-lawyer-website/admin/rulings/" class="<?= ($currentPage == 'rulings') ? 'active' : '' ?>">সিদ্ধান্ত ম্যানেজ</a></li>
                <li><a href="/chayem-lawyer-website/rulings.php" target="_blank">সাইট দেখুন</a></li>
            </ul>
        </nav>

        <main class="admin-content">
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="message success animate__animated animate__fadeInDown">'.e($_SESSION['success_message']).'</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="message error animate__animated animate__fadeInDown">'.e($_SESSION['error_message']).'</div>';
                unset($_SESSION['error_message']);
            }
            ?>
            <!-- Main Content Starts Here -->