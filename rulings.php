<?php
// === পেজ সেটিংস ===
$pageTitle = "উচ্চ আদালতের গুরুত্বপূর্ণ সিদ্ধান্তসমূহ - মোঃ ছায়েম";
$currentPage = "rulings";

// --- Load Core Files ---
define('ROOT_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config/');
define('PARTIALS_PATH', ROOT_PATH . '/partials/');

// === Helper Function Definition ===
// Define e() function here for this page if not globally available
if (!function_exists('e')) {
    function e(string $string): string {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

// Define bangla_number() function here as well
if (!function_exists('bangla_number')) {
    function bangla_number($number) {
        $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $ban = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($eng, $ban, (string)$number);
    }
}

// Initialize error/status flags BEFORE trying to connect/query
$dbConnectionError = null; // Holds connection error message string or null
$dbQueryError = false; // Holds BOOLEAN status for query errors, default to false
$totalRulings = 0;     // Default counts
$rulings = [];         // Default results array
$categories = [];        // Default categories array
$totalPages = 0;       // Default total pages

// Load Database Connection
try {
    require_once CONFIG_PATH . 'db.php'; // $pdo becomes available
} catch (Exception $e) {
    error_log("Critical Error: Failed to load DB config. " . $e->getMessage());
    $dbConnectionError = "গুরুতর ডেটাবেস ত্রুটি। অ্যাডমিনিস্ট্রেটরের সাথে যোগাযোগ করুন।";
    // $pdo will not be set, subsequent checks will handle this
}

// === পেজিনেশন ভ্যারিয়েবলস ===
$limit = 5; // প্রতি পেজে কয়টি আইটেম দেখাবে
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // বর্তমান পেজ নম্বর
$offset = ($page - 1) * $limit; // ডেটাবেস কুয়েরির জন্য অফসেট

// === সার্চ ও ফিল্টার ভ্যারিয়েবলস ===
// GET রিকোয়েস্ট থেকে মান নেওয়া এবং পরিষ্কার করা
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategory = isset($_GET['category']) ? trim($_GET['category']) : 'all';

// === ডেটাবেস কুয়েরি তৈরি ===
$params = []; // Prepared statement এর জন্য প্যারামিটার অ্যারে
$baseSql = "FROM rulings WHERE is_active = 1"; // শুধুমাত্র সক্রিয়গুলো দেখাবে
$whereClauses = [];

// ক্যাটাগরি ফিল্টার
if ($selectedCategory !== 'all' && !empty($selectedCategory)) {
    $whereClauses[] = "category = :category";
    $params[':category'] = $selectedCategory;
}

// সার্চ টার্ম ফিল্টার (Full-Text Search ব্যবহার করে)
// 주의: Full-Text Search সব MySQL ভার্সন বা Storage Engine এ কাজ নাও করতে পারে।
// বিকল্প হিসেবে LIKE ব্যবহার করা যেতে পারে, তবে পারফরম্যান্স কম হবে।
if (!empty($searchTerm)) {
    // Full-Text Search (MATCH AGAINST) - পারফরম্যান্স ভালো
     $whereClauses[] = "MATCH(title, summary, keywords) AGAINST (:searchTerm IN BOOLEAN MODE)";
     // Boolean mode allows more control, e.g., using + for required words
     // Simple search term: add '*' for prefix matching
     $params[':searchTerm'] = $searchTerm . '*'; // Prefix search এর জন্য

    // বিকল্প: LIKE ব্যবহার (পারফরম্যান্স কম)
    /*
    $whereClauses[] = "(title LIKE :searchTerm OR summary LIKE :searchTerm OR keywords LIKE :searchTerm OR citation LIKE :searchTerm)";
    $params[':searchTerm'] = '%' . $searchTerm . '%';
    */
}

// WHERE ক্লজ তৈরি করা
$whereSql = !empty($whereClauses) ? " AND " . implode(" AND ", $whereClauses) : "";

// === মোট ফলাফলের সংখ্যা গণনা ===
$countSql = "SELECT COUNT(*) " . $baseSql . $whereSql;
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($params);
$totalRulings = $stmtCount->fetchColumn(); // মোট সংখ্যা

// === পেজিনেটেড ফলাফল আনা ===
$rulingsSql = "SELECT id, category, icon, title, citation, summary, keywords, judgment_url " . $baseSql . $whereSql . " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmtRulings = $pdo->prepare($rulingsSql);

// LIMIT এবং OFFSET প্যারামিটার বাইন্ড করা (এগুলো ইন্টিজার)
$stmtRulings->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmtRulings->bindValue(':offset', $offset, PDO::PARAM_INT);

// অন্যান্য প্যারামিটার বাইন্ড করা (category, searchTerm)
foreach ($params as $key => $value) {
    $stmtRulings->bindValue($key, $value); // PDO ডিফল্ট টাইপ নির্ধারণ করবে
}

$stmtRulings->execute();
$rulings = $stmtRulings->fetchAll(); // ফলাফল অ্যারে হিসেবে

// === পেজিনেশন ক্যালকুলেশন ===
$totalPages = ceil($totalRulings / $limit);

// === ক্যাটাগরি তালিকা আনা (ফিল্টার ড্রপডাউনের জন্য) ===
try {
    $categoryStmt = $pdo->query("SELECT DISTINCT category FROM rulings WHERE is_active = 1 ORDER BY category ASC");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $categories = []; // Fetch failed
    error_log("Error fetching categories: " . $e->getMessage());
}


// === হেডার লোড ===
// Load Header
require PARTIALS_PATH . 'header.php';
?>

    <!-- === পেজ হেডার === -->
    <section class="page-header section-padding rulings-header bg-gradient-subtle">
         <div class="container text-center">
             <i class="fas fa-balance-scale header-icon animate__animated animate__pulse animate__infinite"></i>
             <h1 class="page-title">উচ্চ আদালতের সিদ্ধান্ত সংগ্রহ</h1>
             <p class="page-subtitle">আইনের গুরুত্বপূর্ণ ব্যাখ্যা ও নজির সম্পর্কে জানুন (প্রাথমিক ধারণার জন্য)</p>
         </div>
     </section>

    <!-- === ডিসক্লেইমার সেকশন === -->
    <section class="disclaimer-section section-padding bg-light-warning">
        <div class="container">
             <div class="disclaimer-box animate__animated animate__fadeInUp">
                <h3 class="disclaimer-title"><i class="fas fa-exclamation-triangle"></i> গুরুত্বপূর্ণ বিজ্ঞপ্তি</h3>
                <p>এই ওয়েবসাইটে উপস্থাপিত আদালতের সিদ্ধান্তসমূহের সারসংক্ষেপ শুধুমাত্র সাধারণ তথ্য ও শিক্ষামূলক উদ্দেশ্যে প্রদান করা হয়েছে। এটি কোনোভাবেই পূর্ণাঙ্গ বা চূড়ান্ত আইনি ব্যাখ্যা নয়...</p> <!-- সংক্ষিপ্ত করা হলো -->
            </div>
        </div>
    </section>

    <!-- === সার্চ ও ফিল্টার বার (ফর্ম সহ) === -->
    <section class="filter-search-bar section-padding">
        <div class="container">
            <!-- ফর্ম GET মেথডে সাবমিট হবে rulings.php তেই -->
            <form method="GET" action="rulings.php" class="filter-search-form">
                <div class="search-wrapper animate__animated animate__fadeInLeft">
                     <!-- name="search" যোগ করা হয়েছে -->
                     <input type="text" id="rulingSearchInput" name="search" placeholder="বিষয়, কীওয়ার্ড বা মামলা দিয়ে খুঁজুন..." aria-label="সিদ্ধান্ত অনুসন্ধান" value="<?= htmlspecialchars($searchTerm) // বর্তমান সার্চ টার্ম দেখানো ?>">
                     <button type="submit" aria-label="অনুসন্ধান"><i class="fas fa-search"></i></button>
                </div>
                <div class="filter-wrapper animate__animated animate__fadeInRight">
                    <label for="categoryFilter">বিষয় অনুযায়ী দেখুন:</label>
                    <!-- name="category" যোগ করা হয়েছে -->
                    <select id="categoryFilter" name="category" aria-label="বিষয় অনুযায়ী ফিল্টার করুন" onchange="this.form.submit()"> <!-- সিলেক্ট পরিবর্তন হলেই সাবমিট -->
                        <option value="all" <?= ($selectedCategory == 'all') ? 'selected' : '' ?>>সকল বিষয়</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>" <?= ($selectedCategory == $category) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                     <!-- Reset button -->
                     <?php if (!empty($searchTerm) || $selectedCategory !== 'all'): ?>
                        <a href="rulings.php" class="reset-button" aria-label="রিসেট ফিল্টার">রিসেট</a>
                     <?php endif; ?>
                </div>
            </form>
        </div>
    </section>

    <!-- === রুলিং ডিসপ্লে এলাকা (অ্যাকরডিয়ন) === -->
       <!-- === Rulings Display Area (Accordion) === -->
    <section class="rulings-display section-padding bg-light">
        <div class="container">
            <div class="rulings-accordion-container">

                <?php if (!empty($rulings)): ?>
                    <!-- Results Summary -->
                    <div class="results-summary animate__animated animate__fadeIn">
                        মোট <?= e(bangla_number($totalRulings)) ?> টি ফলাফলের মধ্যে <?= e(bangla_number(count($rulings))) ?> টি দেখানো হচ্ছে (পেজ <?= e(bangla_number($page)) ?> / <?= e(bangla_number($totalPages)) ?>)
                    </div>

                    <!-- Accordion Items Loop -->
                   <?php foreach ($rulings as $index => $ruling):
                        // Process keywords string into an array, filtering out empty values
                        $keywordsArray = (!empty($ruling['keywords'])) ? array_filter(array_map('trim', explode(',', $ruling['keywords']))) : [];
                        // Set animation delay for staggered effect
                        $animationDelay = $index * 0.07;
                    ?>
                        <div class="accordion-item animate__animated animate__fadeInUp"
                             data-category="<?= e($ruling['category']) ?>"
                             style="animation-delay: <?= $animationDelay ?>s;">

                            <button class="accordion-header"
                                    id="header-<?= e($ruling['id']) ?>"
                                    aria-expanded="false"
                                    aria-controls="content-<?= e($ruling['id']) ?>">
 <!-- ... (Header Icon, Title, Citation, Indicator - আগের মতই) ... -->
                                <span class="header-icon-wrapper" title="ক্যাটাগরি: <?= e($ruling['category']) ?>"> <i class="<?= e($ruling['icon'] ?? 'fas fa-landmark') ?> fa-fw"></i> </span>
                                 <span class="header-title"><?= e($ruling['title']) ?></span>
                                 <?php if(!empty($ruling['citation'])): ?> <span class="header-citation" title="সাইটেশন">(<?= e($ruling['citation']) ?>)</span> <?php endif; ?>
                                 <span class="accordion-indicator"><i class="fas fa-chevron-down"></i></span>
                            </button>

                            <div id="content-<?= e($ruling['id']) ?>"
                                 class="accordion-content"
                                 role="region"
                                 aria-labelledby="header-<?= e($ruling['id']) ?>"
                                 hidden>
                                <div class="content-inner">

                                    <!-- ক্যাটাগরি (প্রথমে দেখানো ভালো) -->
                                    <div class="content-meta category-display">
                                        <strong><i class="fas fa-tag"></i> ক্যাটাগরি:</strong>
                                        <a href="?category=<?= urlencode($ruling['category']) ?>&search=<?= urlencode($searchTerm) ?>" class="category-link"><?= e($ruling['category']) ?></a>
                                    </div>

                              
                                    <!-- Summary Section -->
                                    <!-- সারসংক্ষেপ -->
                                    <h4><i class="fas fa-align-left"></i> সারসংক্ষেপ:</h4>
                                    <div class="summary-content">
                                         <?= nl2br(e($ruling['summary'])) ?>
                                    </div>

                                    <!-- Keywords Section -->
                                    <!-- কীওয়ার্ডস (যদি থাকে) -->
                                    <?php if (!empty($keywordsArray)): ?>
                                        <div class="keywords-section">
                                            <strong><i class="fas fa-tags"></i> কীওয়ার্ডস:</strong>
                                            <?php foreach ($keywordsArray as $keyword): ?>
                                                <a href="?search=<?= urlencode($keyword) ?>" class="keyword-tag"><?= e($keyword) ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>


                                    <!-- Keywords Section (Only if keywords exist) -->
                                    <?php if (!empty($keywordsArray)): ?>
                                        <div class="keywords-section">
                                            <strong><i class="fas fa-tags"></i> কীওয়ার্ডস:</strong>
                                            <?php foreach ($keywordsArray as $keyword): ?>
                                                <a href="?search=<?= urlencode($keyword) ?>" class="keyword-tag"><?= e($keyword) ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                     <!-- পূর্ণাঙ্গ জাজমেন্ট লিংক (যদি থাকে) -->
                                    <?php if (!empty($ruling['judgment_url'])): ?>
                                         <div class="judgment-link-wrapper">
    <a href="<?= e($ruling['judgment_url']) ?>" 
       target="_blank" 
       rel="noopener noreferrer"
       class="judgment-link"
       aria-label="পূর্ণাঙ্গ রায়ের লিংক">
        <span class="judgment-icon"><i class="fas fa-scale-balanced"></i></span>
        <span class="judgment-text">পূর্ণ রায় দেখুন</span>
        <span class="external-link-icon"><i class="fas fa-arrow-up-right-from-square"></i></span>
    </a>
</div>
                                    <?php endif; ?>
                                    <!-- জাজমেন্ট লিংক সেকশন শেষ -->
                                    <!-- End Judgment Link Section -->
                                </div><!-- /.content-inner -->
                            </div><!-- /.accordion-content -->
                        </div><!-- /.accordion-item -->
                    <?php endforeach; ?>
                    <!-- End Accordion Items Loop -->

                    <!-- Pagination Links -->
                    <?php if ($totalPages > 1): ?>
                       <!-- ... পেজিনেশন লিঙ্ক HTML (আগের মতই) ... -->
                        <nav class="pagination-wrapper animate__animated animate__fadeInUp" style="animation-delay: <?= ($index + 1) * 0.05 ?>s;" aria-label="Page navigation">
                            <ul class="pagination">
                                <!-- Previous -->
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>"> <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
                                <!-- Pages -->
                                <?php $linksToShow = 5; $start = max(1, $page - floor($linksToShow / 2)); $end = min($totalPages, $start + $linksToShow - 1); $start = max(1, $end - $linksToShow + 1);?>
                                <?php if ($start > 1): ?> <li class="page-item"><a class="page-link" href="?page=1&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>">1</a></li> <?php if ($start > 2): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?> <?php endif; ?>
                                <?php for ($i = $start; $i <= $end; $i++): ?> <li class="page-item <?= ($i == $page) ? 'active' : '' ?>"> <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>"><?= e(bangla_number($i)) ?></a> </li> <?php endfor; ?>
                                <?php if ($end < $totalPages): ?> <?php if ($end < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">...</span></li><?php endif; ?> <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>"><?= e(bangla_number($totalPages)) ?></a></li> <?php endif; ?>
                                <!-- Next -->
                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>"> <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>" aria-label="Next"><span aria-hidden="true">»</span></a></li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    <!-- End Pagination -->

                <?php elseif (isset($pdo) && !$dbQueryError): // Check if connection exists and no query error ?>
                     <!-- Message if no rulings found -->
                     <div class="no-result-message text-center animate__animated animate__fadeIn">
                         <i class="fas fa-info-circle icon-large"></i>
                         <p>দুঃখিত, <?= (!empty($searchTerm) || $selectedCategory !== 'all') ? 'আপনার অনুসন্ধান বা ফিল্টারের সাথে মিলে এমন কোনো' : 'এই মুহূর্তে কোনো' ?> সিদ্ধান্ত পাওয়া যায়নি।</p>
                         <?php if (!empty($searchTerm) || $selectedCategory !== 'all'): ?>
                             <a href="rulings.php" class="button button-outline button-small"><i class="fas fa-times"></i> সকল ফিল্টার সরান</a>
                         <?php endif; ?>
                     </div>
                <?php endif; // End if !empty($rulings) ?>

                 <!-- Display general DB connection or query error if $pdo wasn't set or $dbQueryError is true -->
                 <?php if (!isset($pdo) || $dbQueryError): ?>
                     <div class="no-result-message text-center text-danger animate__animated animate__fadeIn">
                         <i class="fas fa-database icon-large"></i>
                         <!-- Display the session error message if available -->
                         <p><?= isset($_SESSION['error_message']) ? e($_SESSION['error_message']) : 'দুঃখিত, সিদ্ধান্তগুলো লোড করার সময় একটি সমস্যা হয়েছে।' ?></p>
                         <?php if(isset($_SESSION['error_message'])) unset($_SESSION['error_message']); // Clear message after display ?>
                     </div>
                <?php endif; ?>

            </div> <!-- /.rulings-accordion-container -->
        </div> <!-- /.container -->
    </section> <!-- /.rulings-display -->

<?php
// === ফুটার লোড ===
// Load Footer
require PARTIALS_PATH . 'footer.php';
?>