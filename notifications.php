<?php
$pageTitle = 'Notifications';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$user['id']]);
$items = $stmt->fetchAll();

// Follow activity built from follows table so entries are always up to date and clickable
$followsStmt = $pdo->prepare('SELECT u.id, u.display_name, u.username, f.created_at,
    EXISTS(
        SELECT 1 FROM follows f2
        WHERE f2.follower_id = ? AND f2.following_id = u.id
    ) AS is_following_back
FROM follows f
JOIN users u ON u.id = f.follower_id
WHERE f.following_id = ?
ORDER BY f.created_at DESC');
$followsStmt->execute([$user['id'], $user['id']]);
$followActivity = $followsStmt->fetchAll();
?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold mb-1">Notifications</h1>
            <p class="text-muted-custom mb-0">Follow activity, likes, reposts, and community updates.</p>
        </div>
    </div>

    <?php if ($followActivity): ?>
        <h5 class="fw-bold mb-3">Follow Activity</h5>
        <div class="row g-3 mb-4">
            <?php foreach ($followActivity as $f): ?>
                <div class="col-12">
                    <div class="notif-card d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <a href="<?= BASE_URL ?>/user_profile.php?id=<?= (int)$f['id'] ?>" class="text-decoration-none text-reset flex-grow-1">
                            <div>
                                <div class="fw-semibold"><?= e($f['display_name']) ?> started following you.</div>
                                <div class="small text-muted-custom"><?= e(date('M d, Y h:i A', strtotime($f['created_at']))) ?></div>
                            </div>
                        </a>
                        <?php if ($f['id'] != $user['id']): ?>
                            <form method="POST" action="<?= BASE_URL ?>/follow_toggle.php" class="d-inline">
                                <input type="hidden" name="target_id" value="<?= (int)$f['id'] ?>">
                                <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? (BASE_URL . '/notifications.php')) ?>">
                                <?php if ((int)$f['is_following_back'] === 1): ?>
                                    <button class="btn btn-outline-light btn-sm" type="submit">Followed</button>
                                <?php else: ?>
                                    <button class="btn btn-warning btn-sm" type="submit">Follow back</button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h5 class="fw-bold mb-3">Other Notifications</h5>
    <div class="row g-3">
        <?php foreach ($items as $item): ?>
            <div class="col-12">
                <div class="notif-card">
                    <div class="d-flex justify-content-between gap-3 flex-wrap">
                        <div>
                            <div class="fw-semibold"><?= e($item['message']) ?></div>
                            <div class="small text-muted-custom"><?= e(date('M d, Y h:i A', strtotime($item['created_at']))) ?></div>
                        </div>
                        <?php if ($item['action_label']): ?>
                            <button class="btn btn-outline-light btn-sm"><?= e($item['action_label']) ?></button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
