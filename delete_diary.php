<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$user = currentUser($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM diary_entries WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);
    flash('success', 'Diary entry deleted successfully.');
}
header('Location: ' . BASE_URL . '/diary.php');
exit;
?>
