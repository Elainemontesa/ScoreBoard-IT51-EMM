<?php
$pageTitle = 'Profile';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$statsStmt = $pdo->prepare('SELECT 
    COUNT(CASE WHEN status = "completed" THEN 1 END) AS completed_count,
    COALESCE(SUM(playtime_hours), 0) AS total_playtime,
    COUNT(CASE WHEN status = "backlog" THEN 1 END) AS backlog_count,
    COUNT(CASE WHEN liked = 1 THEN 1 END) AS liked_count
    FROM diary_entries WHERE user_id = ?');
$statsStmt->execute([$user['id']]);
$stats = $statsStmt->fetch();

$followStmt = $pdo->prepare('SELECT
    (SELECT COUNT(*) FROM follows WHERE following_id = ?) AS followers,
    (SELECT COUNT(*) FROM follows WHERE follower_id = ?) AS following_total');
$followStmt->execute([$user['id'], $user['id']]);
$follow = $followStmt->fetch();

$recentStmt = $pdo->prepare('SELECT g.title, d.playtime_hours, d.last_played_at
FROM diary_entries d
JOIN games g ON g.id = d.game_id
WHERE d.user_id = ?
ORDER BY d.last_played_at DESC
LIMIT 4');
$recentStmt->execute([$user['id']]);
$recent = $recentStmt->fetchAll();

$likedStmt = $pdo->prepare('SELECT g.title
FROM diary_entries d
JOIN games g ON g.id = d.game_id
WHERE d.user_id = ? AND d.liked = 1
LIMIT 4');
$likedStmt->execute([$user['id']]);
$likedGames = $likedStmt->fetchAll();
?>
<div class="container py-5">
    <div class="profile-banner mb-4">
        <div class="row align-items-center g-4">
            <div class="col-md-auto text-center">
                <span class="avatar-circle"><?= e(strtoupper(substr($user['display_name'], 0, 1))) ?></span>
            </div>
            <div class="col">
                <h1 class="fw-bold mb-1"><?= e($user['display_name']) ?></h1>
                <p class="text-muted-custom mb-2">@<?= e($user['username']) ?></p>
                <p class="mb-0"><?= e($user['bio']) ?></p>
            </div>
            <div class="col-md-auto text-md-end">
                <a href="<?= BASE_URL ?>/edit_profile.php" class="btn btn-outline-light me-2">Edit Profile</a>
                <a href="<?= BASE_URL ?>/followers.php" class="btn btn-outline-light me-2"><?= (int)$follow['followers'] ?> Followers</a>
                <a href="<?= BASE_URL ?>/followers.php" class="btn btn-warning"><?= (int)$follow['following_total'] ?> Following</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3"><div class="stat-box"><div class="small text-muted-custom">Completed</div><div class="h3 fw-bold"><?= (int)$stats['completed_count'] ?></div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-box"><div class="small text-muted-custom">Playtime</div><div class="h3 fw-bold"><?= number_format((float)$stats['total_playtime'], 1) ?>h</div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-box"><div class="small text-muted-custom">Backlogs</div><div class="h3 fw-bold"><?= (int)$stats['backlog_count'] ?></div></div></div>
        <div class="col-6 col-lg-3"><div class="stat-box"><div class="small text-muted-custom">Liked</div><div class="h3 fw-bold"><?= (int)$stats['liked_count'] ?></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="game-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Recently Played</h4>
                    <a href="<?= BASE_URL ?>/diary.php">My Diary</a>
                </div>
                <?php foreach ($recent as $item): ?>
                    <div class="border-bottom border-secondary-subtle py-2 d-flex gap-3 align-items-center">
                        <img src="<?= gamePlaceholderPath($item['title']) ?>" alt="<?= e($item['title']) ?> placeholder cover" class="game-cover-sm">
                        <div>
                            <div class="fw-semibold"><?= e($item['title']) ?></div>
                            <div class="small text-muted-custom"><?= number_format((float)$item['playtime_hours'], 1) ?> hours · Last played <?= e(date('M d, Y', strtotime($item['last_played_at']))) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="game-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Liked Games</h4>
                    <a href="<?= BASE_URL ?>/reviews.php">My Review</a>
                </div>
                <?php foreach ($likedGames as $item): ?>
                    <div class="border-bottom border-secondary-subtle py-2 d-flex gap-3 align-items-center">
                        <img src="<?= gamePlaceholderPath($item['title']) ?>" alt="<?= e($item['title']) ?> placeholder cover" class="game-cover-sm">
                        <div class="fw-semibold"><?= e($item['title']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
