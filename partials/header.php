    <!-- CSS ফাইল লিংক -->
    <link rel="stylesheet" href="css/style.css">

    <?php
    // পেজ-স্পেসিফিক CSS লোড করার জন্য
    if (isset($currentPage)) {
        $page_css_file = 'css/' . $currentPage . '.css'; // যেমন: css/about.css, css/services.css
        if (file_exists($page_css_file)) {
            echo '<link rel="stylesheet" href="' . $page_css_file . '">';
        }
    }
    ?>

    <!-- বাংলা ফন্ট -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- (বাকি ফন্ট ও অন্যান্য লিঙ্ক আগের মতই থাকবে) -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <!-- Font Awesome CDN (যদি আইকন ব্যবহার করেন) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body>

    <!-- হেডার এবং মেনুবার -->
    <header class="header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php">মোঃ ছায়েম <small>(আইনজীবী)</small></a>
            </div>

     <nav class="navbar" id="main-nav">
    <ul>
        <li><a href="index.php" class="<?= ($currentPage == 'home') ? 'active' : '' ?>">হোম পেজ</a></li>
        <li><a href="services.php" class="<?= ($currentPage == 'services') ? 'active' : '' ?>">সেবাসমূহ</a></li>
        <!-- নতুন লিঙ্ক -->
        <li><a href="rulings.php" class="<?= ($currentPage == 'rulings') ? 'active' : '' ?>">আদালতের সিদ্ধান্ত</a></li>
        <li><a href="faq.php" class="<?= ($currentPage == 'faq') ? 'active' : '' ?>">প্রশ্নাবলী</a></li>
        <li><a href="about.php" class="<?= ($currentPage == 'about') ? 'active' : '' ?>">আমাদের সম্পর্কে</a></li>
        <li><a href="contact.php" class="<?= ($currentPage == 'contact') ? 'active' : '' ?>">যোগাযোগ</a></li>
    </ul>
    </nav>

            <!-- মোবাইল মেনু টগল বাটন -->
            <button class="mobile-menu-toggle" id="mobile-menu-toggle-btn" aria-label="মেনু খুলুন" aria-expanded="false">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
        </div>
    </header>

    <!-- প্রধান কন্টেন্ট এলাকা শুরু -->
    <main class="main-content">