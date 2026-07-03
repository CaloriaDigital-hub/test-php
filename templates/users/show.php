<?php
declare(strict_types=1);

$title = 'View User';
ob_start();
?>
<!-- User details card styled as a modal window -->
<div class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h1 class="modal-title">User Details</h1>
        </div>
        <div class="modal-body">
            <!-- Flash message block (e.g. successful update or creation) -->
            <?php if ($flashMsg = \App\Core\Session::getFlash('success')): ?>
                <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                    <?= e($flashMsg) ?>
                </div>
            <?php endif; ?>
            <dl class="dl-grid">
                <div>
                    <dt class="dl-term">ID</dt>
                    <dd class="dl-desc"><?= e((string)$user->id) ?></dd>
                </div>
                <div>
                    <dt class="dl-term">Login</dt>
                    <dd class="dl-desc"><?= e($user->login) ?></dd>
                </div>
                <div>
                    <dt class="dl-term">First Name</dt>
                    <dd class="dl-desc"><?= e($user->firstName) ?></dd>
                </div>
                <div>
                    <dt class="dl-term">Last Name</dt>
                    <dd class="dl-desc"><?= e($user->lastName) ?></dd>
                </div>
                <div>
                    <dt class="dl-term">Gender</dt>
                    <dd class="dl-desc"><?= e($user->gender) ?></dd>
                </div>
                <div>
                    <dt class="dl-term">Birth Date</dt>
                    <dd class="dl-desc"><?= e($user->birthDate) ?></dd>
                </div>
                <div>
                    <dt class="dl-term">Created At</dt>
                    <dd class="dl-desc"><?= e($user->createdAt) ?></dd>
                </div>
                <div>
                    <dt class="dl-term">Updated At</dt>
                    <dd class="dl-desc"><?= e($user->updatedAt) ?></dd>
                </div>
            </dl>
        </div>
        <div class="modal-footer" style="justify-content: flex-start;">
            <a href="<?= e(\App\Core\Session::getLastListUrl()) ?>" class="btn-text btn-text-primary">
                &larr; Back to list
            </a>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';