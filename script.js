if (typeof toastr !== 'undefined') {
    toastr.options = {
        "closeButton": true,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "timeOut": 4000,
        "extendedTimeOut": 1000,
        "iconClass": "",
        "preventDuplicates": false,
        "tapToDismiss": true,
        "newestOnTop": true
    };
}

const htmlElement = document.documentElement;

function fixSidebarExpandButton(theme) {
    const expandBtn = document.querySelector('.app-sidebar-expand-btn');
    if (expandBtn) {
        if (theme === 'dark') {
            expandBtn.style.setProperty('background-color', 'rgba(8, 17, 32, 0.98)', 'important');
            expandBtn.style.setProperty('border-color', 'var(--color-border)', 'important');
            expandBtn.style.setProperty('color', 'var(--color-text-muted)', 'important');
        } else {
            expandBtn.style.setProperty('background-color', '#ffffff', 'important');
            expandBtn.style.setProperty('border-color', 'rgba(0,0,0,0.15)', 'important');
            expandBtn.style.setProperty('color', '#64748b', 'important');
        }
    }
    
    const closeBtn = document.querySelector('.app-sidebar-close-btn');
    if (closeBtn) {
        if (theme === 'dark') {
            closeBtn.style.setProperty('color', '#ffffff', 'important');
        } else {
            closeBtn.style.setProperty('color', '#212529', 'important');
        }
    }
}

function setTheme(theme, themeIconElement = null) {
    htmlElement.setAttribute('data-bs-theme', theme);
    localStorage.setItem('theme', theme);
    
    if (themeIconElement) {
        if (theme === 'dark') {
            themeIconElement.classList.remove('bi-sun');
            themeIconElement.classList.add('bi-moon-stars');
        } else {
            themeIconElement.classList.remove('bi-moon-stars');
            themeIconElement.classList.add('bi-sun');
        }
    }
    
    fixSidebarExpandButton(theme);
    setTimeout(() => fixSidebarExpandButton(theme), 100);
    setTimeout(() => fixSidebarExpandButton(theme), 500);
}

document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    if (!themeToggle || !themeIcon) {
        return; 
    }

    themeToggle.addEventListener('click', () => {
        const currentTheme = htmlElement.getAttribute('data-bs-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme, themeIcon);
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
        if (!localStorage.getItem('theme')) {
            setTheme(e.matches ? 'dark' : 'light', themeIcon);
        }
    });

    function showToast(type, title, message) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            background-color: ${colors[type]} !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15) !important;
            padding: 14px 40px 14px 16px !important;
            margin-bottom: 10px !important;
            position: relative !important;
            cursor: default !important;
            width: 340px !important;
            max-width: 100% !important;
        `;
        toast.innerHTML = `
            <div style="font-size:14px;font-weight:600;margin-bottom:3px;">${title}</div>
            <div style="font-size:13px;line-height:1.4;opacity:0.95;">${message}</div>
            <button type="button" style="
                position:absolute;
                top:12px;
                right:12px;
                background:none;
                border:none;
                color:inherit;
                font-size:18px;
                cursor:pointer;
                padding:0;
                width:20px;
                height:20px;
                display:flex;
                align-items:center;
                justify-content:center;
                opacity:0.75;
            " onclick="this.closest('.toast').remove()">×</button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    function checkUrlMessages() {
        const urlParams = new URLSearchParams(window.location.search);
        const type = urlParams.get('toast_type');
        const title = urlParams.get('toast_title');
        const message = urlParams.get('toast_message');

        if (type && title && message) {
            showToast(type, title, message);
            const cleanUrl = window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    }

    checkUrlMessages();
});