<?php require_once '_header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-shield"></i> Admin Profile</h4>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="profile-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px; font-size: 2.5rem; font-weight: bold;">
                            <?= htmlspecialchars(strtoupper(substr($user->name, 0, 1))) ?>
                        </div>
                        <div>
                            <h5 class="mb-0"><?= htmlspecialchars($user->name) ?></h5>
                            <p class="text-muted mb-1"><i class="fas fa-envelope"></i> <?= htmlspecialchars($user->email) ?></p>
                            <p class="text-muted mb-1">
                                <i class="fas fa-user-tag"></i> Role: 
                                <span class="badge bg-<?= $user->role === 'admin' ? 'danger' : 'warning' ?>">
                                    <?= ucfirst(htmlspecialchars($user->role)) ?>
                                </span>
                            </p>
                            <p class="text-muted mb-0"><i class="fas fa-calendar-alt"></i> Member since: <?= date('F j, Y', strtotime($user->created_at)) ?></p>
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3">Account Settings</h6>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#password-modal">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div class="modal fade" id="password-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-key"></i> Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach($errors as $e) echo "<p class='mb-1'>" . htmlspecialchars($e) . "</p>"; ?>
                    </div>
                <?php endif; ?>

                <form action="profile.php" method="POST">
                    <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
<script>
    // If there are validation errors on page load, automatically show the modal.
    document.addEventListener('DOMContentLoaded', function() {
        var myModal = new bootstrap.Modal(document.getElementById('password-modal'));
        myModal.show();
    });
</script>
<?php endif; ?>

<?php require_once '_footer.php'; ?>