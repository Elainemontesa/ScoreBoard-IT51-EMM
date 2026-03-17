<?php
$pageTitle = 'Edit Review';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM reviews WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$id, $user['id']]);
$review = $stmt->fetch();
if (!$review) {
    flash('danger', 'Review not found.');
    header('Location: ' . BASE_URL . '/reviews.php');
    exit;
}

$games = $pdo->query('SELECT id, title FROM games ORDER BY title ASC')->fetchAll();
$error = '';
$form = [
    'game_id' => (string)$review['game_id'],
    'rating' => (string)$review['rating'],
    'content' => $review['content'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['game_id'] = trim($_POST['game_id'] ?? '');
    $form['rating'] = trim($_POST['rating'] ?? '');
    $form['content'] = trim($_POST['content'] ?? '');

    if (!$form['game_id'] || !$form['rating'] || !$form['content']) {
        $error = 'Please complete all required fields.';
    } elseif ((int)$form['rating'] < 1 || (int)$form['rating'] > 5) {
        $error = 'Rating must be from 1 to 5.';
    } else {
        // Prevent changing this review to a game that already has a review from this user
        $check = $pdo->prepare('SELECT id FROM reviews WHERE user_id = ? AND game_id = ? AND id != ? LIMIT 1');
        $check->execute([$user['id'], (int)$form['game_id'], $id]);
        if ($check->fetch()) {
            $error = 'You already have another review for this game. Please edit that review instead.';
        } else {
            $update = $pdo->prepare('UPDATE reviews SET game_id = ?, rating = ?, content = ? WHERE id = ? AND user_id = ?');
            $update->execute([(int)$form['game_id'], (int)$form['rating'], $form['content'], $id, $user['id']]);
            flash('success', 'Review updated successfully.');
            header('Location: ' . BASE_URL . '/reviews.php');
            exit;
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
                        <h1 class="fw-bold mb-1">Edit Review</h1>
                        <p class="text-muted-custom mb-0">Refine your score and written thoughts.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/reviews.php" class="btn btn-outline-light">Back</a>
                </div>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Game</label>
                            <select name="game_id" class="form-select" required>
                                <option value="">Select a game</option>
                                <?php foreach ($games as $game): ?>
                                    <option value="<?= (int)$game['id'] ?>" <?= $form['game_id'] == $game['id'] ? 'selected' : '' ?>><?= e($game['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rating (1-5)</label>
                            <input type="number" name="rating" class="form-control" min="1" max="5" value="<?= e($form['rating']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Review</label>
                            <textarea name="content" class="form-control" rows="7" required><?= e($form['content']) ?></textarea>
                        </div>
                    </div>
                    <div class="mt-4 action-row">
                        <button class="btn btn-warning">Update Review</button>
                        <a href="<?= BASE_URL ?>/reviews.php" class="btn btn-outline-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
