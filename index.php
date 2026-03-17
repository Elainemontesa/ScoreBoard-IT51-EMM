<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge bg-warning text-dark mb-3">JOIN NOW!</span>
                <h1 class="display-4 fw-bold mb-3">Track your played games, showcase achievements, and share what’s good.</h1>
                <p class="lead text-muted-custom">ScoreBoard is a clean gaming journal where you can manage your diary, publish reviews, discover games, and connect with other players.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="<?= BASE_URL ?>/signup.php" class="btn btn-warning btn-lg">Create Account</a>
                    <a href="<?= BASE_URL ?>/games.php" class="btn btn-outline-light btn-lg">Browse Games</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-card p-4 p-lg-5">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="small text-muted-custom">Diary</div>
                                <div class="h3 fw-bold">Games</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box">
                                <div class="small text-muted-custom">Community</div>
                                <div class="h3 fw-bold">User Reviews</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="game-card">
                                <img src="<?= gamePlaceholderPath('God of War: Ragnarok') ?>" alt="Featured game placeholder" class="game-cover">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <div class="small text-muted-custom">Featured Profile</div>
                                        <h4 class="mb-0">Pixel Hollow</h4>
                                    </div>
                                    <span class="avatar-circle">P</span>
                                </div>
                                <div class="row text-center mt-3 g-2">
                                    <div class="col-3"><div class="stat-box"><div class="small">Completed</div><div class="fw-bold">23</div></div></div>
                                    <div class="col-3"><div class="stat-box"><div class="small">Playtime</div><div class="fw-bold">549h</div></div></div>
                                    <div class="col-3"><div class="stat-box"><div class="small">Backlogs</div><div class="fw-bold">67</div></div></div>
                                    <div class="col-3"><div class="stat-box"><div class="small">Liked</div><div class="fw-bold">30</div></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
