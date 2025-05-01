<?php
// admin/rulings/edit.php

/**
 * সিদ্ধান্ত সম্পাদনা পেজ
 * 
 * এই পেজটি অ্যাডমিনদের সিদ্ধান্তের তথ্য সম্পাদনা করতে দেয়।
 * 
 * প্রধান কার্যাবলী:
 * - অ্যাডমিন লগইন যাচাই
 * - ডাটাবেস থেকে সিদ্ধান্তের তথ্য আনা
 * - ফর্ম প্রক্রিয়াকরণ এবং আপডেট
 * - ত্রুটি ব্যবস্থাপনা
 * - UI প্রদর্শন
 */

/**
 * ১. ধ্রুবক সংজ্ঞায়িত করা
 * - DEFAULT_RULING_ICON: সিদ্ধান্তের জন্য ডিফল্ট আইকন সেট করে
 * - $adminPageTitle: পেজের শিরোনাম নির্ধারণ করে
 * - $currentPage: বর্তমান পেজের নাম চিহ্নিত করে
 */
define('DEFAULT_RULING_ICON', 'fa-landmark');
$adminPageTitle = 'সিদ্ধান্ত সম্পাদনা';
$currentPage = 'rulings';

/**
 * ২. প্রয়োজনীয় ফাংশন লোড এবং অথেনটিকেশন চেক
 * - functions.php ফাইল থেকে সাধারণ ফাংশন লোড করে
 * - require_admin_login() ফাংশন অ্যাডমিন লগইন যাচাই করে
 * - db.php ফাইল থেকে ডাটাবেস সংযোগ স্থাপন করে
 */
require_once __DIR__ . '/../includes/functions.php';
require_admin_login();
require_once __DIR__ . '/../../config/db.php';

/**
 * ৩. ভেরিয়েবল ইনিশিয়ালাইজেশন
 * - $errors: ত্রুটির তালিকা সংরক্ষণের জন্য একটি অ্যারে
 * - $ruling: সিদ্ধান্তের তথ্য সংরক্ষণের জন্য ভেরিয়েবল
 * - $categories: ক্যাটাগরির তালিকা সংরক্ষণের জন্য অ্যারে
 */
$errors = [];
$ruling = null;
$categories = [];

/**
 * ৪. আইডি যাচাই
 * - URL থেকে 'id' প্যারামিটার গ্রহণ করে এবং এটি সঠিক কিনা যাচাই করে
 * - অবৈধ হলে ত্রুটি বার্তা সহ ইনডেক্স পেজে রিডাইরেক্ট করে
 */
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $_SESSION['error_message'] = 'অবৈধ আইডি!';
    header('Location: index.php');
    exit;
}

/**
 * ৫. ডাটাবেস থেকে তথ্য আনা
 * - PDO ব্যবহার করে সিদ্ধান্তের তথ্য এবং ক্যাটাগরির তালিকা আনে
 * - ত্রুটি হলে লগ করে এবং প্রয়োজনে ত্রুটি বার্তা প্রদর্শন করে
 */
try {
    $stmt = $pdo->prepare("SELECT * FROM rulings WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $ruling = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ruling) {
        $_SESSION['error_message'] = 'সিদ্ধান্ত পাওয়া যায়নি!';
        header('Location: index.php');
        exit;
    }

    $categoryStmt = $pdo->query("SELECT DISTINCT category FROM rulings ORDER BY category ASC");
    $categories = $categoryStmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['error_message'] = 'ডেটাবেস ত্রুটি!';
    if (!$ruling) {
        header('Location: index.php');
        exit;
    }
    $errors[] = 'ক্যাটাগরি লোড ব্যর্থ!';
}

/**
 * ৬. ফর্ম প্রক্রিয়াকরণ
 * - ফর্ম সাবমিট হলে ডাটা যাচাই করে এবং ডাটাবেস আপডেট করে
 * - CSRF টোকেন যাচাই করে নিরাপত্তা নিশ্চিত করে
 * - ত্রুটি থাকলে ফর্ম ডাটা পুনরায় লোড করে
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'সিকিউরিটি ত্রুটি!';
    }

    $postedId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($postedId !== $id) {
        $errors[] = 'আইডি মিসম্যাচ!';
    }

    // ইনপুট স্যানিটাইজ করা
    $title    = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $icon     = trim($_POST['icon'] ?? '') ?: DEFAULT_RULING_ICON;
    $citation = trim($_POST['citation'] ?? '');
    $summary  = trim($_POST['summary'] ?? '');
    $keywords = trim($_POST['keywords'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    // যাচাই করা
    if (empty($title)) $errors[] = 'শিরোনাম আবশ্যক!';
    if (empty($category)) $errors[] = 'ক্যাটাগরি আবশ্যক!';
    if (empty($summary)) $errors[] = 'সারসংক্ষেপ আবশ্যক!';

    if (empty($errors)) {
        try {
            $sql = "UPDATE rulings SET
                title = :title,
                category = :category,
                icon = :icon,
                citation = :citation,
                summary = :summary,
                keywords = :keywords,
                is_active = :is_active,
                updated_at = NOW()
                WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $params = [
                ':title' => $title,
                ':category' => $category,
                ':icon' => $icon,
                ':citation' => $citation ?: null,
                ':summary' => $summary,
                ':keywords' => $keywords ?: null,
                ':is_active' => $isActive,
                ':id' => $id
            ];

            $stmt->execute($params);
            $_SESSION['success_message'] = 'আপডেট সফল!';
            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            error_log("Update Error: " . $e->getMessage());
            $errors[] = 'আপডেট ব্যর্থ!';
        }
    }

    // ফর্ম ডাটা পুনরায় লোড করা
    $ruling = array_merge($ruling, [
        'title' => $title,
        'category' => $category,
        'icon' => $icon,
        'citation' => $citation,
        'summary' => $summary,
        'keywords' => $keywords,
        'is_active' => $isActive
    ]);
}

$csrf_token = generate_csrf_token();
require_once __DIR__ . '/../includes/header.php';

/**
 * ৭. অতিরিক্ত হেড কন্টেন্ট
 * - বাহ্যিক লাইব্রেরি (Select2, TinyMCE, Tagify) লোড করে
 * - কাস্টম CSS স্টাইল সংজ্ঞায়িত করে UI উন্নত করে
 */
