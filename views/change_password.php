<?php
// views/change_password.php - THE NEW, CLEAN "VIEW" VERSION

// The header and config files are now included by the main entry point.
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container" style="max-width: 500px; margin: 2rem auto;">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title text-center mb-4"><i class="fas fa-key"></i> Change Password</h3>
            
            <!-- These variables ($success, $errors) are now provided by the controller -->
            <?php if (isset($success) && $success): ?>
                <div class="alert alert-success text-center"> <?= htmlspecialchars($success) ?> </div>
            <?php elseif (!empty($errors)): ?>
                <div class="alert alert-danger text-center">
                    <?php foreach((array)$errors as $e) echo htmlspecialchars($e) . '<br>'; ?>
                </div>
            <?php endif; ?>
            
            <!-- The form action now points to our dedicated action script -->
            <form method="POST" action="<?= BASE_URL ?>/actions/change_password.php">
                <input type="hidden" name="_csrf" value="<?= Session::generateCsrfToken() ?>">
                
                <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" data-target="current_password"><i class="fa fa-eye-slash"></i></button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="new_password" name="new_password" required autocomplete="new-password">
                        <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" data-target="new_password"><i class="fa fa-eye-slash"></i></button>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required autocomplete="new-password">
                        <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1" data-target="confirm_new_password"><i class="fa fa-eye-slash"></i></button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Update Password</button>
                <a href="<?= BASE_URL ?>/profile.php" class="btn btn-secondary w-100 mt-2">Back to Profile</a>
            </form>
        </div>
    </div>
</div>



<?php require_once __DIR__ . '/../includes/footer.php'; ?>
<script>
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
    });
</script>
<?php require_once '../includes/footer.php'; ?> 