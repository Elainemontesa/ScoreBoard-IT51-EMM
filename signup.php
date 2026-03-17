<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/profile.php');
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$full_name || !$username || !$password) {
        $error = 'Please fill out all fields.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = 'Username already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (display_name, username, password, bio) VALUES (?, ?, ?, ?)');
            $stmt->execute([$full_name, $username, $hashed, 'New member on ScoreBoard']);
            $success = 'Account created successfully. You can sign in now.';
        }
    }
}

$pageTitle = 'Sign Up';
require_once __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-6 p-4 p-lg-5 d-flex align-items-center bg-dark order-md-2">
                            <div>
                                <h2 class="fw-bold">Hello, Friend!</h2>
                                <p class="text-muted-custom">Already a member? Sign in and continue your diary, reviews, and game tracking.</p>
                                <a href="<?= BASE_URL ?>/signin.php" class="btn btn-outline-light">Sign in</a>
                            </div>
                        </div>
                        <div class="col-md-6 p-4 p-lg-5 order-md-1">
                            <h2 class="fw-bold mb-4">Create an Account</h2>
                            <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                            <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button class="btn btn-warning w-100">Sign Up</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
