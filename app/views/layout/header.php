<?php
$urlParam = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';
$urlParts = explode('/', filter_var($urlParam, FILTER_SANITIZE_URL));
$activeController = !empty($urlParts[0]) ? $urlParts[0] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TabungFlow | Manajemen Logistik Tabung Gas</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Theme Script to Prevent Flashing -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.add('light');
        }
    </script>
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <!-- Choices.js for searchable selects -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <!-- Tailwind CSS (Play CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: { DEFAULT: '#4f46e5', hover: '#4338ca', glow: 'rgba(79, 70, 229, 0.15)' },
                        success: { DEFAULT: '#0d9488', bg: 'rgba(13, 148, 136, 0.15)' },
                        warning: { DEFAULT: '#f59e0b', bg: 'rgba(245, 158, 11, 0.15)' },
                        danger: { DEFAULT: '#ef4444', bg: 'rgba(239, 68, 68, 0.15)', glow: 'rgba(239, 68, 68, 0.2)' }
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply bg-slate-100 dark:bg-[#090d16] text-slate-900 dark:text-gray-100 min-h-screen flex transition-colors duration-300 overflow-x-hidden;
            }
            ::-webkit-scrollbar { width: 8px; height: 8px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { @apply bg-slate-400 dark:bg-gray-600 rounded-full; }
            ::-webkit-scrollbar-thumb:hover { @apply bg-slate-500 dark:bg-gray-500; }
            .overflow-x-auto {
                -webkit-overflow-scrolling: touch;
            }
        }
        @layer components {
            .glass-panel {
                @apply bg-white/70 dark:bg-gray-900/60 backdrop-blur-md border border-white/60 dark:border-white/10;
            }
            .btn {
                @apply inline-flex items-center justify-center gap-2 px-5 py-2.5 font-semibold text-sm rounded-xl transition-all no-underline cursor-pointer;
            }
            .btn-primary {
                @apply btn bg-primary text-white shadow-[0_4px_12px_rgba(79,70,229,0.15)] hover:bg-primary-hover hover:-translate-y-0.5;
            }
            .btn-secondary {
                @apply btn glass-panel text-slate-900 dark:text-gray-100 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-primary hover:-translate-y-0.5;
            }
            .btn-danger {
                @apply btn bg-danger text-white hover:bg-red-600 hover:-translate-y-0.5;
            }
            .btn-sm {
                @apply px-3.5 py-1.5 text-xs rounded-lg;
            }
            .form-label {
                @apply block font-semibold text-sm mb-2 text-slate-600 dark:text-gray-400;
            }
            .form-control {
                @apply w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-900 dark:text-gray-100 text-sm transition-all focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10;
            }
            .badge {
                @apply inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold;
            }
            .badge-success { @apply bg-success-bg text-success border border-success/20; }
            .badge-warning { @apply bg-warning-bg text-warning border border-warning/20; }
            .badge-danger { @apply bg-danger-bg text-danger border border-danger/20; }
            .badge-info { @apply bg-indigo-50 dark:bg-indigo-500/10 text-primary border border-primary/20; }
            
            /* Choices.js Custom Overrides for Theme */
            .choices { @apply mb-0; }
            .choices__inner {
                @apply w-full px-4 py-[10px] rounded-xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-900 dark:text-gray-100 text-sm transition-all shadow-none flex items-center min-h-[46px];
            }
            .is-focused .choices__inner, .is-open .choices__inner {
                @apply border-primary ring-4 ring-primary/10 border-slate-200 dark:border-gray-700;
            }
            .choices[data-type*="select-one"]::after {
                @apply border-slate-400 dark:border-gray-500 mt-[-2.5px];
            }
            .choices[data-type*="select-one"].is-open::after {
                @apply mt-[-2.5px] border-slate-400 dark:border-gray-500;
            }
            .choices__list--dropdown, .choices__list[aria-expanded] {
                @apply bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl mt-2 shadow-lg z-50 text-slate-900 dark:text-gray-100;
            }
            .choices__list--dropdown .choices__item--selectable.is-highlighted, .choices__list[aria-expanded] .choices__item--selectable.is-highlighted {
                @apply bg-indigo-50 dark:bg-indigo-500/20 text-primary dark:text-indigo-300;
            }
            .choices__list--dropdown .choices__item, .choices__list[aria-expanded] .choices__item {
                @apply px-4 py-2 text-sm;
            }
            .choices__input {
                @apply bg-transparent text-slate-900 dark:text-gray-100 border-none outline-none mb-0 w-full;
            }
            .choices__input::placeholder {
                @apply text-slate-400 dark:text-gray-500;
            }
            .choices__list--single {
                @apply p-0;
            }
        }
    </style>
</head>
<body>
    <div class="flex w-full relative">
        <!-- Mobile Overlay -->
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden opacity-0 transition-opacity" id="mobileOverlay"></div>
        
        <!-- Sidebar Navigation -->
        <aside class="w-[280px] h-screen fixed left-0 top-0 glass-panel border-r px-6 py-8 flex flex-col z-50 transition-transform duration-300 max-lg:-translate-x-full" id="sidebar">
            <div class="flex items-center gap-3 mb-12">
                <!-- Gas Cylinder Logo Icon -->
                <i class="ph ph-cylinder text-primary drop-shadow-[0_0_8px_rgba(79,70,229,0.15)] text-[32px]"></i>
                <h1 class="text-xl font-bold tracking-tight bg-gradient-to-br from-primary to-success bg-clip-text text-transparent">AbadiGas</h1>
            </div>
            
            <nav class="flex-grow">
                <ul class="flex flex-col gap-2">
                    <?php 
                    $menuItems = [
                        ['id' => 'dashboard', 'label' => 'Dashboard', 'url' => BASE_URL . 'dashboard', 'icon' => 'ph-house'],
                        ['id' => 'pengiriman', 'label' => 'Log Pengiriman', 'url' => BASE_URL . 'pengiriman', 'icon' => 'ph-truck'],
                        ['id' => 'relasi', 'label' => 'Stok Relasi / Mitra', 'url' => BASE_URL . 'relasi', 'icon' => 'ph-users-three'],
                        ['id' => 'gudang', 'label' => 'Stok Gudang', 'url' => BASE_URL . 'gudang', 'icon' => 'ph-warehouse'],
                        ['id' => 'evaluasi', 'label' => 'Evaluasi Repurchase', 'url' => BASE_URL . 'evaluasi', 'icon' => 'ph-chart-bar']
                    ];
                    
                    foreach ($menuItems as $item):
                        $isActive = $activeController === $item['id'];
                        $activeClasses = $isActive ? 'bg-primary text-white shadow-[0_4px_14px_rgba(79,70,229,0.3)]' : 'text-slate-500 dark:text-gray-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary dark:hover:text-primary';
                    ?>
                    <li>
                        <a href="<?= $item['url'] ?>" class="flex items-center gap-4 px-4 py-3 rounded-xl font-medium transition-all <?= $activeClasses ?>">
                            <i class="ph <?= $item['icon'] ?> text-xl"></i>
                            <span><?= $item['label'] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="pt-6 border-t border-slate-200 dark:border-gray-700 flex flex-col gap-4 mt-auto">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-success flex items-center justify-center text-white font-semibold shadow-md">
                            <?= isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : 'A' ?>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold text-sm text-slate-800 dark:text-gray-200"><?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin' ?></span>
                            <span class="text-xs text-slate-500 dark:text-gray-400">Sistem Gudang</span>
                        </div>
                    </div>
                    <button class="p-2 rounded-full glass-panel text-slate-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-primary transition-all" id="themeToggleBtn" title="Ganti Tema">
                        <!-- Sun Icon -->
                        <i id="theme-icon-light" class="ph-fill ph-sun text-xl hidden dark:block"></i>
                        <!-- Moon Icon -->
                        <i id="theme-icon-dark" class="ph-fill ph-moon text-xl block dark:hidden"></i>
                    </button>
                </div>
                <a href="<?= BASE_URL ?>auth/logout" class="btn btn-secondary btn-sm w-full !text-danger hover:!bg-danger-bg">
                    <i class="ph-bold ph-sign-out text-base"></i>
                    Keluar / Logout
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-grow w-full max-w-full lg:max-w-[calc(100%-280px)] lg:ml-[280px] min-h-screen p-4 sm:p-6 lg:p-10 transition-all duration-300 flex flex-col min-w-0 overflow-x-hidden">
            <!-- Mobile Header -->
            <div class="lg:hidden flex items-center justify-between mb-8 glass-panel p-4 rounded-2xl">
                <div class="flex items-center gap-2">
                    <i class="ph ph-cylinder text-primary text-2xl"></i>
                    <h1 class="text-lg font-bold bg-gradient-to-br from-primary to-success bg-clip-text text-transparent">TabungFlow</h1>
                </div>
                <button id="mobileMenuToggle" class="btn btn-secondary btn-sm !p-2">
                    <i class="ph-bold ph-list text-xl"></i>
                </button>
            </div>

            <!-- Global Flash Messages -->
            <?php if (isset($_GET['msg'])): ?>
                <?php 
                $msgConfig = [
                    'success_create' => ['type' => 'success', 'text' => 'Data berhasil disimpan!'],
                    'success_update' => ['type' => 'success', 'text' => 'Data berhasil diperbarui!'],
                    'success_delete' => ['type' => 'info', 'text' => 'Data berhasil dihapus!'],
                    'error_delete' => ['type' => 'danger', 'text' => 'Gagal menghapus data. Periksa ketergantungan relasi.']
                ];
                
                if (isset($msgConfig[$_GET['msg']])):
                    $msgInfo = $msgConfig[$_GET['msg']];
                    $iconPaths = [
                        'success' => 'ph-check-circle',
                        'info' => 'ph-info',
                        'danger' => 'ph-warning-circle'
                    ];
                ?>
                <div class="flex items-center justify-between p-4 mb-8 rounded-xl badge-<?= $msgInfo['type'] ?> animate-[slideDown_0.4s_ease-out]">
                    <div class="flex items-center gap-3">
                        <i class="ph-fill <?= $iconPaths[$msgInfo['type']] ?> text-xl"></i>
                        <p class="font-medium"><?= $msgInfo['text'] ?></p>
                    </div>
                    <button class="hover:opacity-75 transition-opacity alert-close-btn">&times;</button>
                </div>
                <script>
                    document.querySelector('.alert-close-btn')?.addEventListener('click', function() {
                        this.closest('div').remove();
                    });
                </script>
                <?php endif; ?>
            <?php endif; ?>

