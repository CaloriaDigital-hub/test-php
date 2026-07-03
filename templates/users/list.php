<?php
declare(strict_types=1);

$title = 'Registered Users';
ob_start();
?>
<div id="users-container">
    <div class="page-header">
        <h1 class="page-title">Registered Users</h1>
        <a href="/users/create" class="btn btn-primary">
            + Add New User
        </a>
    </div>

    <!-- Flash message block (e.g. successful deletion or addition) -->
    <?php if ($flashMsg = \App\Core\Session::getFlash('success')): ?>
        <div class="alert alert-success">
            <?= e($flashMsg) ?>
        </div>
    <?php endif; ?>

    <div id="users-table-wrapper" class="table-card" style="height: 501px; overflow-y: auto;">
        <div class="table-wrapper">
            <!-- Main data table, loaded on first visit and updated via AJAX -->
            <table class="data-table">
                <thead>
                    <tr>
                        <!-- Generate sorting links using the helper -->
                        <th><?= sortLink('id', 'ID', $sort, $dir) ?></th>
                        <th><?= sortLink('login', 'Login', $sort, $dir) ?></th>
                        <th><?= sortLink('first_name', 'First Name', $sort, $dir) ?></th>
                        <th><?= sortLink('last_name', 'Last Name', $sort, $dir) ?></th>
                        <th>Gender</th>
                        <th><?= sortLink('birth_date', 'Birth Date', $sort, $dir) ?></th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="users-tbody">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="empty-message">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= e((string)$user->id) ?></td>
                                <td><strong><?= e($user->login) ?></strong></td>
                                <td><?= e($user->firstName) ?></td>
                                <td><?= e($user->lastName) ?></td>
                                <td><?= e($user->gender) ?></td>
                                <td><?= e($user->birthDate) ?></td>
                                <td class="actions">
                                    <a href="/users/<?= $user->id ?>" class="btn-text btn-text-primary">View</a>
                                    <a href="/users/<?= $user->id ?>/edit" class="btn-text btn-text-warning">Edit</a>
                                    <form method="post" action="/users/<?= $user->id ?>/delete" style="display:inline;"
                                          onsubmit="return confirm('Delete this user?')">
                                        <?= \App\Core\Csrf::getField() ?>
                                        <button type="submit" class="btn-text btn-text-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($pages > 1): ?>
        <div class="pagination-container">
            <div id="pagination" class="pagination">
                <?php for ($p = 1; $p <= $pages; $p++): ?>
                    <?php if ($p === $currentPage): ?>
                        <span class="page-item active"><?= $p ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $p ?>&sort=<?= $sort ?>&dir=<?= $dir ?>" class="page-item" data-page="<?= $p ?>">
                            <?= $p ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
</div>


<input type="hidden" id="csrf-token" value="<?= \App\Core\Csrf::generateToken() ?>">

<script src="/assets/js/users.js"></script>

<style>


th.text-right,
td.actions {
    text-align: right;
    white-space: nowrap;
}

.data-table th:last-child,
.data-table td:last-child {
    min-width: 180px;
}
</style>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';