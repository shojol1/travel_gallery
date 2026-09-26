<?php
require_once __DIR__ . '/includes/admin_header.php';

$stats = get_trip_stats();

// Fetch total years
$stmtYears = $pdo->query("SELECT COUNT(DISTINCT substr(start_date, 1, 4)) FROM trips");
$totalYears = (int) $stmtYears->fetchColumn();

// Fetch recent trips
$stmtRecentTrips = $pdo->query("SELECT t.*, (SELECT COUNT(*) FROM photos p WHERE p.trip_id = t.id) as photo_count 
    FROM trips t ORDER BY t.created_at DESC LIMIT 5");
$recentTrips = $stmtRecentTrips->fetchAll();

// Fetch recent photos
$stmtRecentPhotos = $pdo->query("SELECT p.*, t.title as trip_title FROM photos p JOIN trips t ON p.trip_id = t.id ORDER BY p.created_at DESC LIMIT 6");
$recentPhotos = $stmtRecentPhotos->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Dashboard Overview</h2>
        <p class="text-muted small mb-0">Welcome back, <?= e($adminUser); ?>. Here is a summary of your website.</p>
    </div>
    
    <div class="d-flex gap-2">
        <a href="/travel-memories/admin/add-trip.php" class="btn btn-success shadow-sm" style="background-color: #2d5a4c; border: none;">
            <i class="fas fa-plus me-1"></i> Add New Trip
        </a>
        <a href="/travel-memories/admin/photos.php" class="btn btn-outline-dark">
            <i class="fas fa-cloud-upload-alt me-1"></i> Upload Photos
        </a>
    </div>
</div>

<!-- Quick Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-success-subtle text-success fs-3"><i class="fas fa-route"></i></div>
                <div>
                    <h3 class="fw-bold mb-0"><?= $stats['trips']; ?></h3>
                    <div class="small text-muted">Total Trips</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-primary-subtle text-primary fs-3"><i class="fas fa-images"></i></div>
                <div>
                    <h3 class="fw-bold mb-0"><?= $stats['photos']; ?></h3>
                    <div class="small text-muted">Total Photos</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-danger-subtle text-danger fs-3"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <h3 class="fw-bold mb-0"><?= $stats['districts']; ?></h3>
                    <div class="small text-muted">Districts Visited</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 rounded-circle bg-warning-subtle text-warning fs-3"><i class="fas fa-calendar"></i></div>
                <div>
                    <h3 class="fw-bold mb-0"><?= $totalYears; ?></h3>
                    <div class="small text-muted">Travel Years</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Trips Table -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Recent Trips</h5>
                <a href="/travel-memories/admin/trips.php" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Trip Title</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Photos</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentTrips)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No trips created yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentTrips as $rt): ?>
                            <tr>
                                <td class="fw-semibold"><?= e($rt['title']); ?></td>
                                <td><?= e($rt['location']); ?>, <?= e($rt['district']); ?></td>
                                <td><?= format_date($rt['start_date'], 'd M Y'); ?></td>
                                <td><span class="badge bg-secondary"><?= $rt['photo_count']; ?></span></td>
                                <td>
                                    <?php if ($rt['published']): ?>
                                        <span class="badge bg-success-subtle text-success">Published</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/travel-memories/admin/edit-trip.php?id=<?= $rt['id']; ?>" class="btn btn-sm btn-light" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="/travel-memories/admin/photos.php?trip_id=<?= $rt['id']; ?>" class="btn btn-sm btn-light" title="Upload Photos"><i class="fas fa-camera"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Uploaded Photos Grid -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-white p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Recent Photos</h5>
                <a href="/travel-memories/admin/photos.php" class="btn btn-sm btn-link text-decoration-none">Manage</a>
            </div>

            <div class="row g-2">
                <?php if (empty($recentPhotos)): ?>
                    <div class="col-12 text-center text-muted py-4">No uploaded photos yet.</div>
                <?php else: ?>
                    <?php foreach ($recentPhotos as $rp): 
                        $thumb = !empty($rp['thumbnail_path']) ? '/travel-memories/' . e($rp['thumbnail_path']) : '/travel-memories/' . e($rp['image_path']);
                    ?>
                    <div class="col-4">
                        <div class="rounded overflow-hidden bg-light" style="height: 80px;">
                            <img src="<?= $thumb; ?>" class="w-100 h-100 object-fit-cover" alt="Thumb">
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
