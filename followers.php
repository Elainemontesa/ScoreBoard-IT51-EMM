<?php
$pageTitle = 'Followers';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$followersStmt = $pdo->prepare('SELECT u.*, EXISTS(SELECT 1 FROM follows f2 WHERE f2.follower_id = ? AND f2.following_id = u.id) AS is_following_back
FROM follows f
JOIN users u ON u.id = f.follower_id
WHERE f.following_id = ?');
$followersStmt->execute([$user['id'], $user['id']]);
$followers = $followersStmt->fetchAll();

$followingStmt = $pdo->prepare('SELECT u.* FROM follows f JOIN users u ON u.id = f.following_id WHERE f.follower_id = ?');
$followingStmt->execute([$user['id']]);
$following = $followingStmt->fetchAll();
?>
<div class="container py-5">
    <div class="profile-banner mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="small text-muted-custom">Profile</div>
                <h1 class="fw-bold mb-1"><?= e($user['display_name']) ?></h1>
                <div class="text-muted-custom">@<?= e($user['username']) ?></div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>/edit_profile.php" class="btn btn-outline-light">Edit Profile</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="list-card h-100">
                <h3 class="fw-bold mb-3">Followers</h3>
                <?php foreach ($followers as $f): ?>
                    <div class="border-bottom border-secondary-subtle py-2 d-flex justify-content-between align-items-center">
                        <a href="<?= BASE_URL ?>/user_profile.php?id=<?= (int)$f['id'] ?>" class="text-decoration-none text-reset">
                            <div>
                                <div class="fw-semibold"><?= e($f['display_name']) ?></div>
                                <div class="small text-muted-custom">@<?= e($f['username']) ?></div>
                            </div>
                        </a>
                        <?php if ($f['id'] != $user['id']): ?>
                            <form method="POST" action="<?= BASE_URL ?>/follow_toggle.php" class="d-inline">
                                <input type="hidden" name="target_id" value="<?= (int)$f['id'] ?>">
                                <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? (BASE_URL . '/followers.php')) ?>">
                                <?php if ((int)$f['is_following_back'] === 1): ?>
                                    <button class="btn btn-outline-light btn-sm" type="submit">Following</button>
                                <?php else: ?>
                                    <button class="btn btn-warning btn-sm" type="submit">Follow back</button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="list-card h-100">
                <h3 class="fw-bold mb-3">Following</h3>
                <?php foreach ($following as $f): ?>
                    <div class="border-bottom border-secondary-subtle py-2 d-flex justify-content-between align-items-center">
                        <a href="<?= BASE_URL ?>/user_profile.php?id=<?= (int)$f['id'] ?>" class="text-decoration-none text-reset">
                            <div>
                                <div class="fw-semibold"><?= e($f['display_name']) ?></div>
                                <div class="small text-muted-custom">@<?= e($f['username']) ?></div>
                            </div>
                        </a>
                        <?php if ($f['id'] != $user['id']): ?>
                            <form method="POST" action="<?= BASE_URL ?>/follow_toggle.php" class="d-inline">
                                <input type="hidden" name="target_id" value="<?= (int)$f['id'] ?>">
                                <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? (BASE_URL . '/followers.php')) ?>">
                                <button class="btn btn-outline-light btn-sm" type="submit">Following</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
