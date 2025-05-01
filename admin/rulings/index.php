<?php
// admin/rulings/index.php
$adminPageTitle = 'সিদ্ধান্তসমূহের তালিকা';
$currentPage = 'rulings';
require_once '../includes/functions.php';
require_admin_login();
require_once '../../config/db.php';
require_once '../includes/header.php'; // অ্যাডমিন হেডার

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT id, title, category, citation, is_active FROM rulings";
$params = [];
if ($statusFilter === 'active') { $sql .= " WHERE is_active = 1"; }
elseif ($statusFilter === 'inactive') { $sql .= " WHERE is_active = 0"; }
$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql); $stmt->execute($params); $rulings = $stmt->fetchAll();
} catch (PDOException $e) { $_SESSION['error_message'] = "ত্রুটি: " . $e->getMessage(); $rulings = []; }
?>

<div style="margin-bottom: 1rem; text-align: right;">
    <a href="add.php" class="button"><i class="fas fa-plus"></i> নতুন যোগ করুন</a>
</div>

<?php if (!empty($rulings)): ?>
<table class="content-table">
    <thead> <!-- টেবিল হেডার --> </thead>
    <tbody>
        <?php foreach ($rulings as $ruling): ?>
        <tr>
            <td><?= e($ruling['title']) ?></td>
            <td><?= e($ruling['category']) ?></td>
            <td><?= e($ruling['citation'] ?? 'N/A') ?></td>
            <td class="actions">
                <?= $ruling['is_active'] ? '<span class="status-active">সক্রিয়</span>' : '<span class="status-inactive">নিষ্ক্রিয়</span>' ?>
            </td>
            <td class="actions">
                <a href="edit.php?id=<?= $ruling['id'] ?>" class="edit-link" title="এডিট"><i class="fas fa-edit"></i></a>
                <!-- ডিলিট বাটন এখন ফর্ম দিয়ে হবে -->
                <form method="POST" action="delete.php" style="display: inline-block;" class="delete-form">
                    <input type="hidden" name="id" value="<?= $ruling['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= e($csrf_token) ?>">
                    <button type="submit" class="delete-link" title="ডিলিট"><i class="fas fa-trash-alt"></i></button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
    <p>কোনো সিদ্ধান্ত খুঁজে পাওয়া যায়নি।</p>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>