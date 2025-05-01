<?php
// admin/rulings/add.php

// --- Configuration & Initialization ---
$adminPageTitle = 'নতুন সিদ্ধান্ত যোগ করুন';
$currentPage = 'rulings'; // For active navigation state in header.php

// --- Load Core Files ---
// Define paths for better organization
define('ADMIN_BASE_PATH', dirname(__DIR__));
define('ADMIN_INCLUDES_PATH', ADMIN_BASE_PATH . '/includes/');
define('ROOT_PATH', dirname(ADMIN_BASE_PATH));
define('ROOT_CONFIG_PATH', ROOT_PATH . '/config/');

// Load essential functions (includes session_start) and perform authentication check
require_once ADMIN_INCLUDES_PATH . 'functions.php';
require_admin_login(); // Redirects if not logged in

// Load Database Connection
try {
    require_once ROOT_CONFIG_PATH . 'db.php'; // Provides $pdo
} catch (Exception $e) {
    error_log("Critical Error: Failed to load DB config. " . $e->getMessage());
    die("গুরুতর ডেটাবেস ত্রুটি। অ্যাডমিনিস্ট্রেটরের সাথে যোগাযোগ করুন।");
}

// --- Initialize Variables ---
$errors = [];
$title = '';
$category = '';
$icon = 'fas fa-landmark'; // Default icon
$citation = '';
$summary = '';
$keywords = ''; // For Tagify (will be JSON string from input, default empty)
$judgment_url = ''; // New field for judgment link
$isActive = 1;

