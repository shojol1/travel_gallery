<?php
$pageTitle = "All Travel Experiences";
$pageDesc  = "Browse all my travel destinations and journals.";
require_once __DIR__ . '/includes/header.php';

// Pagination configuration (12 trips per page)
$limit = 12;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$year = isset($_GET['year']) ? trim($_GET['year']) : '';
$district = isset($_GET['district']) ? trim($_GET['district']) : '';

$where = "WHERE published = 1";
$params = [];

if (!empty($year)) {
    $where .= " AND substr(start_date, 1, 4) = ?";
    $params[] = $year;
}
if (!empty($district)) {
    $where .= " AND district = ?";
    $params[] = $district;
}

// Count total records for pagination
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM trips $where");
$stmtCount->execute($params);
$totalTrips = (int) $stmtCount->fetchColumn();
$totalPages = ceil($totalTrips / $limit);

// Fetch paginated trips
$sql = "SELECT t.*, (SELECT COUNT(*) FROM photos p WHERE p.trip_id = t.id) as photo_count 
        FROM trips t 
        $where 
        ORDER BY t.start_date DESC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$trips = $stmt->fetchAll();
?>

<div class="container py-5 mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 border-bottom pb-4">
        <div>
            <h1 class="display-5 font-serif fw-bold">Travel Journals &amp; Trips</h1>
            <p class="text-secondary mb-0">Showing <?= count($trips); ?> of <?= $totalTrips; ?> travel experiences</p>
        </div>

        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="/travel-memories/trips.php" class="btn btn-sm btn-outline-custom <?= empty($year) && empty($district) ? 'active' : ''; ?>">All</a>
            <?php if (!empty($year)): ?>
                <span class="badge bg-secondary p-2 d-flex align-items-center">Year: <?= e($year); ?></span>
            <?php endif; ?>
            <?php if (!empty($district)): ?>
                <span class="badge bg-secondary p-2 d-flex align-items-center">District: <?= e($district); ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Trips Grid -->
    <div class="row">
        <?php if (empty($trips)): ?>
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-4 border">
                    <i class="fas fa-route fa-3x text-muted mb-3"></i>
                    <h4>No trips found matching criteria.</h4>
                    <a href="/travel-memories/trips.php" class="btn btn-primary-custom mt-3">View All Trips</a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($trips as $trip): 
                $coverImg = get_image_url($trip['cover_image'], 'medium');
                $duration = calculate_duration($trip['start_date'], $trip['end_date']);
                $dateDisplay = format_date($trip['start_date'], 'M Y');
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="travel-card">
                    <div class="card-img-wrapper">
                        <img src="<?= $coverImg; ?>" alt="<?= e($trip['title']); ?>" loading="lazy">
                        <div class="card-tag"><i class="fas fa-camera me-1"></i> <?= $trip['photo_count']; ?> Photos</div>
                    </div>
                    <div class="card-body-custom">
                        <div class="card-meta">
                            <i class="fas fa-map-marker-alt text-danger"></i> <?= e($trip['location']); ?>, <?= e($trip['district']); ?>
                        </div>
                        <h3 class="card-title"><?= e($trip['title']); ?></h3>
                        <p class="card-description"><?= e($trip['description']); ?></p>
                        <div class="mt-auto d-flex align-items-center justify-content-between pt-3 border-top">
                            <span class="small text-muted"><i class="far fa-calendar-alt me-1"></i> <?= $dateDisplay; ?> &bull; <?= $duration; ?></span>
                            <a href="/travel-memories/trip.php?slug=<?= e($trip['slug']); ?>" class="btn btn-sm btn-outline-custom">
                                View Memories <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination Controls -->
    <?php if ($totalPages > 1): ?>
    <nav class="mt-5 d-flex justify-content-center">
        <ul class="pagination">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= $page - 1; ?>&year=<?= e($year); ?>&district=<?= e($district); ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?= $i; ?>&year=<?= e($year); ?>&district=<?= e($district); ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= $page + 1; ?>&year=<?= e($year); ?>&district=<?= e($district); ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
