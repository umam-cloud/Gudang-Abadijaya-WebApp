<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | TabungFlow</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Main Style -->
    <link rel="stylesheet" href="public/css/style.css?v=1.1">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--bg-primary) 0%, rgba(99,102,241,0.05) 100%);
            margin: 0;
            padding: 1rem;
        }
        .auth-card {
            background: var(--bg-glass);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-premium);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow-premium);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-header h1 {
            font-size: 1.75rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }
        .auth-header p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
    </style>
</head>
<body data-theme="light">
    
    <div class="auth-card">
        <div class="auth-header">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 48px; height: 48px; color: var(--primary); margin: 0 auto 1rem auto; filter: drop-shadow(0 0 8px var(--primary-glow));">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.283 8.283 0 013 6.3 8.284 8.284 0 003-6.3 8.287 8.287 0 002.962-2.386z" />
            </svg>
            <h1>TabungFlow</h1>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'success_register'): ?>
                <div class="alert-banner" style="background: var(--success-bg); border-color: rgba(13, 148, 136, 0.2); color: var(--success); margin-bottom:1.5rem; padding:0.75rem;">
                    <p style="font-size:0.85rem; text-align:center;">Pendaftaran berhasil! Silakan login.</p>
                </div>
            <?php elseif ($_GET['msg'] === 'success_logout'): ?>
                <div class="alert-banner" style="background: var(--success-bg); border-color: rgba(13, 148, 136, 0.2); color: var(--success); margin-bottom:1.5rem; padding:0.75rem;">
                    <p style="font-size:0.85rem; text-align:center;">Anda telah berhasil keluar.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert-banner" style="margin-bottom:1.5rem; padding:0.75rem;">
                <p style="font-size:0.85rem; text-align:center;"><?= htmlspecialchars($error) ?></p>
            </div>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=login" method="POST">
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group" style="margin-top:1rem;">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 2rem; padding: 0.85rem;">
                Masuk / Login
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted);">
            Belum punya akun? <a href="index.php?controller=auth&action=register" style="color: var(--primary); text-decoration: none; font-weight: 600;">Daftar di sini</a>
        </div>
    </div>

</body>
</html>