// --- Fetch Existing Categories & Prepare Samples ---
$existingCategories = [];
$sampleCategories = [ // Added more samples
    'পারিবারিক আইন', 'ভূমি আইন', 'ফৌজদারী আইন', 'চুক্তি আইন', 'কোম্পানি আইন',
    'শ্রমিক আইন', 'ব্যাংকিং আইন', 'মেধাস্বত্ব আইন', 'পরিবেশ আইন', 'কর আইন',
    'সাংবিধানিক আইন', 'প্রশাসনিক আইন', 'সালিশি আইন (Arbitration)', 'সাইবার আইন'
];
try {
    $categoryStmt = $pdo->query("SELECT DISTINCT category FROM rulings WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
    $dbCategories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
    // Merge database categories with samples and ensure uniqueness
    $existingCategories = array_unique(array_merge($sampleCategories, $dbCategories));
    sort($existingCategories); // Keep sorted
} catch (PDOException $e) {
    error_log("Error fetching categories: " . $e->getMessage());
    $_SESSION['warning_message'] = "বিদ্যমান ক্যাটাগরি তালিকা আনতে সমস্যা হয়েছে, আপনি নতুন টাইপ করতে পারেন।";
    $existingCategories = $sampleCategories; // Fallback to samples
}


// --- Form Submission Handling ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Verify CSRF Token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'নিরাপত্তা টোকেন মেলেনি বা মেয়াদোত্তীর্ণ। অনুগ্রহ করে ফর্মটি রিফ্রেশ করে আবার চেষ্টা করুন।';
    } else {
        // 2. Collect & Sanitize Data
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $icon_input = trim($_POST['icon'] ?? '');
        $citation = trim($_POST['citation'] ?? '');
        $summary = trim($_POST['summary'] ?? ''); // Allow HTML from CKEditor (sanitize output)
        $keywords_json = trim($_POST['keywords'] ?? '[]'); // Tagify JSON string
        $judgment_url = trim($_POST['judgment_url'] ?? ''); // New judgment URL field
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // 3. Server-side Validation
        if (empty($title)) { $errors[] = 'শিরোনাম পূরণ করা আবশ্যক।'; }
        elseif (mb_strlen($title) > 255) { $errors[] = 'শিরোনাম ২৫৫ অক্ষরের বেশি হতে পারবে না।'; }

        if (empty($category)) { $errors[] = 'ক্যাটাগরি পূরণ করা আবশ্যক।'; }
        elseif (mb_strlen($category) > 100) { $errors[] = 'ক্যাটাগরি ১০০ অক্ষরের বেশি হতে পারবে না।'; }

        if (empty($summary)) { $errors[] = 'সারসংক্ষেপ পূরণ করা আবশ্যক।'; }

        // Validate Icon format
        if (!empty($icon_input) && !preg_match('/^fa[srbld]?\sfa-[\w-]+(\sfa-[\dx]+)?$/', $icon_input)) {
             $errors[] = 'আইকন ক্লাসের ফরম্যাট সঠিক নয় (যেমন: fas fa-gavel বা far fa-file)। Font Awesome 6 ব্যবহার করুন।';
        } else {
            $icon = empty($icon_input) ? 'fas fa-landmark' : $icon_input;
        }

         if (!empty($citation) && mb_strlen($citation) > 150) { $errors[] = 'সাইটেশন ১৫০ অক্ষরের বেশি হতে পারবে না।'; }

        // Validate Judgment URL (if provided)
        if (!empty($judgment_url)) {
            if (!filter_var($judgment_url, FILTER_VALIDATE_URL)) {
                 $errors[] = 'জাজমেন্ট লিঙ্কের URL টি সঠিক ফরম্যাটে নেই। (http:// বা https:// দিয়ে শুরু করুন)';
            } elseif (mb_strlen($judgment_url) > 512) {
                 $errors[] = 'জাজমেন্ট লিঙ্কের URL ৫১২ অক্ষরের বেশি হতে পারবে না।';
            }
        }

        // Decode Keywords from Tagify JSON and prepare for DB
        $keywords_db = null; // Default to NULL
        $keywords_array = json_decode($keywords_json, true);
        if (is_array($keywords_array) && !empty($keywords_array)) {
            $keyword_values = array_column($keywords_array, 'value');
            // Sanitize each keyword slightly (optional: remove extra commas, limit length)
            $sanitized_keywords = array_map(function($kw) {
                return trim(mb_substr($kw, 0, 50)); // Example: trim and limit length
            }, $keyword_values);
            $keywords_db = implode(', ', array_filter($sanitized_keywords)); // Join non-empty keywords

            if (empty($keywords_db)) { // If all keywords were empty after sanitizing
                $keywords_db = null;
            } elseif (mb_strlen($keywords_db) > 255) {
                 $errors[] = 'কীওয়ার্ডগুলো সম্মিলিতভাবে ২৫৫ অক্ষরের বেশি হতে পারবে না।';
                 $keywords_db = null; // Don't save if too long
            }
        }


        // 4. Database Insertion (if no errors)
        if (empty($errors)) {
            try {
                // Added judgment_url column
                $sql = "INSERT INTO rulings
                        (title, category, icon, citation, summary, keywords, judgment_url, is_active, created_at, updated_at)
                        VALUES (:title, :category, :icon, :citation, :summary, :keywords, :judgment_url, :is_active, NOW(), NOW())";

                $stmt = $pdo->prepare($sql);

                // Bind parameters for security
                $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                $stmt->bindParam(':category', $category, PDO::PARAM_STR);
                $stmt->bindParam(':icon', $icon, PDO::PARAM_STR);
                $stmt->bindValue(':citation', empty($citation) ? null : $citation, PDO::PARAM_STR);
                $stmt->bindParam(':summary', $summary, PDO::PARAM_STR); // Storing potentially HTML content
                $stmt->bindValue(':keywords', $keywords_db, PDO::PARAM_STR); // Store comma-separated string or NULL
                $stmt->bindValue(':judgment_url', empty($judgment_url) ? null : $judgment_url, PDO::PARAM_STR); // New field
                $stmt->bindParam(':is_active', $isActive, PDO::PARAM_INT);

                $stmt->execute();

                $_SESSION['success_message'] = 'নতুন সিদ্ধান্ত সফলভাবে যোগ করা হয়েছে।';
                header('Location: index.php'); // Redirect to rulings list
                exit;

            } catch (PDOException $e) {
                error_log("Ruling Add DB Error: (" . $e->getCode() . ") " . $e->getMessage());
                 if ($e->getCode() == '23000') {
                     $errors[] = 'ত্রুটি: এই শিরোনাম বা সাইটেশন ইতিমধ্যে বিদ্যমান থাকতে পারে।';
                 } else {
                    $errors[] = 'ডেটাবেসে সিদ্ধান্ত যোগ করার সময় একটি অপ্রত্যাশিত সমস্যা হয়েছে।';
                 }
            }
        }
    } // End CSRF check else
} // End POST check

