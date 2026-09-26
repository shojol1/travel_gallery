<?php
/**
 * AJAX Endpoint for Filtering Trips
 * Travel Memories Website
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$year = isset($_GET['year']) ? trim($_GET['year']) : '';
$district = isset($_GET['district']) ? trim($_GET['district']) : '';

$sql = "SELECT t.*, 
        (SELECT COUNT(*) FROM photos p WHERE p.trip_id = t.id) as photo_count 
        FROM trips t 
        WHERE t.published = 1";
$params = [];

if (!empty($year)) {
    $sql .= " AND YEAR(t.start_date) = ?";
    $params[] = $year;
}

if (!empty($district)) {
    $sql .= " AND t.district = ?";
    $params[] = $district;
}

$sql .= " ORDER BY t.start_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$trips = $stmt->fetchAll();

if (empty($trips)) {
    echo '<div class="col-12 text-center py-5">
            <div class="p-5 bg-white rounded-4 border">
                <i class="fas fa-compass fa-3x text-muted mb-3"></i>
                <h4>No travel memories found</h4>
                <p class="text-secondary">Try adjusting your filters or search keywords.</p>
            </div>
          </div>';
    exit;
}

foreach ($trips as $trip):
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
