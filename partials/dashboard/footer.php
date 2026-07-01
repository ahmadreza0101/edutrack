</div> 

<footer class="text-secondary py-4">
    <div class="container-fluid text-center">
        <p class="mb-0">&copy; <span id="current-year"><?php echo date('Y'); ?></span> EduTrack. All rights reserved.</p>
    </div>
</footer>
<script>
    document.getElementById('current-year').textContent = new Date().getFullYear();
</script>

<script>
    function revealBody() {
        document.body.classList.add('loaded');
    }

    if (document.fonts && document.fonts.ready) {
        var fontsTimeout = setTimeout(revealBody, 800);
        document.fonts.ready.then(function () {
            clearTimeout(fontsTimeout);
            revealBody();
        });

    } else {
        revealBody();
    }

    var appSidebar      = document.getElementById('appSidebar');
    var sidebarOverlay  = document.getElementById('sidebarOverlay');
    var sidebarToggle   = document.getElementById('sidebarToggle');         
    var sidebarClose    = document.getElementById('sidebarClose');         
    var collapseToggle  = document.getElementById('sidebarCollapseToggle'); 
    var expandToggle    = document.getElementById('sidebarExpandToggle');  

    var STORAGE_KEY = 'sidebarCollapsed';

    function openMobileSidebar() {
        appSidebar.classList.add('mobile-active');
        sidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        appSidebar.classList.remove('mobile-active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (sidebarToggle) sidebarToggle.addEventListener('click', openMobileSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeMobileSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeMobileSidebar);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeMobileSidebar();
    });

    function setCollapsed(collapsed) {
        if (collapsed) {
            appSidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        } else {
            appSidebar.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');
        }
        try {
            localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
        } catch (e) {}
    }

    (function initSidebarState() {
        var collapsed = false;
        try {
            collapsed = localStorage.getItem(STORAGE_KEY) === '1';
        } catch (e) {}

        if (window.innerWidth >= 992 && collapsed) {
            appSidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }

        document.documentElement.classList.remove('sidebar-collapsed-init');
    })();

    if (collapseToggle) {
        collapseToggle.addEventListener('click', function () {
            setCollapsed(true);
        });
    }

    if (expandToggle) {
        expandToggle.addEventListener('click', function () {
            setCollapsed(false);
        });
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            closeMobileSidebar();
        }
    });
</script>

</body>
</html>