<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="app-sidebar" id="appSidebar">

    <div class="app-sidebar-header">
        <a href="/dashboard.php" class="app-sidebar-brand">
            <img src="https://cdn-icons-png.flaticon.com/512/2232/2232688.png" alt="Logo Pendidikan" class="app-sidebar-brand-icon" width="20" height="20">
            <span class="app-sidebar-brand-text">EduTrack</span>
        </a>

        <button class="app-sidebar-collapse-btn" id="sidebarCollapseToggle" title="Collapse sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>

        <button class="app-sidebar-close-btn d-lg-none" id="sidebarClose" aria-label="Tutup menu">
            &times;
        </button>
    </div>

    <ul class="app-sidebar-nav">
        <li>
            <a href="/dashboard.php" class="<?php echo isset($activeMenu) && $activeMenu === 'dashboard' ? 'active' : ''; ?>" data-label="Dashboard">
                <i class="bi bi-speedometer2"></i>
                <span class="app-sidebar-link-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="/jadwal.php" class="<?php echo isset($activeMenu) && $activeMenu === 'jadwal' ? 'active' : ''; ?>" data-label="Kelola Jadwal">
                <i class="bi bi-calendar-check"></i>
                <span class="app-sidebar-link-text">Kelola Jadwal</span>
            </a>
        </li>
        <li>
            <a href="/user.php" class="<?php echo isset($activeMenu) && $activeMenu === 'user' ? 'active' : ''; ?>" data-label="Kelola Akses">
                <i class="bi bi-people"></i>
                <span class="app-sidebar-link-text">Kelola Akses</span>
            </a>
        </li>
    </ul>

    <div class="app-sidebar-footer">
        <a href="/app/proses/login/logout.php" class="app-sidebar-logout" data-label="Logout">
            <i class="bi bi-box-arrow-right"></i>
            <span class="app-sidebar-link-text">Logout</span>
        </a>
    </div>

    <button class="app-sidebar-expand-btn" id="sidebarExpandToggle" title="Expand sidebar">
        <i class="bi bi-chevron-right"></i>
    </button>

</aside>

