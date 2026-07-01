<?php
?>
<!DOCTYPE html>
<html lang="id" id="html-theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduTrack - Jadwal Ujian</title>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const preferredTheme = savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', preferredTheme);
            window.preferredTheme = preferredTheme;
        })();
    </script>

    <link rel="stylesheet" href="/style/navigasi.css">
    <link rel="stylesheet" href="/assets/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/toastr.min.css">
    <link rel="stylesheet" href="/style/toastr.css">
    <link rel="stylesheet" href="/assets/datatables.min.css">
    <link rel="stylesheet" href="/font.css">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/assets/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/style/hero-index.css">

    <script src="/assets/jquery-3.6.1.js" defer></script>
    <script src="/assets/datatables.min.js" defer></script>
    <script src="/assets/bootstrap/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="/assets/toastr.min.js" defer></script>
    <script src="/script.js" defer></script>

    <link rel="preload" href="/font/Open_Sans/OpenSans-Regular.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/font/Open_Sans/OpenSans-Bold.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/assets/bootstrap-icons/font/fonts/bootstrap-icons.woff2" as="font" type="font/woff2" crossorigin>

    <link rel="preload" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" as="image">

    <link rel="apple-touch-icon" sizes="180x180" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" type="image/x-icon">
</head>

<body>

<div class="toast-container" id="toast-container"></div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="sidebar">
    <button class="sidebar-close" id="sidebarClose">&times;</button>

    <a href="/index.php" class="sidebar-brand">
        <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" alt="Logo Pendidikan" width="36" height="36">
        EduTrack
    </a>

    <ul class="sidebar-nav">
        <li><a href="/index.php">Beranda</a></li>
        <li><a href="/daftar.php">Daftar Jadwal</a></li>
        <li><a href="/tentang.php">Tentang</a></li>
    </ul>

    <div class="sidebar-buttons">
        <a href="/login.php" class="btn btn-outline-secondary">Login CMS</a>
    </div>
</aside>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="/index.php">
            <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png"
                 alt="Logo Pendidikan"
                 width="36"
                 height="36"
                 loading="eager">
            EduTrack
        </a>

        <div class="collapse navbar-collapse d-none d-lg-block">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="/daftar.php">Daftar Jadwal</a></li>
                <li class="nav-item"><a class="nav-link" href="/tentang.php">Tentang</a></li>
            </ul>
        </div>

        <div class="navbar-actions d-flex align-items-center gap-2 ms-auto">
            <button class="theme-toggle" id="themeToggle" title="Ganti Tema" style="z-index: 1000;">
                <i class="bi" id="themeIcon"></i>
            </button>
            <script>
                (function() {
                    const themeIcon = document.getElementById('themeIcon');
                    if (themeIcon && window.preferredTheme) {
                        if (window.preferredTheme === 'dark') {
                            themeIcon.classList.add('bi-moon-stars');
                        } else {
                            themeIcon.classList.add('bi-sun');
                        }
                    }
                })();
            </script>
            <button class="navbar-toggler d-lg-none" type="button" id="sidebarToggle" style="border: none; padding: 0.5rem;" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="/login.php" class="btn btn-outline-secondary px-3 d-none d-md-block">Login CMS</a>
        </div>
    </div>
</nav>

<style>
    @media (max-width: 991px) {
        .navbar-brand {
            font-size: 1rem;
        }
        
        .navbar-brand img {
            width: 28px;
            height: 28px;
        }
        
        .navbar-actions {
            gap: 0.5rem !important;
        }
        
        .theme-toggle {
            padding: 0.5rem !important;
        }
    }
    
    @media (max-width: 768px) {
        .navbar {
            padding: 0.5rem 1rem;
        }
        
        .navbar-brand {
            font-size: 0.95rem;
        }
        
        .navbar-brand img {
            width: 26px;
            height: 26px;
        }
        
        .theme-toggle {
            padding: 0.4rem !important;
        }
        
        .theme-toggle i {
            font-size: 1.1rem !important;
        }
        
        .navbar-actions {
            gap: 0.25rem !important;
        }
    }
    
    @media (max-width: 576px) {
        .container {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        
        .navbar-brand {
            font-size: 0.85rem;
        }
        
        .navbar-brand img {
            width: 22px;
            height: 22px;
        }
        
        .theme-toggle {
            padding: 0.35rem !important;
        }
        
        .theme-toggle i {
            font-size: 1rem !important;
        }
        
        .navbar-actions {
            gap: 0.15rem !important;
        }
    }
</style>

<script>
    document.body.classList.add('loaded');

    const sidebar        = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebarClose   = document.getElementById('sidebarClose');

    function syncClosePosition() {
        const rect = sidebarToggle.getBoundingClientRect();
        sidebarClose.style.top   = (rect.top + rect.height / 2) + 'px';
        sidebarClose.style.right = (window.innerWidth - rect.right) + 'px';
    }

    function openSidebar()  {
        syncClosePosition();
        sidebar.classList.add('active');
        sidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    sidebarToggle.addEventListener('click', openSidebar);
    sidebarClose.addEventListener('click',  closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);

    window.addEventListener('resize', () => {
        if (sidebar.classList.contains('active')) syncClosePosition();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
    });
</script>