<?php
/**
 * admin/index.php - অ্যাডমিন ড্যাশবোর্ড পৃষ্ঠা
 * 
 * এই ফাইলটি অ্যাডমিন প্যানেলের প্রধান পৃষ্ঠা। অ্যাডমিনরা এখানে সিদ্ধান্তের পরিসংখ্যান দেখতে এবং 
 * বিভিন্ন কার্যক্রমে যেতে পারেন।
 * 
 * প্রধান কার্যাবলী:
 * - অ্যাডমিন লগইন যাচাই
 * - ডাটাবেস থেকে সিদ্ধান্তের সংখ্যা গণনা (সক্রিয়/নিষ্ক্রিয়)
 * - ড্যাশবোর্ড UI প্রদর্শন
 */

/** 
 * ১. ধ্রুবক সংজ্ঞায়িত করা 
 * - এই ধ্রুবকগুলো ফাইল পাথ নির্ধারণে সাহায্য করে যাতে কোড সঠিক ফাইলগুলো খুঁজে পায়।
 * - __DIR__ বর্তমান ডিরেক্টরি নির্দেশ করে, dirname() প্যারেন্ট ডিরেক্টরি পায়।
 */
 
define('ADMIN_BASE_PATH', __DIR__);
define('ADMIN_INCLUDES_PATH', ADMIN_BASE_PATH . '/includes/');
define('ROOT_PATH', dirname(__DIR__));
define('ROOT_CONFIG_PATH', ROOT_PATH . '/config/');


/** 
 * ২. পৃষ্ঠার প্রাথমিক সেটআপ 
 * - পৃষ্ঠার শিরোনাম এবং বর্তমান পৃষ্ঠা নির্ধারণ করে UI-এর জন্য।
 */
const PAGE_TITLE = 'ড্যাশবোর্ড';
const CURRENT_PAGE = 'dashboard';

/**
 * লগইন যাচাই এবং ফাংশন লোড করা
 * - functions.php ফাইল থেকে সাধারণ ফাংশন লোড করে এবং লগইন চেক করে।
 * - try-catch ব্যবহার করে ত্রুটি ধরা হয় এবং লগ করা হয়।
 */
function initialize_admin_session() {
    try {
        require_once ADMIN_INCLUDES_PATH . 'functions.php';
        require_admin_login(); // লগইন না থাকলে রিডাইরেক্ট করে
    } catch (Exception $e) {
        error_log("ত্রুটি: ফাংশন ফাইল লোড ব্যর্থ। " . $e->getMessage());
        die("সিস্টেম ত্রুটি। অ্যাডমিনের সাথে যোগাযোগ করুন।");
    }
}

/**
 * ডাটাবেস সংযোগ স্থাপন
 * - db.php থেকে PDO অবজেক্ট লোড করে ডাটাবেসের সাথে সংযোগ করে।
 * - ত্রুটি হলে সেশনে মেসেজ সেট করে।
 * @return PDO|null
 */
function connect_to_database() {
    $pdo = null;
    try {
        require_once ROOT_CONFIG_PATH . 'db.php';
        return $pdo;
    } catch (Exception $e) {
        error_log("ডাটাবেস ত্রুটি: " . $e->getMessage());
        $_SESSION['error_message'] = "ডাটাবেস সংযোগে সমস্যা।";
        return null;
    }
}

/**
 * হেডার লোড করা
 * - header.php ফাইল লোড করে UI-এর উপরের অংশ প্রদর্শন করে।
 * - ত্রুটি হলে ম্যানুয়াল HTML আউটপুট করে।
 */
function load_header() {
    try {
        require_once ADMIN_INCLUDES_PATH . 'header.php';
    } catch (Exception $e) {
        error_log("হেডার ত্রুটি: " . $e->getMessage());
        echo "<!DOCTYPE html><html><head><title>ত্রুটি</title></head><body>";
        echo "<h1>সিস্টেম ত্রুটি</h1><p>হেডার লোডে সমস্যা।</p>";
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        die("</body></html>");
    }
}

