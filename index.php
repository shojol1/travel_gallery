<?php
$pageTitle = "MY TRAVEL MEMORIES";
$pageDesc  = "Places I've Been, Moments I've Captured.";
require_once __DIR__ . '/includes/header.php';

$stats = get_trip_stats();

// Fetch distinct years and districts for AJAX Filter bar
$stmtYears = $pdo->query("SELECT DISTINCT YEAR(start_date) as y FROM trips WHERE published = 1 ORDER BY y DESC");
$filterYears = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

$stmtDistricts = $pdo->query("SELECT DISTINCT district FROM trips WHERE published = 1 ORDER BY district ASC");
$filterDistricts = $stmtDistricts->fetchAll(PDO::FETCH_COLUMN);

// Fetch initial featured/recent trips
$stmtTrips = $pdo->query("SELECT t.*, 
    (SELECT COUNT(*) FROM photos p WHERE p.trip_id = t.id) as photo_count 
    FROM trips t 
    WHERE t.published = 1 
    ORDER BY t.start_date DESC LIMIT 9");
$trips = $stmtTrips->fetchAll();

// Hero background image
$heroImg = get_image_url($trips[0]['cover_image'] ?? '', 'medium');
?>

<!-- Parallax Hero Section -->
<section class="hero-section">
    <img src="<?= $heroImg; ?>" alt="Hero Cover" class="hero-bg">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="hero-title"><?= e(get_site_setting('site_title', 'MY TRAVEL MEMORIES')); ?></h1>
        <p class="hero-subtitle"><?= e(get_site_setting('site_subtitle', 'Places I\'ve Been, Moments I\'ve Captured.')); ?></p>
        <a href="#timeline-section" class="btn btn-primary-custom shadow-lg">
            <i class="fas fa-compass me-2"></i><?= e(get_site_setting('hero_button_text', 'Explore My Journey')); ?>
        </a>
    </div>
</section>

<!-- Dynamic Travel Statistics Section -->
<section class="stats-section mb-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['trips']; ?>+</div>
                    <div class="stat-label">Trips</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['places']; ?>+</div>
                    <div class="stat-label">Places</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= $stats['districts']; ?>+</div>
                    <div class="stat-label">Districts</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?= number_format($stats['photos']); ?>+</div>
                    <div class="stat-label">Photos</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Timeline & Filter Section -->
<section id="timeline-section" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 font-serif fw-bold mb-3">Travel Journey &amp; Memories</h2>
            <p class="text-secondary max-w-600 mx-auto">Chronological moments and visual stories from my journeys across different regions.</p>
        </div>

        <!-- Filter Bar -->
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-5">
            <button class="btn btn-primary-custom ajax-filter-btn active" data-year="" data-district="">All Memories</button>
            
            <?php foreach ($filterYears as $yr): ?>
                <button class="btn btn-outline-custom ajax-filter-btn" data-year="<?= $yr; ?>"><?= $yr; ?></button>
            <?php endforeach; ?>

            <?php foreach ($filterDistricts as $dist): ?>
                <button class="btn btn-outline-custom ajax-filter-btn" data-district="<?= e($dist); ?>"><?= e($dist); ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Trips Cards Grid -->
        <div class="row" id="trips-grid-container">
            <?php if (empty($trips)): ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded-4 border">
                        <i class="fas fa-compass fa-3x text-muted mb-3"></i>
                        <h4>No travel memories yet.</h4>
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
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
