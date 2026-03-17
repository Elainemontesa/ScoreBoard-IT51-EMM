<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$user = currentUser($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare('DELETE FROM reviews WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);
    flash('success', 'Review deleted successfully.');
}
header('Location: ' . BASE_URL . '/reviews.php');
exit;
?>
