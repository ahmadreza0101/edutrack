<?php
$currentPage = basename($_SERVER['PHP_SELF']);

$pageModules = [
    'dashboard.php' => 'dashboard',
    'jadwal.php'    => 'jadwal',
    'edit.php'      => 'jadwal',
    'user.php'      => 'user',
];

$moduleTitles = [
    'dashboard' => 'Dashboard',
    'jadwal'    => 'Kelola Jadwal',
    'user'      => 'Kelola Akses',
];

$activeMenu = $pageModules[$currentPage] ?? 'dashboard';
$pageTitle  = $moduleTitles[$activeMenu] ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id" id="html-theme">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - EduTrack</title>

 <link rel="stylesheet" href="/style/cms-header.css">
    <link rel="stylesheet" href="/assets/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/toastr.min.css">
    <link rel="stylesheet" href="/style/toastr.css">
    <link rel="stylesheet" href="/assets/datatables.min.css">
    <link rel="stylesheet" href="/font.css">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/assets/bootstrap-icons/font/bootstrap-icons.css">

    <script src="/assets/jquery-3.6.1.js" defer></script>
    <script src="/assets/datatables.min.js" defer></script>
    <script src="/assets/bootstrap/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="/assets/toastr.min.js" defer></script>
    <script src="/script.js" defer></script>

    <link rel="apple-touch-icon" sizes="180x180" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png">
    <link rel="icon" type="image/png" sizes="32x32" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png">
    <link rel="icon" type="image/png" sizes="16x16" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png">
    <link rel="shortcut icon" href="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" type="image/x-icon">

    <link rel="preload" href="/font/Open_Sans/OpenSans-Regular.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/font/Open_Sans/OpenSans-Bold.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="/assets/bootstrap-icons/font/fonts/bootstrap-icons.woff2" as="font" type="font/woff2" crossorigin>

    <script>
        (function () {
            try {
                var collapsed = localStorage.getItem('sidebarCollapsed') === '1';
                if (collapsed) {
                    document.documentElement.classList.add('sidebar-collapsed-init');
                }
            } catch (e) {}
        })();
    </script>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const preferredTheme = savedTheme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-bs-theme', preferredTheme);
            window.preferredTheme = preferredTheme;
        })();
    </script>
</head>

<body class="has-sidebar">
<noscript><style>body { opacity: 1 !important; }</style></noscript>

<div class="toast-container" id="toast-container"></div>

<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid d-flex align-items-center">
        <button class="navbar-toggler d-lg-none" type="button" id="sidebarToggle" style="border: none;" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <span class="navbar-page-title"><?php echo htmlspecialchars($pageTitle); ?></span>
        
        <button class="theme-toggle ms-auto" id="themeToggle" title="Ganti Tema">
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
    </div>
</nav>

<div class="page-wrapper">

