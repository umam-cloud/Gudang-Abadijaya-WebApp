<?php
$activeController = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
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
    <!-- Main Style -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <!-- Gas Cylinder Logo Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.283 8.283 0 013 6.3 8.284 8.284 0 003-6.3 8.287 8.287 0 002.962-2.386z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5h.008v.008H9V13.5zm3 0h.008v.008H12V13.5zm3 0h.008v.008H15V13.5z" />
                </svg>
                <h1>TabungFlow</h1>
            </div>
            
            <nav style="flex-grow: 1;">
                <ul class="sidebar-menu">
                    <li class="menu-item <?= $activeController === 'dashboard' ? 'active' : '' ?>">
                        <a href="index.php?controller=dashboard&action=index">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $activeController === 'pengiriman' ? 'active' : '' ?>">
                        <a href="index.php?controller=pengiriman&action=index">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 18.75a1.5 1.5 0 01-3 0m11.25 0a1.5 1.5 0 01-3 0m3 0h1.125v-1.5M16.5 18.75a1.5 1.5 0 00-3 0M16.5 18.75h-3.75m1.5-11.25h-3m3 3h-3m3 3h-3M6.75 2.25h10.5a2.25 2.25 0 012.25 2.25v13.5a2.25 2.25 0 01-2.25 2.25H6.75a2.25 2.25 0 01-2.25-2.25V4.25a2.25 2.25 0 012.25-2.25z" />
                            </svg>
                            <span>Log Pengiriman</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $activeController === 'relasi' ? 'active' : '' ?>">
                        <a href="index.php?controller=relasi&action=index">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.005 9.005 0 00-6-6.197V8.5a3.5 3.5 0 117 0v4.023a9.005 9.005 0 00-1 6.197zm-6-6.197a9.005 9.005 0 00-6 6.197V12.523A9.003 9.003 0 0012 8.5v4.023zm-6 6.197a9.003 9.003 0 001 6.197V18.72zm6 0v2.28c0 .248-.202.45-.45.45H10.45a.45.45 0 01-.45-.45v-2.28m6 0v2.28c0 .248-.202.45-.45.45h-1.1c-.248 0-.45-.202-.45-.45v-2.28M6 18.72v2.28c0 .248.202.45.45.45h1.1a.45.45 0 00.45-.45v-2.28" />
                            </svg>
                            <span>Stok Relasi / Mitra</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $activeController === 'gudang' ? 'active' : '' ?>">
                        <a href="index.php?controller=gudang&action=index">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h18v3H3V3z" />
                            </svg>
                            <span>Stok Gudang</span>
                        </a>
                    </li>
                    <li class="menu-item <?= $activeController === 'evaluasi' ? 'active' : '' ?>">
                        <a href="index.php?controller=evaluasi&action=index">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <span>Evaluasi Repurchase</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="sidebar-footer" style="flex-direction: column; align-items: stretch; gap: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div class="admin-profile">
                        <div class="avatar"><?= isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : 'A' ?></div>
                        <div class="admin-info">
                            <span><?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin' ?></span>
                            <span>Sistem Gudang</span>
                        </div>
                    </div>
                    <button class="theme-toggle-btn" id="themeToggleBtn" title="Ganti Tema">
                        <!-- Sun Icon -->
                        <svg id="theme-icon-light" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21M5.22 5.22l1.59 1.59m10.38 10.38l1.59 1.59M12 6a6 6 0 110 12 6 6 0 010-12zM3 12h2.25m13.5 0H21m-15.78 5.78l1.59-1.59m10.38-10.38l1.59-1.59" />
                        </svg>
                        <!-- Moon Icon -->
                        <svg id="theme-icon-dark" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                    </button>
                </div>
                <a href="index.php?controller=auth&action=logout" class="btn btn-secondary btn-sm" style="color: var(--danger); text-align: center; justify-content: center; width: 100%;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Keluar / Logout
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Global Flash Messages -->
            <?php if (isset($_GET['msg'])): ?>
                <?php if ($_GET['msg'] === 'success_create'): ?>
                    <div class="alert-banner" style="background: var(--success-bg); border-color: rgba(13, 148, 136, 0.2); color: var(--success);">
                        <div class="alert-content">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Data berhasil disimpan!</p>
                        </div>
                        <button class="alert-close" style="color: var(--success);">&times;</button>
                    </div>
                <?php elseif ($_GET['msg'] === 'success_update'): ?>
                    <div class="alert-banner" style="background: var(--success-bg); border-color: rgba(13, 148, 136, 0.2); color: var(--success);">
                        <div class="alert-content">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>Data berhasil diperbarui!</p>
                        </div>
                        <button class="alert-close" style="color: var(--success);">&times;</button>
                    </div>
                <?php elseif ($_GET['msg'] === 'success_delete'): ?>
                    <div class="alert-banner" style="background: rgba(99, 102, 241, 0.08); border-color: rgba(99, 102, 241, 0.2); color: var(--primary);">
                        <div class="alert-content">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <p>Data berhasil dihapus!</p>
                        </div>
                        <button class="alert-close" style="color: var(--primary);">&times;</button>
                    </div>
                <?php elseif ($_GET['msg'] === 'error_delete'): ?>
                    <div class="alert-banner">
                        <div class="alert-content">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p>Gagal menghapus data. Periksa ketergantungan relasi.</p>
                        </div>
                        <button class="alert-close">&times;</button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