/**
 * সিদ্ধান্তের পরিসংখ্যান গণনা
 * - PDO ব্যবহার করে rulings টেবিল থেকে সক্রিয় এবং নিষ্ক্রিয় সিদ্ধান্ত গণনা করে।
 * - ত্রুটি হলে UI-তে সতর্কতা দেখায়।
 * @param PDO|null $pdo
 * @return array [active, inactive, error]
 */
function fetch_rulings_stats($pdo) {
    $active = 0;
    $inactive = 0;
    $error = false;

    if ($pdo) {
        try {
            $stmt = $pdo->query("
                SELECT 
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) AS active_count,
                    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) AS inactive_count
                FROM rulings
            ");
            $counts = $stmt->fetch(PDO::FETCH_ASSOC);
            $active = (int)($counts['active_count'] ?? 0);
            $inactive = (int)($counts['inactive_count'] ?? 0);
        } catch (PDOException $e) {
            $error = true;
            error_log("কোয়েরি ত্রুটি: " . $e->getMessage());
        }
    } else {
        $error = true;
        error_log("PDO অনুপলব্ধ।");
    }

    return [$active, $inactive, $error];
}

// প্রোগ্রাম শুরু
initialize_admin_session();
$pdo = connect_to_database();
load_header();

// সেশন মেসেজ প্রদর্শন
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . e($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}

// পরিসংখ্যান সংগ্রহ
[$activeRulingsCount, $inactiveRulingsCount, $dbQueryError] = fetch_rulings_stats($pdo);
if ($dbQueryError) {
    echo '<div class="alert alert-warning">ড্যাশবোর্ড তথ্য আনতে সমস্যা।</div>';
}
?>

<!-- ড্যাশবোর্ড UI -->
<div class="dashboard-container">
    <div class="welcome-section">
        <h1><?= e(PAGE_TITLE) ?></h1>
        <p>অ্যাডমিন প্যানেলে স্বাগতম। ওয়েবসাইট পরিচালনা করুন।</p>
    </div>

    <div class="dashboard-stats">
        <div class="stat-card animate__animated animate__fadeInUp delay-1">
            <h3>সক্রিয় সিদ্ধান্ত</h3>
            <p class="stat-number"><?= e($activeRulingsCount) ?></p>
            <a href="rulings/?status=active" class="stat-link"><i class="fas fa-eye"></i> দেখুন</a>
        </div>
        <div class="stat-card animate__animated animate__fadeInUp delay-2">
            <h3>নিষ্ক্রিয় সিদ্ধান্ত</h3>
            <p class="stat-number"><?= e($inactiveRulingsCount) ?></p>
            <a href="rulings/?status=inactive" class="stat-link"><i class="fas fa-eye"></i> দেখুন</a>
        </div>
        <div class="stat-card animate__animated animate__fadeInUp delay-3">
            <h3>মোট সিদ্ধান্ত</h3>
            <p class="stat-number"><?= e($activeRulingsCount + $inactiveRulingsCount) ?></p>
            <a href="rulings/" class="stat-link"><i class="fas fa-list"></i> সব দেখুন</a>
        </div>
    </div>

    <div class="quick-links-section">
        <h2>দ্রুত লিংকসমূহ</h2>
        <ul class="quick-links">
            <li class="animate__animated animate__fadeInUp delay-4">
                <a href="rulings/add.php" class="button"><i class="fas fa-plus"></i> নতুন সিদ্ধান্ত</a>
            </li>
            <li class="animate__animated animate__fadeInUp delay-5">
                <a href="rulings/" class="button button-secondary"><i class="fas fa-list"></i> সকল সিদ্ধান্ত</a>
            </li>
            <li class="animate__animated animate__fadeInUp delay-6">
                <a href="settings.php" class="button button-outline"><i class="fas fa-cog"></i> সেটিংস</a>
            </li>
        </ul>
    </div>
</div>

<?php
// ফুটার লোড
require_once ADMIN_INCLUDES_PATH . 'footer.php';
?>