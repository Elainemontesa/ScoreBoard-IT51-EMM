<?php
$pageTitle = 'Write Review';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$games = $pdo->query('SELECT id, title FROM games ORDER BY title ASC')->fetchAll();
$error = '';
$form = [
    'game_search' => '',
    'rating' => '',
    'content' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['game_search'] = trim($_POST['game_search'] ?? '');
    $form['rating'] = trim($_POST['rating'] ?? '');
    $form['content'] = trim($_POST['content'] ?? '');

    if (!$form['game_search'] || !$form['rating'] || !$form['content']) {
        $error = 'Please complete all required fields.';
    } elseif ((int)$form['rating'] < 1 || (int)$form['rating'] > 5) {
        $error = 'Rating must be from 1 to 5.';
    } else {
        // Resolve game by exact title from search field
        $gameLookup = $pdo->prepare('SELECT id FROM games WHERE title = ? LIMIT 1');
        $gameLookup->execute([$form['game_search']]);
        $gameRow = $gameLookup->fetch();

        if (!$gameRow) {
            $error = 'Please choose a game from the suggestions.';
        } else {
            $gameId = (int)$gameRow['id'];
            // Prevent duplicate reviews for the same game by the same user
            $existing = $pdo->prepare('SELECT id FROM reviews WHERE user_id = ? AND game_id = ? LIMIT 1');
            $existing->execute([$user['id'], $gameId]);
            if ($existing->fetch()) {
                $error = 'You already wrote a review for this game. You can edit it from your reviews page.';
            } else {
		        $stmt = $pdo->prepare('INSERT INTO reviews (user_id, game_id, rating, content) VALUES (?, ?, ?, ?)');
                $stmt->execute([$user['id'], $gameId, (int)$form['rating'], $form['content']]);
                flash('success', 'Review posted successfully.');
                header('Location: ' . BASE_URL . '/reviews.php');
                exit;
            }
        }
    }
}
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="glass-card p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
                    <div>
                        <h1 class="fw-bold mb-1">Write Review</h1>
                        <p class="text-muted-custom mb-0">Share your thoughts, score, and recommendation.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/reviews.php" class="btn btn-outline-light">Back</a>
                </div>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Game</label>
                            <input type="text" name="game_search" list="game-search-list" class="form-control" placeholder="Start typing a game title..." value="<?= e($form['game_search']) ?>" required>
                            <datalist id="game-search-list">
                                <?php foreach ($games as $game): ?>
                                    <option value="<?= e($game['title']) ?>"></option>
                                <?php endforeach; ?>
                            </datalist>
                            <div class="form-text text-muted-custom">Begin typing and choose a game from the suggestions.</div>
                        </div>
                        <div class="col-md-4">
                               <label class="form-label">Rating (1-5)</label>
                               <input type="number" name="rating" class="form-control" min="1" max="5" value="<?= e($form['rating']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Review</label>
                            <textarea name="content" class="form-control" rows="7" placeholder="Write what you liked, disliked, and whether you recommend this game..." required><?= e($form['content']) ?></textarea>
                        </div>
                    </div>
                    <div class="mt-4 action-row">
                        <button class="btn btn-warning">Post Review</button>
                        <a href="<?= BASE_URL ?>/reviews.php" class="btn btn-outline-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
