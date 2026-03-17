<?php
$pageTitle = 'Game Details';
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM games WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$game = $stmt->fetch();

if (!$game) {
    http_response_code(404);
    echo '<div class="container py-5"><div class="alert alert-danger">Game not found.</div></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$reviewsStmt = $pdo->prepare('SELECT r.*, u.display_name FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.game_id = ? ORDER BY r.created_at DESC');
$reviewsStmt->execute([$id]);
$reviews = $reviewsStmt->fetchAll();

$likesCount = gameLikesCount($pdo, $id);
$userHasLiked = $user ? userHasLikedGame($pdo, $user['id'], $id) : false;
$ratingStats = gameAverageRating($pdo, $id);
$avgRating = $ratingStats['avg'];
$reviewCount = $ratingStats['count'];
?>
<div class="container py-5">
    <div class="glass-card p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <img src="<?= gamePlaceholderPath($game['title']) ?>" alt="<?= e($game['title']) ?> placeholder cover" class="game-cover">
            </div>
            <div class="col-lg-5">
                <div class="d-flex justify-content-between flex-wrap gap-3 align-items-start mb-3">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex flex-wrap gap-1 mb-1">
                            <?php foreach (explode(',', $game['genre']) as $g): ?>
                                <?php $g = trim($g); if ($g === '') continue; ?>
                                <span class="badge text-bg-warning text-dark"><?= e($g) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <span class="badge bg-light text-dark"><?= (int)$likesCount ?> like<?= $likesCount === 1 ? '' : 's' ?></span>
                    </div>
                    <a href="<?= BASE_URL ?>/games.php" class="btn btn-outline-light btn-sm">Back to Games</a>
                </div>
                <h1 class="fw-bold mb-2"><?= e($game['title']) ?></h1>
                <div class="text-muted-custom mb-1">Developer: <?= e($game['developer']) ?></div>
                <div class="text-muted-custom mb-3">Categories:
                    <?php
                    $cats = array_filter(array_map('trim', explode(',', $game['genre'])));
                    echo e(implode(', ', $cats));
                    ?>
                </div>
                <p class="mb-3 text-muted-custom"><?= e($game['description']) ?></p>
                <p class="placeholder-copy mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Use this area for a future story summary, gameplay notes, or official game details.</p>
                <?php if ($user): ?>
                    <div class="action-row">
                        <a href="<?= BASE_URL ?>/add_review.php" class="btn btn-warning btn-sm">Write Review</a>
                        <a href="<?= BASE_URL ?>/add_diary.php" class="btn btn-outline-light btn-sm">Add to Diary</a>
                        <form method="POST" action="<?= BASE_URL ?>/like_game.php" class="d-inline">
                            <input type="hidden" name="game_id" value="<?= (int)$game['id'] ?>">
                            <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI'] ?? (BASE_URL . '/game.php?id=' . (int)$game['id'])) ?>">
                            <button class="btn btn-outline-warning btn-sm" type="submit">
                                <?= $userHasLiked ? 'Unlike' : 'Like' ?>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($reviewCount > 0): ?>
        <div class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <span class="rating-stars"><?= ratingStars((int)round($avgRating), 5) ?></span>
            <span class="small text-muted-custom"><?= number_format($avgRating, 1) ?>/5 · <?= $reviewCount ?> review<?= $reviewCount === 1 ? '' : 's' ?></span>
        </div>
    <?php else: ?>
        <div class="mb-3 small text-muted-custom">No reviews yet. Be the first to rate this game.</div>
    <?php endif; ?>

    <h3 class="fw-bold mb-3">User Reviews</h3>
    <div class="row g-4">
        <?php foreach ($reviews as $review): ?>
            <div class="col-lg-6">
                <div class="review-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold"><?= e($review['display_name']) ?></div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="rating-stars"><?= ratingStars((int)$review['rating'], 5) ?></span>
                            <span class="small text-muted-custom"><?= (int)$review['rating'] ?>/5</span>
                        </div>
                    </div>
                    <p class="text-muted-custom mb-0"><?= e($review['content']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
