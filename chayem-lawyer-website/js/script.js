document.addEventListener('DOMContentLoaded', function() {

    // --- মোবাইল মেনু টগল ফাংশনালিটি ---
    const menuToggleButton = document.getElementById('mobile-menu-toggle-btn');
    const mainNav = document.getElementById('main-nav');
    if (menuToggleButton && mainNav) {
       // ... (মোবাইল মেনুর আগের কোড) ...
        menuToggleButton.addEventListener('click', function() {
            mainNav.classList.toggle('active'); // Navbar এ active ক্লাস যোগ/রিমুভ
            const isExpanded = mainNav.classList.contains('active');
            menuToggleButton.setAttribute('aria-expanded', isExpanded);
            menuToggleButton.setAttribute('aria-label', isExpanded ? 'মেনু বন্ধ করুন' : 'মেনু খুলুন');
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
    if (accordionContainer) {
        const accordionItems = accordionContainer.querySelectorAll('.accordion-item');
        const closeOthers = false; // true করলে একটি খুললে বাকিগুলো বন্ধ হবে

    // Function to close a specific item
    const closeAccordionItem = (item) => {
        const header = item.querySelector('.accordion-header');
             const content = item.querySelector('.accordion-content');
             if (header && content && item.classList.contains('is-open')) {
                 item.classList.remove('is-open');
                 header.setAttribute('aria-expanded', 'false');
                 content.style.maxHeight = content.scrollHeight + 'px';
                 requestAnimationFrame(() => {
                     content.style.maxHeight = '0px';
                     content.style.paddingTop = '0px';
                     content.style.paddingBottom = '0px';
                 });
                 content.addEventListener('transitionend', () => {
                     if (!item.classList.contains('is-open')) {
                         content.hidden = true;
                     }
                 }, { once: true });
             }
        };

    // Function to open a specific item
    const openAccordionItem = (item) => {
        const header = item.querySelector('.accordion-header');
            const content = item.querySelector('.accordion-content');
            if (header && content && !item.classList.contains('is-open')) {
                item.classList.add('is-open');
                header.setAttribute('aria-expanded', 'true');
                content.hidden = false;
                const contentHeight = content.scrollHeight;
                content.style.maxHeight = contentHeight + 'px';
                 content.style.paddingTop = '';
                 content.style.paddingBottom = '';
                content.addEventListener('transitionend', () => {
                   // if (item.classList.contains('is-open')) {
                   //      content.style.maxHeight = 'none'; // Use with caution
                   // }
                }, { once: true });
            }
        };


    accordionItems.forEach(item => {
       const header = item.querySelector('.accordion-header');
            const content = item.querySelector('.accordion-content');
            if (header && content) {
                content.style.maxHeight = '0px';
                content.hidden = true;
                header.setAttribute('aria-expanded', 'false');
                header.addEventListener('click', () => {
                    const isOpen = item.classList.contains('is-open');
                    if (closeOthers && !isOpen) {
                        accordionItems.forEach(otherItem => { if (otherItem !== item) { closeAccordionItem(otherItem); } });
                    }
                    if (!isOpen) { openAccordionItem(item); } else { closeAccordionItem(item); }
                });
            }
        });
    } // End of accordion logic

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