$additional_head_content = <<<HTML
<!-- External Libraries -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.tiny.cloud/1/YOUR-API-KEY/tinymce/6/tinymce.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">


HTML;
?>

/**
 * ৮. UI প্রদর্শন
 * - ফর্ম এবং এর উপাদানগুলো প্রদর্শন করে ব্যবহারকারীর জন্য
 * - ত্রুটি বার্তা দেখায় এবং ফর্ম ডাটা পূরণ করে
 */
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="modern-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="text-primary"><i class="fas fa-edit me-2"></i>সিদ্ধান্ত সম্পাদনা</h3>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="autoSave" checked>
                        <label class="form-check-label">অটো সেভ</label>
                    </div>
                </div>

                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                        <li><?= e($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form method="POST" id="editForm">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                    <input type="hidden" name="id" value="<?= e($id) ?>">

                    <!-- শিরোনাম ইনপুট -->
                    <div class="floating-label">
                        <input type="text" name="title" value="<?= e($ruling['title']) ?>" 
                               class="form-control" required maxlength="255">
                        <label>শিরোনাম *</label>
                        <div class="char-counter"><?= mb_strlen($ruling['title']) ?>/255</div>
                    </div>

                    <!-- ক্যাটাগরি সিলেক্ট -->
                    <div class="floating-label">
                        <select name="category" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat) ?>" <?= ($cat == $ruling['category']) ? 'selected' : '' ?>>
                                <?= e($cat) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <label>ক্যাটাগরি *</label>
                    </div>

                    <!-- আইকন পিকার -->
                    <div class="floating-label">
                        <input type="text" name="icon" value="<?= e($ruling['icon']) ?>" 
                               class="form-control" list="iconList">
                        <datalist id="iconList">
                            <option value="fa-landmark">
                            <option value="fa-gavel">
                            <option value="fa-balance-scale">
                        </datalist>
                        <div class="icon-grid mt-3">
                            <div class="icon-option" data-icon="fa-landmark"><i class="fas fa-landmark"></i></div>
                            <div class="icon-option" data-icon="fa-gavel"><i class="fas fa-gavel"></i></div>
                            <div class="icon-option" data-icon="fa-balance-scale"><i class="fas fa-balance-scale"></i></div>
                        </div>
                    </div>

                    <!-- রিচ টেক্সট এডিটর -->
                    <div class="floating-label">
                        <textarea name="summary" id="summaryEditor"><?= e($ruling['summary']) ?></textarea>
                        <div class="char-counter"><?= mb_strlen($ruling['summary']) ?> characters</div>
                    </div>

                    <!-- কীওয়ার্ডস ট্যাগিফাই -->
                    <div class="floating-label">
                        <input name="keywords" value="<?= e($ruling['keywords']) ?>" 
                               class="form-control tagify-input">
                        <label>কীওয়ার্ডস (কমা দ্বারা পৃথক)</label>
                    </div>

                    <!-- সক্রিয় স্ট্যাটাস -->
                    <div class="form-check form-switch my-4">
                        <input class="form-check-input" type="checkbox" name="is_active" 
                               <?= $ruling['is_active'] ? 'checked' : '' ?>>
                        <label class="form-check-label">সক্রিয়</label>
                    </div>

                    <!-- ফর্ম অ্যাকশনস -->
                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <a href="index.php" class="btn btn-secondary">বাতিল</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>আপডেট করুন
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

/**
 * ৯. জাভাস্ক্রিপ্ট ইনিশিয়ালাইজেশন
 * - TinyMCE এডিটর চালু করে টেক্সট ফরম্যাটিং এর জন্য
 * - Tagify চালু করে কীওয়ার্ড ট্যাগিং এর জন্য
 * - আইকন পিকার এবং অটো-সেভ ফিচার যোগ করে
 */
<script>
// TinyMCE ইনিশিয়ালাইজ
tinymce.init({
    selector: '#summaryEditor',
    plugins: 'lists link',
    toolbar: 'bold italic | bullist numlist | link',
    skin: document.documentElement.getAttribute('data-theme') === 'dark' ? 'oxide-dark' : 'oxide',
    content_css: document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'default'
});

// Tagify ইনিশিয়ালাইজ
new Tagify(document.querySelector('.tagify-input'), {
    delimiters: ", ",
    pattern: /^[\p{L}\s]{2,}$/u,
    dropdown: { 
        enabled: 1,
        maxItems: 10
    }
});

// আইকন পিকার লজিক
document.querySelectorAll('.icon-option').forEach(icon => {
    icon.addEventListener('click', () => {
        document.querySelector('input[name="icon"]').value = icon.dataset.icon;
    });
});

// অটো-সেভ ফিচার
const form = document.getElementById('editForm');
document.getElementById('autoSave').addEventListener('change', (e) => {
    if(e.target.checked) {
        setInterval(() => {
            localStorage.setItem('rulingDraft', new FormData(form));
        }, 5000);
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>