// --- Load Header ---
require_once '../includes/header.php';
?>

<!-- === Form Section === -->
<div class="card form-card animate__animated animate__fadeIn">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-plus-circle"></i> নতুন আইনি সিদ্ধান্ত যোগ করুন</h2>
        <p class="card-subtitle">প্রয়োজনীয় সকল তথ্য পূরণ করুন। <span class="required">*</span> চিহ্নিত ফিল্ডগুলো আবশ্যক।</p>
    </div>
    <div class="card-body">

        <!-- Display Validation Errors -->
        <?php if (!empty($errors)): ?>
            <div class="message error animate__animated animate__shakeX" role="alert">
                <strong><i class="fas fa-times-circle"></i> অনুগ্রহ করে নিচের ত্রুটিগুলো সংশোধন করুন:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

         <!-- Display Warning Message -->
         <?php if (isset($_SESSION['warning_message'])): ?>
            <div class="message warning animate__animated animate__fadeIn">
                <i class="fas fa-exclamation-triangle"></i> <?= e($_SESSION['warning_message']) ?>
            </div>
            <?php unset($_SESSION['warning_message']); ?>
        <?php endif; ?>


        <!-- Add Ruling Form -->
        <form method="POST" action="add.php" id="addRulingForm" novalidate>
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">

            <div class="form-grid">
                <!-- Left Column -->
                <div class="grid-column">
                    <!-- Title -->
                    <div class="form-group">
                        <label for="title"><i class="fas fa-heading"></i> শিরোনাম <span class="required">*</span></label>
                        <input type="text" id="title" name="title" value="<?= e($title) ?>" required maxlength="255">
                    </div>

                    <!-- Category -->
                    <div class="form-group">
                        <label for="category"><i class="fas fa-tags"></i> ক্যাটাগরি <span class="required">*</span></label>
                        <input list="category-list" id="category" name="category" value="<?= e($category) ?>" required maxlength="100" placeholder="আইনের ক্ষেত্র বাছাই করুন বা লিখুন">
                        <datalist id="category-list">
                            <?php foreach ($existingCategories as $cat): ?>
                                <option value="<?= e($cat) ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>

                    <!-- Icon -->
                     <div class="form-group">
                        <label for="icon"><i class="fas fa-icons"></i> আইকন ক্লাস (Font Awesome 6 Free)</label>
                        <div class="input-group">
                             <input type="text" id="icon" name="icon" value="<?= e($icon) ?>" placeholder="যেমন: fas fa-gavel">
                             <button type="button" id="iconPreviewBtn" class="button button-icon-preview" title="আইকন প্রিভিউ"><i id="iconPreview" class="<?= e($icon) // Initial icon ?>"></i></button>
                         </div>
                        <small class="form-text">বিষয়ভিত্তিক আইকন (<a href="https://fontawesome.com/search?m=free&o=r" target="_blank" rel="noopener noreferrer">খুঁজুন</a>)। খালি রাখলে ডিফল্ট (<i class="fas fa-landmark"></i>) আসবে। স্টাইল সহ লিখুন (যেমন fas/far/fab)।</small>
                    </div>

                    <!-- Citation -->
                     <div class="form-group">
                        <label for="citation"><i class="fas fa-book"></i> সাইটেশন (ঐচ্ছিক)</label>
                        <input type="text" id="citation" name="citation" value="<?= e($citation) ?>" maxlength="150" placeholder="যেমন: 55 DLR (AD) 123">
                    </div>
                </div><!-- /.grid-column -->

                <!-- Right Column -->
                <div class="grid-column">
                    <!-- Keywords (Tagify) -->
                    <div class="form-group">
                        <label for="keywordsInput"><i class="fas fa-key"></i> কীওয়ার্ডস (ঐচ্ছিক)</label>
                        <!-- IMPORTANT: name="keywords" is the REAL input Tagify uses -->
                        <input type="text" id="keywordsInput" name="keywords" value="<?= e($keywords) ?>" placeholder="কীওয়ার্ড যোগ করুন এবং এন্টার চাপুন">
                         <small class="form-text">সিদ্ধান্তটি খুঁজে পেতে সাহায্য করবে এমন শব্দ যোগ করুন (কমা বা এন্টার)।</small>
                    </div>

                    <!-- Judgment URL -->
                    <div class="form-group">
                        <label for="judgment_url"><i class="fas fa-link"></i> পূর্ণাঙ্গ জাজমেন্ট লিংক (ঐচ্ছিক)</label>
                        <input type="url" id="judgment_url" name="judgment_url" value="<?= e($judgment_url) ?>" maxlength="512" placeholder="https://.... বা http://....">
                        <small class="form-text">যদি পূর্ণাঙ্গ রায়ের কোনো অনলাইন লিঙ্ক থাকে (যেমন: কোনো ওয়েবসাইট বা PDF)।</small>
                    </div>

                    <!-- Active Status -->
                    <div class="form-group checkbox-group">
                         <input type="checkbox" id="is_active" name="is_active" value="1" <?= $isActive ? 'checked' : '' ?>>
                         <label for="is_active">সক্রিয়</label>
                         <small>টিক দেওয়া থাকলে এই সিদ্ধান্তটি ওয়েবসাইটে দেখানো হবে।</small>
                    </div>

                </div><!-- /.grid-column -->
            </div><!-- /.form-grid -->

            <!-- Summary (CKEditor) -->
            <div class="form-group form-group-full">
                <label for="summaryEditor"><i class="fas fa-file-alt"></i> সারসংক্ষেপ <span class="required">*</span></label>
                <textarea id="summaryEditor" name="summary" required><?= e($summary) // Use e() for initial value, CKEditor handles HTML ?></textarea>
                 <small>সিদ্ধান্তের মূল বিষয়বস্তু, যুক্তি এবং ফলাফল লিখুন।</small>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="button button-primary"><i class="fas fa-save"></i> সংরক্ষণ করুন</button>
                <a href="index.php" class="button button-secondary"><i class="fas fa-times"></i> বাতিল</a>
            </div>
        </form>
    </div><!-- /.card-body -->
