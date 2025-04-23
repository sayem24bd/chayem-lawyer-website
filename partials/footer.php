    </main> <!-- প্রধান কন্টেন্ট এলাকা শেষ -->

    <!-- ফুটার -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <h4>যোগাযোগের তথ্য</h4>
                    <address> <!-- ঠিকানা লিখার জন্য address ট্যাগ ভালো -->
                        <strong>ঠিকানা:</strong> [আপনার চেম্বারের সম্পূর্ণ ঠিকানা]<br>
                        <strong>ফোন:</strong> <a href="tel:[আপনার ফোন নম্বর]">[আপনার ফোন নম্বর]</a><br> <!-- ফোন নম্বর ক্লিকেবল -->
                        <strong>ইমেইল:</strong> <a href="mailto:[আপনার ইমেইল আইডি]">[আপনার ইমেইল আইডি]</a><br> <!-- ইমেইল ক্লিকেবল -->
                        <strong>কাজের সময়:</strong> [আপনার চেম্বারের সময়সূচী]
                    </address>
                </div>
                <div class="footer-col">
                    <h4>গুরুত্বপূর্ণ লিঙ্ক</h4>
                    <ul>
                        <li><a href="about.php">আমাদের সম্পর্কে</a></li>
                        <li><a href="services.php">সেবাসমূহ</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                        <li><a href="#">গোপনীয়তা নীতি</a></li>
                        <li><a href="#">ব্যবহারের শর্তাবলী</a></li>
                    </ul>
                </div>
                 <div class="footer-col">
                    <h4>সামাজিক মাধ্যম</h4>
                    <div class="social-links">
                        <a href="[ফেসবুক লিংক]" target="_blank" rel="noopener noreferrer" aria-label="ফেসবুক">Facebook</a> |
                        <a href="[লিঙ্কডইন লিংক]" target="_blank" rel="noopener noreferrer" aria-label="লিঙ্কডইন">LinkedIn</a>
                        <!-- প্রয়োজনে অন্যান্য আইকন যোগ করুন -->
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>© <?php echo date("Y"); // বর্তমান বছর ডাইনামিকভাবে দেখাবে ?> মোঃ ছায়েম। সর্বস্বত্ব সংরক্ষিত।</p>
                <!-- আপনি চাইলে এখানে ডেভেলপারের ক্রেডিট দিতে পারেন -->
                <!-- <p>ডিজাইন ও ডেভেলপমেন্ট: [আপনার নাম/কোম্পানি]</p> -->
            </div>
        </div>
    </footer>

    <!-- JavaScript ফাইল লিংক (বডির শেষে) -->
    <script src="js/script.js"></script>

</body>
</html>