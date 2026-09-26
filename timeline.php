<?php
$pageTitle = "Chronological Travel Timeline";
$pageDesc  = "Complete timeline history of my travels arranged by year and month.";
require_once __DIR__ . '/includes/header.php';

// Fetch all trips ordered chronologically (newest first)
$stmt = $pdo->query("SELECT t.*, 
        (SELECT COUNT(*) FROM photos p WHERE p.trip_id = t.id) as photo_count 
        FROM trips t 
        WHERE t.published = 1 
        ORDER BY t.start_date DESC");
$allTrips = $stmt->fetchAll();

// Group trips by Year
$groupedTrips = [];
foreach ($allTrips as $t) {
    $year = date('Y', strtotime($t['start_date']));
    $groupedTrips[$year][] = $t;
}
?>

<div class="container py-5 mt-4">
    <div class="text-center max-w-700 mx-auto mb-5">
        <h1 class="display-4 font-serif fw-bold mb-3">Travel Timeline</h1>
        <p class="text-secondary fs-5">A chronological journey through time, documenting every expedition, mountain trek, and beach escape.</p>
    </div>

    <div class="timeline-container">
        <?php foreach ($groupedTrips as $year => $trips): ?>
            <!-- Year Header Badge -->
            <div class="timeline-year-badge">
                <span><?= $year; ?></span>
            </div>

            <?php foreach ($trips as $trip): 
                $coverImg = get_image_url($trip['cover_image'], 'medium');
                $monthName = format_date($trip['start_date'], 'F Y');
                $duration = calculate_duration($trip['start_date'], $trip['end_date']);
            ?>
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="row align-items-center g-4">
                    <div class="col-md-6 order-2 order-md-1">
                        <div class="bg-white p-4 rounded-4 border shadow-sm ms-md-4">
                            <span class="badge bg-gold text-dark mb-2"><i class="far fa-calendar-alt me-1"></i> <?= $monthName; ?></span>
                            <h3 class="font-serif fw-bold mb-2"><?= e($trip['title']); ?></h3>
                            <div class="small text-danger mb-3 font-semibold">
                                <i class="fas fa-map-marker-alt me-1"></i> <?= e($trip['location']); ?>, <?= e($trip['district']); ?>
                            </div>
                            <p class="text-secondary mb-3"><?= e($trip['description']); ?></p>
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <span class="small text-muted"><i class="fas fa-clock me-1"></i> <?= $duration; ?></span>
                                <a href="/travel-memories/trip.php?slug=<?= e($trip['slug']); ?>" class="btn btn-sm btn-outline-custom">
                                    View Memories <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 order-1 order-md-2">
                        <div class="rounded-4 overflow-hidden border shadow-sm">
                            <a href="/travel-memories/trip.php?slug=<?= e($trip['slug']); ?>">
                                <img src="<?= $coverImg; ?>" alt="<?= e($trip['title']); ?>" class="w-100" style="height: 250px; object-fit: cover;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
