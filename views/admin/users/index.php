<?php require_once __DIR__ . '/../_header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-users"></i> User Management</h4>
                    <span class="badge bg-light text-dark">Total Users: <?= count($users) ?></span>
                </div>
                <div class="card-body">
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($errors): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Failed Attempts</th>
                                    <th>Account Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user->id) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="user-avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                     style="width: 30px; height: 30px; font-size: 0.8rem; font-weight: bold;">
                                                    <?= htmlspecialchars(strtoupper(substr($user->name, 0, 1))) ?>
                                                </div>
                                                <?= htmlspecialchars($user->name) ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($user->email) ?></td>
                                        <td>
                                            <?php if ($user->role === 'manager'): ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-crown"></i> Manager
                                                </span>
                                            <?php elseif ($user->role === 'admin'): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-shield-alt"></i> Admin
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-user"></i> User
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user->failed_login_attempts > 0): ?>
                                                <span class="badge bg-warning text-dark">
                                                    <?= $user->failed_login_attempts ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user->account_locked_until && strtotime($user->account_locked_until) > time()): ?>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-lock"></i> Locked
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-success">
                                                    <i class="fas fa-unlock"></i> Active
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($user->created_at)) ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <?php $currentUserId = Session::get('user_id'); ?>
                                                <?php if ($user->id == $currentUserId): ?>
                                                    <span class="text-muted small">
                                                        <i class="fas fa-info-circle"></i> Current User
                                                    </span>
                                                <?php else: ?>
                                                    <?php if ($user->role === 'user'): ?>
                                                        <button type="button" class="btn btn-outline-primary btn-sm me-1" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#roleModal"
                                                                data-user-id="<?= $user->id ?>"
                                                                data-user-name="<?= htmlspecialchars($user->name) ?>"
                                                                data-new-role="admin"
                                                                title="Make Admin">
                                                            <i class="fas fa-shield-alt"></i> Admin
                                                        </button>
                                                        <button type="button" class="btn btn-outline-success btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#roleModal"
                                                                data-user-id="<?= $user->id ?>"
                                                                data-user-name="<?= htmlspecialchars($user->name) ?>"
                                                                data-new-role="manager"
                                                                title="Make Manager">
                                                            <i class="fas fa-crown"></i> Manager
                                                        </button>
                                                    <?php elseif ($user->role === 'admin'): ?>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm me-1" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#roleModal"
                                                                data-user-id="<?= $user->id ?>"
                                                                data-user-name="<?= htmlspecialchars($user->name) ?>"
                                                                data-new-role="user"
                                                                title="Make User">
                                                            <i class="fas fa-user"></i> User
                                                        </button>
                                                        <button type="button" class="btn btn-outline-success btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#roleModal"
                                                                data-user-id="<?= $user->id ?>"
                                                                data-user-name="<?= htmlspecialchars($user->name) ?>"
                                                                data-new-role="manager"
                                                                title="Make Manager">
                                                            <i class="fas fa-crown"></i> Manager
                                                        </button>
                                                    <?php elseif ($user->role === 'manager'): ?>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm me-1" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#roleModal"
                                                                data-user-id="<?= $user->id ?>"
                                                                data-user-name="<?= htmlspecialchars($user->name) ?>"
                                                                data-new-role="user"
                                                                title="Make User">
                                                            <i class="fas fa-user"></i> User
                                                        </button>
                                                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#roleModal"
                                                                data-user-id="<?= $user->id ?>"
                                                                data-user-name="<?= htmlspecialchars($user->name) ?>"
                                                                data-new-role="admin"
                                                                title="Make Admin">
                                                            <i class="fas fa-shield-alt"></i> Admin
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (empty($users)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No users found</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Role Change Confirmation Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">Change User Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="users.php?action=change_role" method="POST">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Warning:</strong> This action will change the user's access permissions.
                    </div>
                    
                    <p>Are you sure you want to change <strong id="modal-user-name"></strong>'s role to <strong id="modal-new-role"></strong>?</p>
                    
                    <input type="hidden" name="user_id" id="modal-user-id">
                    <input type="hidden" name="new_role" id="modal-new-role-input">
                    <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Change</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleModal = document.getElementById('roleModal');
    if (roleModal) {
        roleModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const newRole = button.getAttribute('data-new-role');
            
            // Update modal content
            document.getElementById('modal-user-name').textContent = userName;
            document.getElementById('modal-new-role').textContent = newRole;
            document.getElementById('modal-user-id').value = userId;
            document.getElementById('modal-new-role-input').value = newRole;
        });
    }
});
</script>

<?php require_once __DIR__ . '/../_footer.php'; ?> 