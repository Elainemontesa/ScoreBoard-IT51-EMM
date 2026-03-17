<?php
$pageTitle = 'Edit Diary Entry';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM diary_entries WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$id, $user['id']]);
$entry = $stmt->fetch();
if (!$entry) {
    flash('danger', 'Diary entry not found.');
    header('Location: ' . BASE_URL . '/diary.php');
    exit;
}

$games = $pdo->query('SELECT id, title FROM games ORDER BY title ASC')->fetchAll();
$error = '';
$form = [
    'game_id' => (string)$entry['game_id'],
    'status' => $entry['status'],
    'playtime_hours' => (string)$entry['playtime_hours'],
    'last_played_at' => date('Y-m-d', strtotime($entry['last_played_at'])),
    'liked' => (string)$entry['liked'],
    'achievement_notes' => $entry['achievement_notes'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['game_id'] = trim($_POST['game_id'] ?? '');
    $form['status'] = trim($_POST['status'] ?? 'playing');
    $form['playtime_hours'] = trim($_POST['playtime_hours'] ?? '');
    $form['last_played_at'] = trim($_POST['last_played_at'] ?? date('Y-m-d'));
    $form['liked'] = isset($_POST['liked']) ? '1' : '0';
    $form['achievement_notes'] = trim($_POST['achievement_notes'] ?? '');

    if (!$form['game_id'] || !in_array($form['status'], ['playing', 'completed', 'backlog'], true) || $form['playtime_hours'] === '' || !$form['last_played_at']) {
        $error = 'Please complete all required fields.';
    } else {
        // Prevent changing this diary entry to a game that already has another diary entry from this user
        $check = $pdo->prepare('SELECT id FROM diary_entries WHERE user_id = ? AND game_id = ? AND id != ? LIMIT 1');
        $check->execute([$user['id'], (int)$form['game_id'], $id]);
        if ($check->fetch()) {
            $error = 'You already have another diary entry for this game. Please edit that entry instead.';
        } else {
            $update = $pdo->prepare('UPDATE diary_entries SET game_id = ?, status = ?, playtime_hours = ?, liked = ?, last_played_at = ?, achievement_notes = ? WHERE id = ? AND user_id = ?');
            $update->execute([
                (int)$form['game_id'],
                $form['status'],
                (float)$form['playtime_hours'],
                (int)$form['liked'],
                $form['last_played_at'] . ' 00:00:00',
                $form['achievement_notes'],
                $id,
                $user['id'],
            ]);
            flash('success', 'Diary entry updated successfully.');
            header('Location: ' . BASE_URL . '/diary.php');
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
                        <h1 class="fw-bold mb-1">Edit Diary Entry</h1>
                        <p class="text-muted-custom mb-0">Update your progress, notes, and playtime.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/diary.php" class="btn btn-outline-light">Back</a>
                </div>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Game</label>
                            <select name="game_id" class="form-select" required>
                                <option value="">Select a game</option>
                                <?php foreach ($games as $game): ?>
                                    <option value="<?= (int)$game['id'] ?>" <?= $form['game_id'] == $game['id'] ? 'selected' : '' ?>><?= e($game['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="playing" <?= $form['status'] === 'playing' ? 'selected' : '' ?>>Playing</option>
                                <option value="completed" <?= $form['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="backlog" <?= $form['status'] === 'backlog' ? 'selected' : '' ?>>Backlog</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Total Playtime (hours)</label>
                            <input type="number" name="playtime_hours" class="form-control" step="0.1" min="0" value="<?= e($form['playtime_hours']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Played</label>
                            <input type="date" name="last_played_at" class="form-control" value="<?= e($form['last_played_at']) ?>" required>
                        </div>
                        <div class="col-12">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="liked" name="liked" <?= $form['liked'] === '1' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="liked">Mark this game as liked</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Achievement / Diary Notes</label>
                            <textarea name="achievement_notes" class="form-control" rows="6" required><?= e($form['achievement_notes']) ?></textarea>
                        </div>
                    </div>
                    <div class="mt-4 action-row">
                        <button class="btn btn-warning">Update Diary Entry</button>
                        <a href="<?= BASE_URL ?>/diary.php" class="btn btn-outline-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
