<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran | TabungFlow</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        primary: '#6366f1',
                        secondary: '#64748b',
                        success: '#0d9488',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                        info: '#0ea5e9'
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .glass-panel {
                @apply bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border border-white/20 dark:border-slate-700/50;
            }
            .form-label {
                @apply block text-sm font-semibold text-slate-700 dark:text-gray-300 mb-2;
            }
            .form-control {
                @apply w-full px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-gray-100 placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all;
            }
            .btn-primary {
                @apply inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary hover:bg-indigo-600 text-white font-medium rounded-xl shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5 active:translate-y-0;
            }
            .form-help {
                @apply block text-xs text-slate-500 mt-2;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-slate-50 to-indigo-50/50 dark:from-slate-900 dark:to-slate-800 text-slate-800 dark:text-slate-200 font-sans">
    
    <div class="glass-panel w-full max-w-md p-10 rounded-3xl shadow-xl shadow-indigo-500/10 dark:shadow-none">
        <div class="text-center mb-8">
            <i class="ph ph-cylinder text-[48px] mx-auto mb-4 text-primary drop-shadow-[0_0_8px_rgba(99,102,241,0.5)] block text-center"></i>
            <h1 class="text-3xl font-extrabold bg-gradient-to-br from-primary to-cyan-500 bg-clip-text text-transparent font-display mb-2">Buat Akun Baru</h1>
            <p class="text-slate-500 dark:text-gray-400 text-sm">Daftar sebagai pengguna sistem AbadiGas</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-danger/10 border border-danger/20 text-danger p-3 rounded-xl mb-6 text-sm text-center font-medium">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=register" method="POST" class="space-y-5">
            <div>
                <label class="form-label" for="nama_lengkap">Nama Lengkap</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" required>
            </div>
            <div>
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div>
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <span class="form-help">Minimal 6 karakter</span>
            </div>
            
            <button type="submit" class="btn-primary w-full py-3 mt-4 text-base">
                Daftar Akun
            </button>
        </form>
        
        <div class="text-center mt-8 text-sm text-slate-500 dark:text-gray-400">
            Sudah punya akun? <a href="index.php?controller=auth&action=login" class="text-primary font-semibold hover:text-indigo-700 dark:hover:text-indigo-400 transition-colors">Masuk di sini</a>
        </div>
    </div>

    <script>
        // Check for saved theme preference, otherwise use system preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</body>
</html>
