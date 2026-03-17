<?php
$pageTitle = 'Games';
require_once __DIR__ . '/includes/header.php';

$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

$sql = 'SELECT g.*, COALESCE(l.like_count, 0) AS like_count,
               COALESCE(r.avg_rating, 0) AS avg_rating,
               COALESCE(r.review_count, 0) AS review_count
FROM games g
LEFT JOIN (
    SELECT game_id, COUNT(*) AS like_count
    FROM diary_entries
    WHERE liked = 1
    GROUP BY game_id
) l ON l.game_id = g.id
LEFT JOIN (
    SELECT game_id, AVG(rating) AS avg_rating, COUNT(*) AS review_count
    FROM reviews
    GROUP BY game_id
) r ON r.game_id = g.id
WHERE 1=1';
$params = [];
if ($q !== '') {
    $sql .= ' AND g.title LIKE ?';
    $params[] = "%$q%";
}
if ($category !== '') {
    $sql .= ' AND FIND_IN_SET(?, g.genre)';
    $params[] = $category;
}
$sql .= ' ORDER BY g.title ASC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll();

$genres = ['Action', 'RPG', 'Co-op', 'FPS', 'Horror', 'Survival', 'Strategy', 'Simulator', 'VR', 'Platformer'];
?>
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="glass-card p-4">
                <h4 class="fw-bold">Category</h4>
                <div class="mt-3">
                    <?php foreach ($genres as $g): ?>
                        <a class="category-pill" href="<?= BASE_URL ?>/games.php?category=<?= urlencode($g) ?>"><?= e($g) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div>
                    <h1 class="fw-bold mb-1">Games</h1>
                    <p class="text-muted-custom mb-0">Browse titles, categories, and developer details.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <form class="d-flex gap-2" method="GET">
                        <input type="text" name="q" class="form-control" placeholder="Search games..." value="<?= e($q) ?>">
                        <?php if ($category): ?><input type="hidden" name="category" value="<?= e($category) ?>"><?php endif; ?>
                        <button class="btn btn-warning">Search</button>
                    </form>
                    <?php if ($user): ?>
                        <a href="<?= BASE_URL ?>/add_game.php" class="btn btn-outline-light">+ Add Game</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($q): ?>
                <div class="mb-3 text-muted-custom">Search results for: <strong class="text-white"><?= e($q) ?></strong></div>
            <?php endif; ?>

            <div class="row g-4">
                <?php foreach ($games as $game): ?>
                    <div class="col-md-6">
                        <div class="game-card h-100">
                            <img src="<?= gamePlaceholderPath($game['title']) ?>" alt="<?= e($game['title']) ?> placeholder cover" class="game-cover">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h4 class="mb-0"><?= e($game['title']) ?></h4>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach (explode(',', $game['genre']) as $g): ?>
                                        <?php $g = trim($g); if ($g === '') continue; ?>
                                        <span class="badge text-bg-warning text-dark"><?= e($g) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="small text-muted-custom mb-2">Developer: <?= e($game['developer']) ?></div>
                            <div class="small text-muted-custom mb-1"><?= (int)$game['like_count'] ?> like<?= ((int)$game['like_count']) === 1 ? '' : 's' ?></div>
                            <div class="small text-muted-custom mb-2">
                                <?php $reviewCount = (int)$game['review_count']; ?>
                                <?php if ($reviewCount > 0): ?>
                                    <span class="rating-stars"><?= ratingStars((int)round((float)$game['avg_rating']), 5) ?></span>
                                    <span class="ms-1"><?= number_format((float)$game['avg_rating'], 1) ?>/5 · <?= $reviewCount ?> review<?= $reviewCount === 1 ? '' : 's' ?></span>
                                <?php else: ?>
                                    No ratings yet
                                <?php endif; ?>
                            </div>
                            <p class="mb-2 text-muted-custom"><?= e($game['description']) ?></p>
                            <p class="placeholder-copy mb-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit. This placeholder text helps visualize a richer card layout while final screenshots or cover art are still unavailable.</p>
                            <div class="action-row">
                                <a class="btn btn-outline-light btn-sm" href="<?= BASE_URL ?>/game.php?id=<?= (int)$game['id'] ?>">View Details</a>
                                <?php if ($user): ?>
                                    <a class="btn btn-outline-warning btn-sm" href="<?= BASE_URL ?>/add_review.php">Write Review</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