</div><!-- /.card -->


<!-- Include Footer -->
<?php require_once '../includes/footer.php'; ?>

<!-- === Page Specific Scripts === -->
<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<!-- Tagify CDN -->
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify@3.1.0/dist/tagify.polyfills.min.js"></script> <!-- Optional for IE11 -->

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- Initialize CKEditor 5 ---
        const summaryEditorElement = document.getElementById('summaryEditor');
        if (summaryEditorElement) {
            ClassicEditor
                .create(summaryEditorElement, {
                    // Basic Toolbar Configuration (customize as needed)
                    toolbar: {
                        items: [
                            'heading', '|',
                            'bold', 'italic', 'underline', '|',
                            'bulletedList', 'numberedList', '|',
                            'blockQuote', 'insertTable', '|',
                            'undo', 'redo'
                        ]
                    },
                    language: 'en', // Set language if needed (bn not directly supported by default build)
                    table: { // Table feature configuration
                        contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
                    },
                    // Add more configurations if needed
                })
                .catch(error => {
                    console.error('CKEditor Initialization Error:', error);
                });
        } else {
            console.error("Element 'summaryEditor' not found for CKEditor.");
        }

        // --- Initialize Tagify for Keywords ---
        const keywordsInputElement = document.getElementById('keywordsInput');
        if (keywordsInputElement) {
            // Sample keywords (can be expanded)
            const sampleKeywords = ['আইন', 'আদালত', 'মামলা', 'অধিকার', 'সম্পত্তি', 'দলিল', 'চুক্তি', 'জামিন', 'তালাক', 'বিচ্ছেদ', 'ভরণপোষণ', 'অভিভাবকত্ব', 'ব্যবসা', 'শ্রমিক', 'পরিবেশ', 'কর', 'সংবিধান'];
            // Combine existing categories with samples for whitelist suggestions
            const categorySuggestions = <?= json_encode($existingCategories) ?>;
            const keywordWhitelist = [...new Set([...categorySuggestions, ...sampleKeywords])].map(kw => ({ value: kw }));


            const tagify = new Tagify(keywordsInputElement, {
                whitelist: keywordWhitelist, // Suggestions list
                maxTags: 10, // Limit number of tags
                dropdown: {
                    maxItems: 15,
                    enabled: 0, // Show suggestions on focus
                    closeOnSelect: true,
                },
                originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(', '), // How to format for hidden input (optional, default is JSON)
                // We will use the default JSON submission for PHP handling
            });

            // Load initial tags if keywords variable has values (e.g., during validation error)
             try {
                 const initialTags = <?= $keywords ? json_encode(array_map(function($k) { return ['value' => trim($k)]; }, explode(',', $keywords))) : '[]' ?>;
                 if (initialTags.length > 0) {
                    tagify.addTags(initialTags, true, true); // Add tags without triggering events
                 }
             } catch (e) { console.error("Error parsing initial keywords for Tagify", e); }

        } else {
            console.error("Element 'keywordsInput' not found for Tagify.");
        }


        // --- Auto-Select Icon based on Category ---
        const categoryInput = document.getElementById('category');
        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('iconPreview');

        if (categoryInput && iconInput && iconPreview) {
            // Mapping of categories (lowercase) to icons
            const categoryIconMap = {
                'পারিবারিক আইন': 'fas fa-users',
                'ভূমি আইন': 'fas fa-map-marked-alt',
                'ফৌজদারী আইন': 'fas fa-gavel',
                'চুক্তি আইন': 'fas fa-file-signature',
                'কোম্পানি আইন': 'fas fa-building',
                'শ্রমিক আইন': 'fas fa-hard-hat', // or fas fa-users-cog
                'ব্যাংকিং আইন': 'fas fa-university', // or fas fa-money-check-alt
                'মেধাস্বত্ব আইন': 'fas fa-copyright', // or fa-lightbulb
                'পরিবেশ আইন': 'fas fa-leaf', // or fa-tree
                'কর আইন': 'fas fa-calculator', // or fa-file-invoice-dollar
                'সাংবিধানিক আইন': 'fas fa-balance-scale', // or fa-landmark-flag
                'প্রশাসনিক আইন': 'fas fa-landmark', // or fa-university
                'সালিশি আইন': 'fas fa-handshake',
                'সাইবার আইন': 'fas fa-shield-alt', // or fa-laptop-code
                // Add more mappings as needed
                'default': 'fas fa-landmark' // Default icon
            };

            const updateIcon = () => {
                const categoryValue = categoryInput.value.trim(); //.toLowerCase(); // Use original case for matching map keys if needed
                let selectedIcon = categoryIconMap['default']; // Start with default

                // Find matching icon (case-insensitive check can be added if needed)
                 if (categoryIconMap.hasOwnProperty(categoryValue)) {
                    selectedIcon = categoryIconMap[categoryValue];
                }
                /* // Case-insensitive alternative:
                const lowerCategory = categoryValue.toLowerCase();
                 for (const key in categoryIconMap) {
                    if (key.toLowerCase() === lowerCategory) {
                        selectedIcon = categoryIconMap[key];
                        break;
                    }
                 } */

                iconInput.value = selectedIcon;
                iconPreview.className = selectedIcon; // Update the preview icon class
            };

            // Update icon when category changes or on input
            categoryInput.addEventListener('change', updateIcon);
            categoryInput.addEventListener('input', updateIcon); // Update as user types

             // Also update preview when icon is manually changed
             iconInput.addEventListener('input', () => {
                 const currentIconClass = iconInput.value.trim() || categoryIconMap['default'];
                 // Basic validation check before applying class
                 if (/^fa[srbld]?\sfa-[\w-]+(\sfa-[\dx]+)?$/.test(currentIconClass)) {
                     iconPreview.className = currentIconClass;
                 } else {
                      iconPreview.className = categoryIconMap['default']; // Revert to default if invalid
                 }
             });
        }

    }); // End DOMContentLoaded
