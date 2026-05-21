// Premium UI Scripting Layer

document.addEventListener('DOMContentLoaded', () => {
    // 1. Theme Management (Light / Dark Mode)
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Set initial theme
    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        updateThemeIcon(true);
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
        updateThemeIcon(false);
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme');
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                updateThemeIcon(false);
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                updateThemeIcon(true);
            }
        });
    }

    function updateThemeIcon(isDark) {
        const darkIcon = document.getElementById('theme-icon-dark');
        const lightIcon = document.getElementById('theme-icon-light');
        if (darkIcon && lightIcon) {
            if (isDark) {
                darkIcon.style.display = 'none';
                lightIcon.style.display = 'block';
            } else {
                darkIcon.style.display = 'block';
                lightIcon.style.display = 'none';
            }
        }
    }

    // 2. Tab Control System
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTab = btn.getAttribute('data-tab');
            
            // Toggle buttons
            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Toggle contents
            tabContents.forEach(content => {
                if (content.id === targetTab) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });
            
            // Preserve tab in URL query (optional helper)
            const url = new URL(window.location);
            url.searchParams.set('tab', targetTab);
            window.history.pushState({}, '', url);
        });
    });

    // 3. Simple Modal Helpers
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
        }
    }

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    }

    // Close modal on clicking outside the box
    const overlays = document.querySelectorAll('.modal-overlay');
    overlays.forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                overlay.classList.remove('active');
            }
        });
    });

    // 4. Alert Banner Auto-dismiss/Close
    const alertCloseBtns = document.querySelectorAll('.alert-close');
    alertCloseBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const banner = btn.closest('.alert-banner');
            if (banner) {
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    banner.remove();
                }, 300);
            }
        });
    });
});
