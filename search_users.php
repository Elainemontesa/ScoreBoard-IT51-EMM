<?php
$pageTitle = 'Find Players';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$q = trim($_GET['q'] ?? '');
$results = [];

if ($q !== '') {
    $like = "%$q%";
    $stmt = $pdo->prepare('SELECT u.id, u.display_name, u.username,
        EXISTS(
            SELECT 1 FROM follows f
            WHERE f.follower_id = ? AND f.following_id = u.id
        ) AS is_following
        FROM users u
        WHERE (u.display_name LIKE ? OR u.username LIKE ?) AND u.id != ?
        ORDER BY u.display_name ASC');
    $stmt->execute([$user['id'], $like, $like, $user['id']]);
    $results = $stmt->fetchAll();
}
?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <h1 class="fw-bold mb-1">Find Players</h1>
            <p class="text-muted-custom mb-0">Search for other users to follow.</p>
        </div>
        <form class="d-flex gap-2" method="GET">
            <input type="text" name="q" class="form-control" placeholder="Search by name or username" value="<?= e($q) ?>">
            <button class="btn btn-warning">Search</button>
        </form>
    </div>

    <?php if ($q !== ''): ?>
        <div class="mb-3 text-muted-custom">Search results for: <strong class="text-white"><?= e($q) ?></strong></div>
    <?php endif; ?>

    <div class="row g-3">
        <?php foreach ($results as $row): ?>
            <div class="col-12">
                <div class="list-card d-flex justify-content-between align-items-center">
                    <a href="<?= BASE_URL ?>/user_profile.php?id=<?= (int)$row['id'] ?>" class="text-decoration-none text-reset">
                        <div>
                            <div class="fw-semibold"><?= e($row['display_name']) ?></div>
                            <div class="small text-muted-custom">@<?= e($row['username']) ?></div>
                        </div>
                    </a>
                    <form method="POST" action="<?= BASE_URL ?>/follow_toggle.php" class="d-inline">
                        <input type="hidden" name="target_id" value="<?= (int)$row['id'] ?>">
                        <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? (BASE_URL . '/search_users.php')) ?>">
                        <?php if ((int)$row['is_following'] === 1): ?>
                            <button class="btn btn-outline-light btn-sm" type="submit">Following</button>
                        <?php else: ?>
                            <button class="btn btn-warning btn-sm" type="submit">Follow</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if ($q !== '' && empty($results)): ?>
            <div class="col-12">
                <div class="alert alert-secondary">No users found.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
