<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

requireLogin();
$user = currentUser($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('danger', 'Invalid request method.');
    header('Location: ' . BASE_URL . '/games.php');
    exit;
}

$gameId = (int)($_POST['game_id'] ?? 0);
$redirect = trim($_POST['redirect'] ?? '');
if ($redirect === '') {
    $redirect = BASE_URL . '/game.php?id=' . $gameId;
}

if ($gameId <= 0) {
    flash('danger', 'Invalid game selected.');
    header('Location: ' . $redirect);
    exit;
}

// Ensure game exists
$stmt = $pdo->prepare('SELECT id FROM games WHERE id = ? LIMIT 1');
$stmt->execute([$gameId]);
$game = $stmt->fetch();
if (!$game) {
    flash('danger', 'Game not found.');
    header('Location: ' . BASE_URL . '/games.php');
    exit;
}

// Check for existing diary entry for this user and game
$entryStmt = $pdo->prepare('SELECT id, liked, status, playtime_hours, last_played_at, achievement_notes FROM diary_entries WHERE user_id = ? AND game_id = ? LIMIT 1');
$entryStmt->execute([$user['id'], $gameId]);
$entry = $entryStmt->fetch();

if ($entry) {
    // Toggle like on existing diary entry
    $newLiked = (int)!((int)$entry['liked'] === 1);
    $update = $pdo->prepare('UPDATE diary_entries SET liked = ? WHERE id = ? AND user_id = ?');
    $update->execute([$newLiked, $entry['id'], $user['id']]);
    if ($newLiked === 1) {
        flash('success', 'You liked this game.');
    } else {
        flash('success', 'You unliked this game.');
    }
} else {
    // Create a minimal diary entry marked as liked
    $insert = $pdo->prepare('INSERT INTO diary_entries (user_id, game_id, status, playtime_hours, liked, last_played_at, achievement_notes) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $insert->execute([
        $user['id'],
        $gameId,
        'playing',
        0.0,
        1,
        date('Y-m-d H:i:s'),
        '',
    ]);
    flash('success', 'Game added to your diary as liked.');
}

header('Location: ' . $redirect);
exit;
