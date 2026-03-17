<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/profile.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: ' . BASE_URL . '/profile.php');
        exit;
    }

    $error = 'Invalid username or password.';
}

$pageTitle = 'Sign In';
require_once __DIR__ . '/includes/header.php';
?>
<div class="auth-wrap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card overflow-hidden">
                    <div class="row g-0">
                        <div class="col-md-6 p-4 p-lg-5">
                            <h2 class="fw-bold mb-4">Sign In to <span class="text-warning">ScoreBoard</span></h2>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?= e($error) ?></div>
                            <?php endif; ?>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <span class="small text-muted-custom">Forgot your password?</span>
                                    <a href="<?= BASE_URL ?>/signup.php">Sign up</a>
                                </div>
                                <button class="btn btn-warning w-100">Sign In</button>
                            </form>
                        </div>
                        <div class="col-md-6 p-4 p-lg-5 d-flex align-items-center bg-dark">
                            <div>
                                <h2 class="fw-bold">Hello, Friend!</h2>
                                <p class="text-muted-custom">Not a member yet? Create an account and start tracking your gaming journey.</p>
                                <a href="<?= BASE_URL ?>/signup.php" class="btn btn-outline-light">Create account</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
