<?php
declare(strict_types=1);

use App\Core\Session;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'User Administration') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="https://code.jquery.com/jquery-4.0.0.min.js"></script>
</head>
<body>
    <?php if (Session::isLoggedIn()): ?>
        <nav class="navbar">
            <div class="navbar-container">
                <div class="nav-links">
                    <a href="/users">Users</a>
                </div>
                <div class="nav-user">
                    <span class="nav-user-name"><?= e($_SESSION['admin_username'] ?? '') ?></span>
                    <form method="post" action="/logout" style="display:inline;">
                        <?= \App\Core\Csrf::getField() ?>
                        <button type="submit" class="nav-logout">Logout</button>
                    </form>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="main-content">
        <?= $content ?? '' ?>
    </main>
</body>
</html>