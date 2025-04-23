document.addEventListener('DOMContentLoaded', function() {

    // --- মোবাইল মেনু টগল ফাংশনালিটি ---
    const menuToggleButton = document.getElementById('mobile-menu-toggle-btn');
    const mainNav = document.getElementById('main-nav');

    if (menuToggleButton && mainNav) {
        menuToggleButton.addEventListener('click', function() {
            mainNav.classList.toggle('active'); // Navbar এ active ক্লাস যোগ/রিমুভ
            const isExpanded = mainNav.classList.contains('active');
            menuToggleButton.setAttribute('aria-expanded', isExpanded);
            menuToggleButton.setAttribute('aria-label', isExpanded ? 'মেনু বন্ধ করুন' : 'মেনু খুলুন');

            // Navbar active হলে body তে ক্লাস যোগ করা (ঐচ্ছিক, overflow 막ার জন্য)
            // document.body.classList.toggle('mobile-nav-active', isExpanded);
        });

        // মেনুর বাইরে ক্লিক করলে বন্ধ করার কোড (ঐচ্ছিক)
        document.addEventListener('click', function(event) {
            const isClickInsideNav = mainNav.contains(event.target);
            const isClickOnToggle = menuToggleButton.contains(event.target);

            if (!isClickInsideNav && !isClickOnToggle && mainNav.classList.contains('active')) {
                mainNav.classList.remove('active');
                menuToggleButton.setAttribute('aria-expanded', 'false');
                menuToggleButton.setAttribute('aria-label', 'মেনু খুলুন');
                // document.body.classList.remove('mobile-nav-active');
            }
        });
         // Escape কী চাপলে বন্ধ করার কোড (ঐচ্ছিক)
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && mainNav.classList.contains('active')) {
                mainNav.classList.remove('active');
                menuToggleButton.setAttribute('aria-expanded', 'false');
                menuToggleButton.setAttribute('aria-label', 'মেনু খুলুন');
                // document.body.classList.remove('mobile-nav-active');
            }
        });
    }

    // --- অ্যাকরডিয়ন ফাংশনালিটি (Rulings Page) ---
    const accordionContainer = document.querySelector('.rulings-accordion-container');

    if (accordionContainer) { // নিশ্চিত করা যে আমরা rulings.php পেজে আছি
        const accordionItems = accordionContainer.querySelectorAll('.accordion-item');

        accordionItems.forEach(item => {
            const header = item.querySelector('.accordion-header');
            const content = item.querySelector('.accordion-content');

            if (header && content) {
                // শুরুতে কন্টেন্ট হাইড আছে ও max-height 0 সেট করা (CSS transition এর জন্য)
                content.style.maxHeight = '0px';
                content.hidden = true; // নিশ্চিত করা যে এটি লুকানো
                header.setAttribute('aria-expanded', 'false'); // শুরুতে এক্সপান্ডেড না

                header.addEventListener('click', () => {
                    const isExpanded = header.getAttribute('aria-expanded') === 'true';

                    if (!isExpanded) {
                        // আইটেমটি খুলবে
                        header.setAttribute('aria-expanded', 'true');
                        content.hidden = false;
                        // প্রথমে ডিসপ্লে ঠিক করে হাইট মাপা
                        content.style.display = 'block'; // Ensure display is correct for scrollHeight calculation
                        const contentHeight = content.scrollHeight + 'px';
                        content.style.maxHeight = contentHeight;

                        // ট্রানজিশন শেষ হলে ম্যাক্স-হাইট রিমুভ (ঐচ্ছিক, কিন্তু ভালো)
                        content.addEventListener('transitionend', function handler() {
                           if (content.style.maxHeight !== '0px') { // যদি এখনো খোলা থাকে
                                content.style.maxHeight = 'none'; // 'auto' equivalent
                           }
                           content.removeEventListener('transitionend', handler);
                        }, { once: true }); // একবারই রান হবে

                    } else {
                        // আইটেমটি বন্ধ হবে
                        header.setAttribute('aria-expanded', 'false');
                        // বন্ধ করার সময়: প্রথমে হাইট সেট করে তারপর ০ করা
                        content.style.maxHeight = content.scrollHeight + 'px';
                        // ছোট্ট ডিলে দিয়ে ০ করলে অ্যানিমেশন স্মুথ হয়
                        requestAnimationFrame(() => {
                            content.style.maxHeight = '0px';
                        });

                        // ট্রানজিশন শেষ হলে hidden=true সেট করা
                        content.addEventListener('transitionend', function handler() {
                           if (content.style.maxHeight === '0px') { // যদি বন্ধ হয়ে থাকে
                                content.hidden = true;
                                content.style.display = ''; // Reset display
                           }
                           content.removeEventListener('transitionend', handler);
                        }, { once: true });
                    }

                     // ঐচ্ছিক: একটি খুললে বাকিগুলো বন্ধ করা (যদি চান)
                    /*
                    accordionItems.forEach(otherItem => {
                        if (otherItem !== item) {
                             const otherHeader = otherItem.querySelector('.accordion-header');
                             const otherContent = otherItem.querySelector('.accordion-content');
                             if (otherHeader && otherContent && otherHeader.getAttribute('aria-expanded') === 'true') {
                                 otherHeader.setAttribute('aria-expanded', 'false');
                                 otherContent.style.maxHeight = otherContent.scrollHeight + 'px';
                                 requestAnimationFrame(() => {
                                     otherContent.style.maxHeight = '0px';
                                 });
                                  otherContent.addEventListener('transitionend', function handler() {
                                    if (otherContent.style.maxHeight === '0px') {
                                        otherContent.hidden = true;
                                        otherContent.style.display = '';
                                    }
                                    otherContent.removeEventListener('transitionend', handler);
                                  }, { once: true });
                             }
                        }
                    });
                    */

                });
            }
        });
    }


    // --- সার্চ ও ফিল্টার ফাংশনালিটি (Rulings Page) ---
    const searchInput = document.getElementById('rulingSearchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const rulingsContainerForFilter = document.querySelector('.rulings-accordion-container'); // আবার সিলেক্ট করা হচ্ছে নির্দিষ্টতার জন্য

    function performFiltering() {
        if (!rulingsContainerForFilter) return; // যদি কন্টেইনার না থাকে, কিছু করার নেই

        const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedCategory = categoryFilter ? categoryFilter.value : 'all';
        const items = rulingsContainerForFilter.querySelectorAll('.accordion-item');
        let visibleCount = 0;

        items.forEach(item => {
            const title = item.querySelector('.header-title')?.textContent.toLowerCase() || '';
            const citation = item.querySelector('.header-citation')?.textContent.toLowerCase() || '';
            const summary = item.querySelector('.accordion-content p')?.textContent.toLowerCase() || '';
            const keywords = Array.from(item.querySelectorAll('.keyword-tag')).map(tag => tag.textContent.toLowerCase());
            const itemCategory = item.dataset.category || ''; // HTML-এ data-category থাকতে হবে

            // সার্চ টার্ম ম্যাচিং (টাইটেল, সাইটেশন, সামারি বা কীওয়ার্ডে)
            const matchesSearch = searchTerm === '' ||
                                  title.includes(searchTerm) ||
                                  citation.includes(searchTerm) ||
                                  summary.includes(searchTerm) ||
                                  keywords.some(k => k.includes(searchTerm));

            // ক্যাটাগরি ম্যাচিং
            const matchesCategory = selectedCategory === 'all' || itemCategory === selectedCategory;

            // ম্যাচ করলে দেখাও, না করলে লুকাও (ক্লাস ব্যবহার করে)
            if (matchesSearch && matchesCategory) {
                item.classList.remove('hidden-by-filter'); // লুকানো ক্লাস রিমুভ
                visibleCount++;
            } else {
                item.classList.add('hidden-by-filter'); // লুকানো ক্লাস যোগ
            }
        });

         // যদি কোনো আইটেম না পাওয়া যায়, বার্তা দেখানোর ব্যবস্থা (ঐচ্ছিক)
         const noResultMessage = rulingsContainerForFilter.querySelector('.no-result-message');
         if (visibleCount === 0 && !noResultMessage) {
             const messageElement = document.createElement('p');
             messageElement.textContent = 'আপনার অনুসন্ধান বা ফিল্টারের সাথে মিলে এমন কোনো সিদ্ধান্ত পাওয়া যায়নি।';
             messageElement.className = 'no-result-message text-center text-danger'; // স্টাইলিংয়ের জন্য ক্লাস
             rulingsContainerForFilter.appendChild(messageElement);
         } else if (visibleCount > 0 && noResultMessage) {
             noResultMessage.remove(); // যদি ফলাফল পাওয়া যায়, বার্তা সরিয়ে ফেলা
         }

    } // performFiltering শেষ

    // ইনপুট বা ফিল্টার পরিবর্তনের সময় ফাংশন কল করা
    if (searchInput) {
        searchInput.addEventListener('input', performFiltering);
    }
    if (categoryFilter) {
        categoryFilter.addEventListener('change', performFiltering);
    }

    // পেজ লোড হওয়ার পর একবার ফিল্টার চালানো (যদি প্রয়োজন হয়)
    // performFiltering();

}); // DOMContentLoaded শেষ