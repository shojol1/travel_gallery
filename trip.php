<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT * FROM trips WHERE slug = ? AND published = 1");
    $stmt->execute([$slug]);
} else if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM trips WHERE id = ? AND published = 1");
    $stmt->execute([$id]);
} else {
    header("Location: /travel-memories/trips.php");
    exit;
}

$trip = $stmt->fetch();

if (!$trip) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

$pageTitle = $trip['title'];
$pageDesc  = substr(strip_tags($trip['description']), 0, 160);
require_once __DIR__ . '/includes/header.php';

// Fetch all photos for this trip
$stmtPhotos = $pdo->prepare("SELECT * FROM photos WHERE trip_id = ? ORDER BY display_order ASC, id ASC");
$stmtPhotos->execute([$trip['id']]);
$photos = $stmtPhotos->fetchAll();

$coverImg = get_image_url($trip['cover_image'], 'medium');
$duration = calculate_duration($trip['start_date'], $trip['end_date']);
$dateFormatted = format_date($trip['start_date'], 'd M Y') . ($trip['end_date'] ? ' &mdash; ' . format_date($trip['end_date'], 'd M Y') : '');
?>

<!-- Trip Hero Banner -->
<section class="hero-section" style="height: 60vh; min-height: 450px;">
    <img src="<?= $coverImg; ?>" alt="<?= e($trip['title']); ?>" class="hero-bg">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="badge bg-gold text-dark px-3 py-2 rounded-pill fw-bold text-uppercase tracking-wider mb-3">
            <i class="fas fa-map-marker-alt me-1"></i> <?= e($trip['location']); ?>
        </span>
        <h1 class="hero-title"><?= e($trip['title']); ?></h1>
        <p class="hero-subtitle mb-0"><i class="far fa-calendar-alt me-2"></i> <?= $dateFormatted; ?></p>
    </div>
</section>

<div class="container py-5">
    <div class="row g-5">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm mb-5">
                <h3 class="font-serif fw-bold mb-4">About This Trip</h3>
                <div class="fs-5 text-secondary leading-relaxed">
                    <?= nl2br(e($trip['description'])); ?>
                </div>
            </div>

            <!-- Photo Gallery Masonry -->
            <div class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h3 class="font-serif fw-bold mb-0">Photo Gallery (<?= count($photos); ?>)</h3>
                    <span class="small text-muted"><i class="fas fa-info-circle me-1"></i> Click any photo for Lightbox view</span>
                </div>

                <?php if (empty($photos)): ?>
                    <div class="p-4 bg-light text-center rounded-3 border">
                        <i class="fas fa-images fa-2x text-muted mb-2"></i>
                        <p class="mb-0 text-secondary">No gallery photos added yet for this trip.</p>
                    </div>
                <?php else: ?>
                    <div class="masonry-grid">
                        <?php foreach ($photos as $idx => $photo): 
                            $medUrl   = get_image_url($photo['medium_path'] ?? $photo['image_path'], 'medium');
                            $thumbUrl = get_image_url($photo['thumbnail_path'] ?? $photo['image_path'], 'thumb');
                            $caption  = !empty($photo['caption']) ? $photo['caption'] : $trip['title'] . ' Photo ' . ($idx + 1);
                        ?>
                        <div class="masonry-item" 
                             data-lightbox="true" 
                             data-lightbox-group="trip-<?= $trip['id']; ?>"
                             data-lightbox-src="<?= $medUrl; ?>"
                             data-caption="<?= e($caption); ?>">
                            <img src="<?= $thumbUrl; ?>" alt="<?= e($caption); ?>" loading="lazy">
                            <div class="masonry-overlay">
                                <div>
                                    <div class="small text-warning"><i class="fas fa-expand me-1"></i> View Fullscreen</div>
                                    <div class="fw-semibold text-truncate max-w-200"><?= e($caption); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="bg-white p-4 rounded-4 border shadow-sm sticky-top" style="top: 100px;">
                <h4 class="font-serif fw-bold mb-4 pb-2 border-bottom">Travel Information</h4>
                
                <div class="mb-3 d-flex align-items-center gap-3">
                    <div class="bg-light p-3 rounded-circle text-success"><i class="fas fa-calendar-check fa-lg"></i></div>
                    <div>
                        <div class="small text-muted text-uppercase">Dates</div>
                        <div class="fw-semibold"><?= $dateFormatted; ?></div>
                    </div>
                </div>

                <div class="mb-3 d-flex align-items-center gap-3">
                    <div class="bg-light p-3 rounded-circle text-primary"><i class="fas fa-clock fa-lg"></i></div>
                    <div>
                        <div class="small text-muted text-uppercase">Duration</div>
                        <div class="fw-semibold"><?= $duration; ?></div>
                    </div>
                </div>

                <div class="mb-3 d-flex align-items-center gap-3">
                    <div class="bg-light p-3 rounded-circle text-danger"><i class="fas fa-map-marked-alt fa-lg"></i></div>
                    <div>
                        <div class="small text-muted text-uppercase">Location &amp; District</div>
                        <div class="fw-semibold"><?= e($trip['location']); ?>, <?= e($trip['district']); ?></div>
                        <div class="small text-muted"><?= e($trip['division']); ?>, <?= e($trip['country']); ?></div>
                    </div>
                </div>

                <?php if (!empty($trip['companions'])): ?>
                <div class="mb-4 d-flex align-items-center gap-3">
                    <div class="bg-light p-3 rounded-circle text-warning"><i class="fas fa-users fa-lg"></i></div>
                    <div>
                        <div class="small text-muted text-uppercase">Companions</div>
                        <div class="fw-semibold"><?= e($trip['companions']); ?></div>
                    </div>
                </div>
                <?php endif; ?>

                <a href="/travel-memories/trips.php" class="btn btn-outline-custom w-100 text-center">
                    <i class="fas fa-arrow-left me-2"></i> Back to All Trips
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
