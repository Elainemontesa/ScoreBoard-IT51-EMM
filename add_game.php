<?php
$pageTitle = 'Add Game';
require_once __DIR__ . '/includes/header.php';
requireLogin();

$genres = ['Action', 'RPG', 'Co-op', 'FPS', 'Horror', 'Survival', 'Strategy', 'Simulator', 'VR', 'Platformer'];

$error = '';
$form = [
    'title' => '',
    'genres' => [],
    'developer' => '',
    'description' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['title'] = trim($_POST['title'] ?? '');
    $form['genres'] = array_filter(array_map('trim', $_POST['genres'] ?? []));
    $form['developer'] = trim($_POST['developer'] ?? '');
    $form['description'] = trim($_POST['description'] ?? '');

    if ($form['title'] === '' || empty($form['genres']) || $form['developer'] === '' || $form['description'] === '') {
        $error = 'Please complete all required fields.';
    } elseif (array_diff($form['genres'], $genres)) {
        $error = 'Please choose at least one valid category.';
    } else {
        $genreValue = implode(',', $form['genres']);
        $stmt = $pdo->prepare('INSERT INTO games (title, genre, developer, description) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $form['title'],
            $genreValue,
            $form['developer'],
            $form['description'],
        ]);
        flash('success', 'Game added successfully.');
        header('Location: ' . BASE_URL . '/games.php');
        exit;
    }
}
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="glass-card p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
                    <div>
                        <h1 class="fw-bold mb-1">Add Game</h1>
                        <p class="text-muted-custom mb-0">Create a new game entry and choose its category.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/games.php" class="btn btn-outline-light">Back</a>
                </div>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="<?= e($form['title']) ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Categories</label>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($genres as $g): ?>
                                    <div class="form-check me-2">
                                        <input class="form-check-input" type="checkbox" id="genre_<?= e($g) ?>" name="genres[]" value="<?= e($g) ?>" <?= in_array($g, $form['genres'], true) ? 'checked' : '' ?>>
                                        <label class="form-check-label small" for="genre_<?= e($g) ?>"><?= e($g) ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Developer</label>
                            <input type="text" name="developer" class="form-control" value="<?= e($form['developer']) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="6" required><?= e($form['description']) ?></textarea>
                        </div>
                    </div>
                    <div class="mt-4 action-row">
                        <button class="btn btn-warning">Save Game</button>
                        <a href="<?= BASE_URL ?>/games.php" class="btn btn-outline-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
