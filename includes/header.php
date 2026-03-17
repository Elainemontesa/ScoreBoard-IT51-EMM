<?php
if (!isset($pageTitle)) {
    $pageTitle = 'ScoreBoard';
}
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/game_helpers.php';
$user = currentUser($pdo);
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | ScoreBoard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top scoreboard-nav">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>/index.php">ScoreBoard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/diary.php">Diary</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/games.php">Games</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/reviews.php">User Reviews</a></li>
                <?php if ($user): ?>
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/notifications.php">Notifications</a></li>
                <?php endif; ?>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($user): ?>
                    <form class="d-flex me-2" method="GET" action="<?= BASE_URL ?>/search_users.php">
                        <input class="form-control form-control-sm bg-transparent text-light" type="search" name="q" placeholder="Search players" aria-label="Search players">
                    </form>
                    <a href="<?= BASE_URL ?>/profile.php" class="btn btn-outline-light btn-sm"><?= e($user['display_name']) ?></a>
                    <a href="<?= BASE_URL ?>/logout.php" class="btn btn-warning btn-sm">Logout</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/signin.php" class="btn btn-outline-light btn-sm">Sign In</a>
                    <a href="<?= BASE_URL ?>/signup.php" class="btn btn-warning btn-sm">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<?php if ($flash): ?>
<div class="container pt-3">
    <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>
