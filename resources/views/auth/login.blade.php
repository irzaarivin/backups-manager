<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · Backup Finder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <main class="login-shell">
        <section class="login-visual" aria-label="Backup Finder">
            <div class="window-chrome">
                <span class="traffic red"></span>
                <span class="traffic yellow"></span>
                <span class="traffic green"></span>
                <span class="window-title">Backup Finder</span>
            </div>
            <div class="visual-copy">
                <div class="brand-mark brand-mark-large">⌁</div>
                <p class="eyebrow">SECURE BACKUP LIBRARY</p>
                <h1>Your files,<br><em>quietly organized.</em></h1>
                <p class="visual-description">A focused workspace for browsing, inspecting, and retrieving your database backups.</p>
            </div>
            <div class="visual-lines" aria-hidden="true"></div>
            <div class="visual-footer">
                <span>Private workspace</span>
                <span>Encrypted in transit</span>
            </div>
        </section>
        <section class="login-panel">
            <div class="login-panel-inner">
                <div class="mobile-brand"><span class="brand-mark">⌁</span><span>Backup Finder</span></div>
                <div class="login-heading">
                    <p class="eyebrow">WELCOME BACK</p>
                    <h2>Sign in to your<br><span>backup space.</span></h2>
                    <p>Use your workspace credentials to continue.</p>
                </div>
                <form method="POST" action="{{ route('login.store') }}" class="login-form">
                    @csrf
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" placeholder="Enter your username" required autofocus>
                    @error('username') <span class="form-error">{{ $message }}</span> @enderror
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required>
                        <button type="button" class="icon-button password-toggle" data-password-toggle aria-label="Show password">◉</button>
                    </div>
                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                    <label class="check-row"><input type="checkbox" name="remember" value="1"><span>Keep me signed in</span></label>
                    <button type="submit" class="primary-button">Continue <span aria-hidden="true">→</span></button>
                </form>
                <p class="login-note">Access is limited to your configured backup directory.</p>
            </div>
        </section>
    </main>
</body>
</html>
