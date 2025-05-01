<?php
// admin/rulings/delete.php
require_once '../includes/functions.php';
require_admin_login();
require_once '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'অবৈধ অনুরোধ পদ্ধতি।';
    header('Location: index.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $_SESSION['error_message'] = 'অবৈধ বা মেয়াদোত্তীর্ণ অনুরোধ।';
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $_SESSION['error_message'] = 'অবৈধ আইডি।';
    header('Location: index.php');
    exit;
}

try {
    // ডিলিট করার আগে চেক করা যেতে পারে যে এটি আসলেই আছে কিনা
    $stmt = $pdo->prepare("DELETE FROM rulings WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = 'সিদ্ধান্ত সফলভাবে ডিলিট করা হয়েছে।';
    } else {
         $_SESSION['error_message'] = 'সিদ্ধান্ত খুঁজে পাওয়া যায়নি বা ডিলিট করা সম্ভব হয়নি।';
    }
} catch (PDOException $e) {
    error_log("Ruling Delete Error: " . $e->getMessage());
    $_SESSION['error_message'] = 'ডিলিট করার সময় সমস্যা হয়েছে।';
}

header('Location: index.php'); // লিস্ট পেজে ফেরত
exit;
?>