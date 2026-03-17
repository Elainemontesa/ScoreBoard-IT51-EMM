<?php
$pageTitle = 'My Reviews';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$stmt = $pdo->prepare('SELECT r.*, g.title
FROM reviews r
JOIN games g ON g.id = r.game_id
WHERE r.user_id = ?
ORDER BY r.created_at DESC');
$stmt->execute([$user['id']]);
$reviews = $stmt->fetchAll();
?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <h1 class="fw-bold mb-1">My Review</h1>
            <p class="text-muted-custom mb-0">Community-friendly thoughts on the games you played.</p>
        </div>
        <a href="<?= BASE_URL ?>/add_review.php" class="btn btn-warning">+ Write Review</a>
    </div>

    <div class="row g-4">
        <?php foreach ($reviews as $review): ?>
            <div class="col-lg-6">
                <div class="review-card">
                    <div class="d-flex gap-3 align-items-start mb-3">
                        <img src="<?= gamePlaceholderPath($review['title']) ?>" alt="<?= e($review['title']) ?> placeholder cover" class="game-cover-sm">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="mb-0"><?= e($review['title']) ?></h4>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rating-stars"><?= ratingStars((int)$review['rating'], 5) ?></span>
                                    <span class="small text-muted-custom"><?= (int)$review['rating'] ?>/5</span>
                                </div>
                            </div>
                            <p class="text-muted-custom mb-2"><?= e($review['content']) ?></p>
                            <p class="placeholder-copy mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. This extra space can later hold more review preview text or platform-specific notes.</p>
                            <div class="action-row">
                                <a href="<?= BASE_URL ?>/edit_review.php?id=<?= (int)$review['id'] ?>" class="btn btn-outline-light btn-sm">Edit</a>
                                <form method="POST" action="<?= BASE_URL ?>/delete_review.php" onsubmit="return confirm('Delete this review?');">
                                    <input type="hidden" name="id" value="<?= (int)$review['id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm">Delete</button>
                                </form>
                                <a href="<?= BASE_URL ?>/game.php?id=<?= (int)$review['game_id'] ?>" class="btn btn-outline-warning btn-sm">See More</a>
                            </div>
                        </div>
                    </div>
                    <div class="small text-muted-custom"><?= e(date('M d, Y', strtotime($review['created_at']))) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
