<?php
declare(strict_types=1);

// Determine if we are editing an existing user or creating a new one
$isEdit = isset($user) && $user !== null;
$title = $isEdit ? 'Edit User' : 'Create User';
$formAction = $isEdit ? '/users/' . $user->id : '/users';

ob_start();
?>
<!-- Pseudo-modal window: centers the form over a dark overlay background -->
<div class="modal-overlay">
    <div class="modal-card">
        <div class="modal-header">
            <h1 class="modal-title"><?= e($title) ?></h1>
        </div>
        <div class="modal-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $msg): ?>
                            <li><?= e($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e($formAction) ?>">
                <?= \App\Core\Csrf::getField() ?>

                <div class="form-group">
                    <label for="login" class="form-label">Login</label>
                    <input type="text" name="login" id="login" required maxlength="100"
                           value="<?= e($old['login'] ?? $user->login ?? '') ?>"
                           class="form-control">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" <?= $isEdit ? '' : 'required' ?>
                           class="form-control">
                    <?php if ($isEdit): ?>
                        <p class="form-hint">Leave blank to keep current password.</p>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div>
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" id="first_name" required
                               value="<?= e($old['first_name'] ?? $user->firstName ?? '') ?>"
                               class="form-control">
                    </div>
                    <div>
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" id="last_name" required
                               value="<?= e($old['last_name'] ?? $user->lastName ?? '') ?>"
                               class="form-control">
                    </div>
                </div>

                <div class="form-row">
                    <div>
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" required class="form-control">
                            <option value="">Select...</option>
                            <?php foreach (\App\Enums\Gender::options() as $value => $label): ?>
                                <?php $selected = ($old['gender'] ?? $user->gender ?? '') === $value ? 'selected' : ''; ?>
                                <option value="<?= e($value) ?>" <?= $selected ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="birth_date" class="form-label">Birth Date</label>
                        <input type="date" name="birth_date" id="birth_date" required
                               value="<?= e($old['birth_date'] ?? $user->birthDate ?? '') ?>"
                               class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?= e($_SESSION['last_list_url'] ?? '/users') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';