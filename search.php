<?php
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$pageTitle = "Search Results: " . $query;
require_once __DIR__ . '/includes/header.php';

$trips = [];
if (!empty($query)) {
    $searchTerm = "%" . $query . "%";
    $sql = "SELECT t.*, (SELECT COUNT(*) FROM photos p WHERE p.trip_id = t.id) as photo_count 
            FROM trips t 
            WHERE t.published = 1 
            AND (
                t.title LIKE ? OR 
                t.location LIKE ? OR 
                t.district LIKE ? OR 
                t.division LIKE ? OR 
                t.country LIKE ? OR 
                t.description LIKE ? OR 
                substr(t.start_date, 1, 4) LIKE ?
            )
            ORDER BY t.start_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    $trips = $stmt->fetchAll();
}
?>

<div class="container py-5 mt-4">
    <div class="mb-5 border-bottom pb-4">
        <h1 class="display-5 font-serif fw-bold">Search Travel Memories</h1>
        <p class="text-secondary mb-0">Showing search results for: <span class="badge bg-gold text-dark fs-6">"<?= e($query); ?>"</span> (Found <?= count($trips); ?> matches)</p>
    </div>

    <?php if (empty($trips)): ?>
        <div class="col-12 text-center py-5">
            <div class="p-5 bg-white rounded-4 border">
                <i class="fas fa-search-location fa-3x text-muted mb-3"></i>
                <h4>No memories found matching "<?= e($query); ?>"</h4>
                <p class="text-secondary">Try searching for location names like "Cox's Bazar", "Bandarban", "Sylhet", "Rajshahi", or years like "2026".</p>
                <a href="/travel-memories/trips.php" class="btn btn-primary-custom mt-3">Browse All Trips</a>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
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
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
