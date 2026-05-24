// Premium UI Scripting Layer (Tailwind Adapted)

document.addEventListener('DOMContentLoaded', () => {
    // 1. Theme Management (Light / Dark Mode via Tailwind class)
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    const currentTheme = localStorage.getItem('theme') || 'light';
    const htmlEl = document.documentElement;
    
    // Set initial theme
    if (currentTheme === 'dark') {
        htmlEl.classList.add('dark');
        htmlEl.classList.remove('light');
    } else {
        htmlEl.classList.add('light');
        htmlEl.classList.remove('dark');
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            if (htmlEl.classList.contains('dark')) {
                htmlEl.classList.replace('dark', 'light');
                localStorage.setItem('theme', 'light');
            } else {
                htmlEl.classList.replace('light', 'dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    }

    // 2. Tab Control System
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTab = btn.getAttribute('data-tab');
            
            // Toggle buttons
            tabButtons.forEach(b => {
                b.classList.remove('text-primary', 'border-primary', 'dark:text-primary');
                b.classList.add('text-slate-500', 'dark:text-gray-400', 'border-transparent');
            });
            btn.classList.remove('text-slate-500', 'dark:text-gray-400', 'border-transparent');
            btn.classList.add('text-primary', 'border-primary', 'dark:text-primary');
            
            // Toggle contents
            tabContents.forEach(content => {
                if (content.id === targetTab) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
            
            // Preserve tab in URL query
            const url = new URL(window.location);
            url.searchParams.set('tab', targetTab);
            window.history.pushState({}, '', url);
        });
    });

    // 3. Simple Modal Helpers
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            // small timeout to allow display:block to apply before animating opacity
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.firstElementChild?.classList.remove('scale-95');
                modal.firstElementChild?.classList.add('scale-100');
            }, 10);
        }
    }

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('opacity-0');
            modal.firstElementChild?.classList.remove('scale-100');
            modal.firstElementChild?.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }

    window.confirmAction = function(event, message, url, title = 'Peringatan Konfirmasi') {
        event.preventDefault();
        document.getElementById('confirmModalMessage').textContent = message;
        document.getElementById('confirmModalTitle').textContent = title;
        document.getElementById('confirmModalBtn').href = url;
        window.openModal('globalConfirmModal');
        return false;
    }

    // Close modal on clicking outside the box
    const overlays = document.querySelectorAll('.modal-overlay');
    overlays.forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                closeModal(overlay.id);
            }
        });
    });

    // 4. Alert Banner Auto-dismiss/Close
    // Covered inline in header.php for global messages, but handle any custom ones here:
    const alertCloseBtns = document.querySelectorAll('.alert-close-btn');
    alertCloseBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const banner = btn.closest('div[class*="badge-"]');
            if (banner) {
                banner.style.opacity = '0';
                setTimeout(() => banner.remove(), 300);
            }
        });
    });

    // 5. Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const sidebar = document.getElementById('sidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if (mobileMenuToggle && sidebar && mobileOverlay) {
        mobileMenuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('max-lg:-translate-x-full');
            mobileOverlay.classList.toggle('hidden');
            setTimeout(() => {
                mobileOverlay.classList.toggle('opacity-0');
            }, 10);
        });

        mobileOverlay.addEventListener('click', () => {
            sidebar.classList.add('max-lg:-translate-x-full');
            mobileOverlay.classList.add('opacity-0');
            setTimeout(() => {
                mobileOverlay.classList.add('hidden');
            }, 300);
        });
    }
});
