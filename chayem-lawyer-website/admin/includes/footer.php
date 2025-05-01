<?php
// admin/includes/footer.php
?>
        </main>
    </div>

    <!-- Theme Toggle Script -->
    <script>
// থিম ম্যানেজমেন্ট
const toggleBtn = document.getElementById('themeToggle');
const htmlElement = document.documentElement;

// লোকালস্টোরেজ থেকে থিম লোড
const savedTheme = localStorage.getItem('theme') || 'light';
htmlElement.setAttribute('data-theme', savedTheme);
toggleBtn.innerHTML = savedTheme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';

// থিম পরিবর্তন ইভেন্ট
toggleBtn.addEventListener('click', () => {
    const newTheme = htmlElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    htmlElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    toggleBtn.innerHTML = newTheme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    
    // TinyMCE এডিটরের থিম আপডেট
    if (typeof tinymce !== 'undefined') {
        tinymce.editors.forEach(editor => {
            editor.skin = newTheme === 'dark' ? 'oxide-dark' : 'oxide';
            editor.contentCSS = newTheme === 'dark' ? 'dark' : 'default';
        });
    }
});
</script>

</body>
</html>
