<?php require_once '../includes/header.php'; ?>
<?php require_once '../includes/db.php'; ?>

<main>
    <h2 style="text-align:center; margin-bottom: 0.5em;">Our Locations</h2>
    <p style="text-align:center; margin-bottom: 2em;">Find a Mixue store near you!</p>



    <?php
    // Fetch all branches
    $db = Database::getInstance();
    $stmt = $db->query('SELECT * FROM branches');
    $branches = $stmt->fetchAll();
    ?>

    <div class="branches-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
        <?php foreach ($branches as $branch): ?>
            <div class="branch-card" style="background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.10); padding: 2rem; display: flex; flex-direction: column; align-items: flex-start; min-height: 180px;">
                <h3 style="margin-top:0; color: var(--primary-color, #007bff); font-size: 1.3rem; margin-bottom: 0.5em;">
                    <i class="fa fa-map-marker-alt" style="color: #e74c3c; margin-right: 8px;"></i>
                    <?= htmlspecialchars($branch->name) ?>
                </h3>
                <div style="margin-bottom: 0.5em; color: #555;">
                    <i class="fa fa-location-dot" style="margin-right: 6px; color: #888;"></i>
                    <?= htmlspecialchars($branch->address) ?>
                </div>
                <?php if (!empty($branch->phone)): ?>
                    <div style="color: #555;">
                        <i class="fa fa-phone" style="margin-right: 6px; color: #888;"></i>
                        <?= htmlspecialchars($branch->phone) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

<?php require_once '../includes/footer.php'; ?>