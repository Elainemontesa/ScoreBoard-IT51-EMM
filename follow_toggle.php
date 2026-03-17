<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$user = currentUser($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('danger', 'Invalid request.');
    header('Location: ' . BASE_URL . '/profile.php');
    exit;
}

$targetId = (int)($_POST['target_id'] ?? 0);
$redirect = trim($_POST['redirect'] ?? '');
if ($redirect === '') {
    $redirect = BASE_URL . '/followers.php';
}

if ($targetId <= 0 || $targetId === (int)$user['id']) {
    flash('danger', 'Invalid user to follow.');
    header('Location: ' . $redirect);
    exit;
}

// Ensure target user exists
$uStmt = $pdo->prepare('SELECT id, display_name FROM users WHERE id = ? LIMIT 1');
$uStmt->execute([$targetId]);
$target = $uStmt->fetch();
if (!$target) {
    flash('danger', 'User not found.');
    header('Location: ' . $redirect);
    exit;
}

// Check current follow state
$check = $pdo->prepare('SELECT id FROM follows WHERE follower_id = ? AND following_id = ? LIMIT 1');
$check->execute([$user['id'], $targetId]);
$existing = $check->fetch();

if ($existing) {
    // Unfollow
    $del = $pdo->prepare('DELETE FROM follows WHERE id = ? AND follower_id = ?');
    $del->execute([$existing['id'], $user['id']]);
    flash('success', 'You unfollowed ' . $target['display_name'] . '.');
} else {
    // Follow
    $ins = $pdo->prepare('INSERT INTO follows (follower_id, following_id) VALUES (?, ?)');
    $ins->execute([$user['id'], $targetId]);

    // Optional notification for the target user
    $msg = $user['display_name'] . ' started following you.';
    $notif = $pdo->prepare('INSERT INTO notifications (user_id, message, action_label) VALUES (?, ?, ?)');
    $notif->execute([$targetId, $msg, 'Follow']);

    flash('success', 'You are now following ' . $target['display_name'] . '.');
}

header('Location: ' . $redirect);
exit;
