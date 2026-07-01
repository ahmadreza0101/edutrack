<?php
$rootPath = __DIR__;

include $rootPath . '/koneksi.php';
/** @var mysqli */
require_once $rootPath . '/app/config/session.php';

include 'partials/index/header.php';
?>

<style>
    html,
    body {
        height: auto;
        min-height: 100%;
    }

    .login-hero-bg {
        position: relative;
        background-image: 
            linear-gradient(rgba(8, 17, 32, 0.45), rgba(8, 17, 32, 0.6)),
            url('/paralax.webp');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        padding: 80px 0 40px 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    [data-bs-theme="light"] .login-hero-bg {
        background-image: 
            linear-gradient(rgba(255, 255, 255, 0.3), rgba(248, 250, 252, 0.4)),
            url('/paralax.webp');
    }

    .login-hero-bg h2,
    .login-hero-bg a.text-light {
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    @media (max-width: 768px) {
        .login-hero-bg {
            padding: 60px 15px 30px 15px;
        }
        
        .col-lg-5.col-md-7 {
            max-width: 100%;
            flex: 0 0 100%;
        }
    }
</style>



<main class="d-flex flex-column login-hero-bg fullscreen">

    <section id="login-admin" class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    
                    <h2 class="text-center mb-4 fw-bold">
                        Masuk CMS Administrator
                    </h2>

                    <div class="card card-feature rounded-4">
                        <div class="card-body p-4 p-md-5">
                            
                            <form action="/app/proses/login/cmslogin-proses.php" method="POST" id="loginForm">
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Email / Username</label>
                                    <input type="text" class="form-control" 
                                           id="username" name="username" placeholder="Masukkan email atau username" required>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" 
                                           id="password" name="password" placeholder="Masukkan password" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3 py-2 fw-bold">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </button>

                            </form>

                            <div class="text-center mt-4">
                                <a href="/index.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                                </a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'partials/index/footer.php'; ?>