<?php
$pageTitle = "মোঃ ছায়েম - আইনজীবী | হোম"; // এই পেজের টাইটেল
$currentPage = "home"; // এই পেজের আইডি
require 'partials/header.php'; // হেডার অংশ যুক্ত করা
?>

    <!-- হিরো সেকশন -->
    <section class="hero hero-bg"> <!-- ব্যাকগ্রাউন্ড ইমেজের জন্য একটি ক্লাস যোগ করা হলো -->
        <div class="hero-overlay"></div> <!-- ওভারলে যোগ করা হলো -->
        <div class="container hero-content"> <!-- কন্টেন্টের জন্য আলাদা div -->
            <h1>আইনি সমাধানে আপনার নির্ভরযোগ্য ঠিকানা।</h1>
            <p class="intro-text">
                আমি মোঃ ছায়েম, আপনার সকল প্রকার আইনি প্রয়োজনে পেশাদার পরামর্শ এবং নির্ভরযোগ্য সহায়তা প্রদানের জন্য প্রতিশ্রুতিবদ্ধ।
            </p>
            <a href="contact.php" class="cta-button hero-button">পরামর্শ নিন</a>
        </div>
    </section>

    <!-- আপনি চাইলে হোমপেজে অন্য কোনো সংক্ষিপ্ত সেকশন (যেমন কেন আমাদের বেছে নিবেন?) যোগ করতে পারেন -->
    <section class="why-choose-us section-padding">
        <div class="container">
            <h2>কেন আমাদের বেছে নিবেন?</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <i class="feature-icon">[আইকন ক্লাস]</i> <!-- এখানে একটি আইকন ফন্ট (যেমন FontAwesome) ব্যবহার করতে পারেন -->
                    <h3>অভিজ্ঞতা</h3>
                    <p>দীর্ঘদিনের আইনি অভিজ্ঞতা ও বিভিন্ন মামলায় সফলতা।</p>
                </div>
                <div class="feature-item">
                     <i class="feature-icon">[আইকন ক্লাস]</i>
                    <h3>নির্ভরযোগ্যতা</h3>
                    <p>আপনার তথ্যের সর্বোচ্চ গোপনীয়তা ও নির্ভরতা নিশ্চিত।</p>
                </div>
                <div class="feature-item">
                     <i class="feature-icon">[আইকন ক্লাস]</i>
                    <h3>ক্লায়েন্ট-কেন্দ্রিকতা</h3>
                    <p>প্রতিটি মক্কেলের প্রতি ব্যক্তিগত মনোযোগ ও সেরা সমাধান।</p>
                </div>
                 <div class="feature-item">
                     <i class="feature-icon">[আইকন ক্লাস]</i>
                    <h3>ফলাফল</h3>
                    <p>আপনার জন্য সম্ভাব্য সর্বোত্তম ফলাফল অর্জনে নিবেদিত।</p>
                </div>
            </div>
        </div>
    </section>

<?php
require 'partials/footer.php'; // ফুটার অংশ যুক্ত করা
?>