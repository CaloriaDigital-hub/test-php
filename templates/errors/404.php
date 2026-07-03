<?php
$title = '404 Not Found';
ob_start();
?>
<div class="error-page">
    <div class="error-code">404</div>
    <h1 class="error-title">Page not found</h1>
    <p class="error-desc">
        The user or page you are looking for doesn't exist, has been removed, or is temporarily unavailable.
    </p>
    <div class="error-actions">
        <a href="<?= e($_SESSION['last_list_url'] ?? '/users') ?>" class="btn btn-primary">
            Go back to list
        </a>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';