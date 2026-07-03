<?php
$title = 'Admin Login';
ob_start();
?>
<div class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h1 class="modal-title">Admin Login</h1>
            <p class="form-hint">Please enter your credentials</p>
        </div>
        
        <div class="modal-body">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="/login">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required autofocus placeholder="admin">
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                </div>
                
                <div class="form-group" style="margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Sign in</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';