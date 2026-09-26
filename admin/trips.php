<?php
require_once __DIR__ . '/includes/admin_header.php';

// Fetch all trips
$stmt = $pdo->query("SELECT t.*, 
    (SELECT COUNT(*) FROM photos p WHERE p.trip_id = t.id) as photo_count 
    FROM trips t ORDER BY t.start_date DESC");
$trips = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Manage Trips</h2>
        <p class="text-muted small mb-0">Total <?= count($trips); ?> trip journals recorded.</p>
    </div>

    <a href="/travel-memories/admin/add-trip.php" class="btn btn-success shadow-sm" style="background-color: #2d5a4c; border: none;">
        <i class="fas fa-plus me-1"></i> Add New Trip
    </a>
</div>

<div class="card border-0 shadow-sm bg-white p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Cover</th>
                    <th>Trip Title</th>
                    <th>District / Country</th>
                    <th>Dates</th>
                    <th>Photos</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($trips)): ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">No trips created yet.</td></tr>
                <?php else: ?>
                    <?php foreach ($trips as $t): 
                        $cover = !empty($t['cover_image']) ? '/travel-memories/' . e($t['cover_image']) : '/travel-memories/uploads/trips/coxsbazar_cover.jpg';
                    ?>
                    <tr>
                        <td style="width: 70px;">
                            <img src="<?= $cover; ?>" class="rounded object-fit-cover" style="width: 60px; height: 45px;" alt="Cover">
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?= e($t['title']); ?></div>
                            <div class="small text-muted"><?= e($t['location']); ?></div>
                        </td>
                        <td>
                            <div><?= e($t['district']); ?></div>
                            <div class="small text-muted"><?= e($t['country']); ?></div>
                        </td>
                        <td class="small">
                            <?= format_date($t['start_date'], 'd M Y'); ?>
                        </td>
                        <td>
                            <a href="/travel-memories/admin/photos.php?trip_id=<?= $t['id']; ?>" class="badge bg-primary text-decoration-none">
                                <i class="fas fa-camera me-1"></i> <?= $t['photo_count']; ?>
                            </a>
                        </td>
                        <td>
                            <?= $t['featured'] ? '<span class="badge bg-warning text-dark"><i class="fas fa-star"></i> Featured</span>' : '<span class="text-muted small">&mdash;</span>'; ?>
                        </td>
                        <td>
                            <?= $t['published'] ? '<span class="badge bg-success">Published</span>' : '<span class="badge bg-secondary">Draft</span>'; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="/travel-memories/admin/photos.php?trip_id=<?= $t['id']; ?>" class="btn btn-outline-primary" title="Manage Photos"><i class="fas fa-images"></i></a>
                                <a href="/travel-memories/admin/edit-trip.php?id=<?= $t['id']; ?>" class="btn btn-outline-secondary" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="/travel-memories/admin/delete-trip.php?id=<?= $t['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete this trip and all its photos?');" title="Delete"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