</script>

<!-- Specific CSS (Move to admin_style.css for production) -->
<style>
    .required { color: #dc3545; margin-left: 3px; font-weight: normal; }
    .checkbox-group { display: flex; align-items: center; gap: 0.5rem; margin-top: 1rem;}
    .checkbox-group input[type="checkbox"] { width: auto; margin: 0; flex-shrink: 0; transform: scale(1.1);} /* Slightly larger checkbox */
    .checkbox-group label { margin-bottom: 0; font-weight: normal; cursor: pointer; }
    .form-group small.form-text { font-size: 0.85em; color: #6c757d; display: block; margin-top: 0.3rem;}
    .form-group small a {text-decoration: none; color: #007bff;}
    .form-group small a:hover { text-decoration: underline; }

    /* Form Grid Layout */
    .form-grid { display: grid; grid-template-columns: 1fr; gap: 0 2rem; /* Only gap between columns */}
    @media (min-width: 992px) { .form-grid { grid-template-columns: 1fr 1fr; } } /* 2 columns on large screens */
    .grid-column .form-group { margin-bottom: 1.5rem; } /* Vertical spacing within columns */
    .form-group-full { grid-column: 1 / -1; margin-top: 1.5rem;} /* Span full width */

    /* Card Styling */
    .card.form-card { background-color: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.07); border: 1px solid #e0e0e0; margin-top: 1rem; overflow: hidden;}
    .card-header { background-color: #f8f9fa; padding: 1.2rem 1.8rem; border-bottom: 1px solid #e0e0e0; border-top-left-radius: 8px; border-top-right-radius: 8px;}
    .card-title { font-size: 1.5rem; margin: 0; color: #343a40; display: flex; align-items: center; gap: 0.5rem;}
    .card-subtitle { font-size: 0.95rem; margin: 0.2rem 0 0 0; color: #6c757d;}
    .card-body { padding: 2rem 1.8rem; }

    /* Tagify Input Styling */
    .tagify { border: 1px solid #ced4da; border-radius: 4px; padding: 0.4rem 0.6rem; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; }
    .tagify:hover { border-color: #86b7fe;}
    .tagify--focus { border-color: #86b7fe; outline: 0; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
    .tagify__tag { background-color: #0d6efd; color: white; border-radius: 15px; padding: 0.3em 0.7em; margin: 0.3em;}
    .tagify__tag > div::before { box-shadow: none; } /* Remove default box-shadow */
    .tagify__input { margin: 0.3em; }
    .tagify__tag__removeBtn { color: white; opacity: 0.7; order: 1; margin-left: 0.4em;}
    .tagify__tag__removeBtn:hover { color: white; opacity: 1; }
    /* Suggestions dropdown styling (optional) */
    .tagify__dropdown__item { padding: .5em .7em; }
    .tagify__dropdown__item--active { background: #e2eaf6; }

    /* CKEditor Styling */
    .ck-editor__editable { min-height: 180px; border: 1px solid #ced4da !important; border-top: none !important; border-radius: 0 0 4px 4px !important; box-shadow: inset 0 1px 1px rgba(0,0,0,.075); transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;}
    .ck-editor__editable.ck-focused { border-color: #86b7fe !important; outline: 0; box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;}
    .ck.ck-toolbar { border-radius: 4px 4px 0 0 !important; border: 1px solid #ced4da !important; background-color: #f8f9fa; }

    /* Icon Input with Preview */
    .input-group { display: flex; border: 1px solid #ced4da; border-radius: 4px; overflow: hidden; transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out; }
    .input-group input[type="text"] { flex-grow: 1; border: none; padding: 0.7rem 0.9rem; outline: none; border-radius: 0; /* Remove individual radius */}
    .input-group:focus-within { border-color: #86b7fe; outline: 0; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
    .button-icon-preview { background-color: #e9ecef; border: none; border-left: 1px solid #ced4da; padding: 0 0.9rem; cursor: default; color: #495057; font-size: 1.1rem;}

    /* Form Actions */
    .form-actions { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #eee; text-align: right; }
    .form-actions .button { margin-left: 0.5rem; }
    .button i { margin-right: 0.5em; }

</style>