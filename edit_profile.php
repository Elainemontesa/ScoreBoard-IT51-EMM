<?php
$pageTitle = 'Edit Profile';
require_once __DIR__ . '/includes/header.php';
requireLogin();
$user = currentUser($pdo);

$error = '';
$success = '';

$form = [
    'display_name' => $user['display_name'] ?? '',
    'bio' => $user['bio'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['display_name'] = trim($_POST['display_name'] ?? '');
    $form['bio'] = trim($_POST['bio'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($form['display_name'] === '') {
        $error = 'Display name is required.';
    } else {
        // Validate password change only if a new password was entered
        if ($new_password !== '' || $confirm_password !== '' || $current_password !== '') {
            if ($new_password === '' || $confirm_password === '' || $current_password === '') {
                $error = 'To change your password, fill in all password fields.';
            } elseif (!password_verify($current_password, $user['password'])) {
                $error = 'Current password is incorrect.';
            } elseif ($new_password !== $confirm_password) {
                $error = 'New password and confirmation do not match.';
            }
        }

        if ($error === '') {
            // Update basic profile fields
            $updateSql = 'UPDATE users SET display_name = ?, bio = ?';
            $params = [$form['display_name'], $form['bio']];

            // Optionally update password
            if ($new_password !== '' && $new_password === $confirm_password && password_verify($current_password, $user['password'])) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $updateSql .= ', password = ?';
                $params[] = $hashed;
            }

            $updateSql .= ' WHERE id = ?';
            $params[] = $user['id'];

            $stmt = $pdo->prepare($updateSql);
            $stmt->execute($params);

            // Refresh user data in memory
            $user = currentUser($pdo);
            $success = 'Profile updated successfully.';
            flash('success', $success);
            header('Location: ' . BASE_URL . '/profile.php');
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
                        <h1 class="fw-bold mb-1">Edit Profile</h1>
                        <p class="text-muted-custom mb-0">Update your name, bio, and password.</p>
                    </div>
                    <a href="<?= BASE_URL ?>/profile.php" class="btn btn-outline-light">Back to Profile</a>
                </div>
                <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Display Name</label>
                        <input type="text" name="display_name" class="form-control" value="<?= e($form['display_name']) ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="Tell others about your gaming style..."><?= e($form['bio']) ?></textarea>
                    </div>

                    <h5 class="fw-bold mb-3">Change Password</h5>
                    <p class="small text-muted-custom">Leave these fields empty if you don't want to change your password.</p>
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>

                    <div class="action-row mt-3">
                        <button class="btn btn-warning">Save Changes</button>
                        <a href="<?= BASE_URL ?>/profile.php" class="btn btn-outline-light">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
