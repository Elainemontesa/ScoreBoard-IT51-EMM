<?php
$pageTitle = 'My Diary';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$stmt = $pdo->prepare('SELECT d.*, g.title, g.genre, g.developer
FROM diary_entries d
JOIN games g ON g.id = d.game_id
WHERE d.user_id = ?
ORDER BY d.last_played_at DESC');
$stmt->execute([$user['id']]);
$entries = $stmt->fetchAll();
?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
        <div>
            <h1 class="fw-bold mb-1">My Diary</h1>
            <p class="text-muted-custom mb-0">Your played games, playtime, and achievement notes.</p>
        </div>
        <a href="<?= BASE_URL ?>/add_diary.php" class="btn btn-warning">+ Add Diary Entry</a>
    </div>

    <div class="row g-4">
        <?php foreach ($entries as $entry): ?>
            <div class="col-lg-6">
                <div class="game-card">
                    <div class="d-flex gap-3 align-items-start mb-2">
                        <img src="<?= gamePlaceholderPath($entry['title']) ?>" alt="<?= e($entry['title']) ?> placeholder cover" class="game-cover-sm">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-2 flex-wrap">
                                <div>
                                    <h4 class="mb-1"><?= e($entry['title']) ?></h4>
                                    <div class="small text-muted-custom">
                                        <?php
                                        $cats = array_filter(array_map('trim', explode(',', $entry['genre'])));
                                        echo e(implode(', ', $cats));
                                        ?>
                                        
                                        · <?= e($entry['developer']) ?>
                                    </div>
                                </div>
                                <span class="badge bg-<?= diaryStatusBadge($entry['status']) ?>"><?= e(ucfirst($entry['status'])) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-sm-6">
                            <div class="stat-box">
                                <div class="small text-muted-custom">Total Playtime</div>
                                <div class="fw-bold"><?= number_format((float)$entry['playtime_hours'], 1) ?> hours</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="stat-box">
                                <div class="small text-muted-custom">Last Played</div>
                                <div class="fw-bold"><?= e(date('M d, Y', strtotime($entry['last_played_at']))) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="fw-semibold mb-1">In-Game Achievement Lists</div>
                        <p class="mb-3 text-muted-custom"><?= e($entry['achievement_notes']) ?></p>
                        <div class="action-row">
                            <a href="<?= BASE_URL ?>/edit_diary.php?id=<?= (int)$entry['id'] ?>" class="btn btn-outline-light btn-sm">Edit</a>
                            <form method="POST" action="<?= BASE_URL ?>/delete_diary.php" onsubmit="return confirm('Delete this diary entry?');">
                                <input type="hidden" name="id" value="<?= (int)$entry['id'] ?>">
                                <button class="btn btn-outline-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
