<?php
function gamePlaceholderPath(string $title): string {
    $map = [
        'Red Dead Redemption 2' => 'rdr2.svg',
        'God of War: Ragnarok' => 'gowr.svg',
        'Ghost of Tsushima' => 'tsushima.svg',
        'Apex Legends' => 'apex.svg',
        'Devil May Cry 5' => 'dmc5.svg',
        'Overwatch 2' => 'ow2.svg',
        'Fallout: New Vegas' => 'fnv.svg',
        'Fallout 4' => 'fallout4.svg',
    ];

    $file = $map[$title] ?? 'default.svg';
    return BASE_URL . '/assets/img/placeholders/' . $file;
}

function diaryStatusBadge(string $status): string {
    return match($status) {
        'completed' => 'success',
        'backlog' => 'secondary',
        default => 'warning text-dark'
    };
}

function gameLikesCount(PDO $pdo, int $gameId): int {
    $stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM diary_entries WHERE game_id = ? AND liked = 1');
    $stmt->execute([$gameId]);
    $row = $stmt->fetch();
    return (int)($row['cnt'] ?? 0);
}

function userHasLikedGame(PDO $pdo, int $userId, int $gameId): bool {
    $stmt = $pdo->prepare('SELECT liked FROM diary_entries WHERE user_id = ? AND game_id = ? LIMIT 1');
    $stmt->execute([$userId, $gameId]);
    $row = $stmt->fetch();
    return !empty($row) && (int)$row['liked'] === 1;
}

function ratingStars(int $rating, int $max = 5): string {
    $rating = max(0, min($rating, $max));
    $html = '';
    for ($i = 1; $i <= $max; $i++) {
        if ($i <= $rating) {
            $html .= '<span class="star-filled">★</span>';
        } else {
            $html .= '<span class="star-empty">☆</span>';
        }
    }
    return $html;
}

function gameAverageRating(PDO $pdo, int $gameId): array {
    $stmt = $pdo->prepare('SELECT AVG(rating) AS avg_rating, COUNT(*) AS review_count FROM reviews WHERE game_id = ?');
    $stmt->execute([$gameId]);
    $row = $stmt->fetch();

    if (!$row || (int)($row['review_count'] ?? 0) === 0) {
        return ['avg' => null, 'count' => 0];
    }

    return [
        'avg' => (float)$row['avg_rating'],
        'count' => (int)$row['review_count'],
    ];
}
?>
