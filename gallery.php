<?php
$pageTitle = "Global Photo Gallery";
$pageDesc  = "High resolution photography from all my travel destinations.";
require_once __DIR__ . '/includes/header.php';

// Pagination (24 photos per page)
$limit = 24;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$year = isset($_GET['year']) ? trim($_GET['year']) : '';
$district = isset($_GET['district']) ? trim($_GET['district']) : '';

$where = "WHERE t.published = 1";
$params = [];

if (!empty($year)) {
    $where .= " AND substr(t.start_date, 1, 4) = ?";
    $params[] = $year;
}
if (!empty($district)) {
    $where .= " AND t.district = ?";
    $params[] = $district;
}

// Count photos
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM photos p JOIN trips t ON p.trip_id = t.id $where");
$stmtCount->execute($params);
$totalPhotos = (int) $stmtCount->fetchColumn();
$totalPages = ceil($totalPhotos / $limit);

// Fetch paginated photos with trip details
$sql = "SELECT p.*, t.title as trip_title, t.slug as trip_slug, t.district, t.location as trip_location 
        FROM photos p 
        JOIN trips t ON p.trip_id = t.id 
        $where 
        ORDER BY t.start_date DESC, p.display_order ASC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$photos = $stmt->fetchAll();

// Fetch filter lists
$stmtYears = $pdo->query("SELECT DISTINCT substr(start_date, 1, 4) as y FROM trips WHERE published = 1 ORDER BY y DESC");
$filterYears = $stmtYears->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container py-5 mt-4">
    <div class="text-center max-w-700 mx-auto mb-5">
        <h1 class="display-4 font-serif fw-bold mb-3">Travel Photo Gallery</h1>
        <p class="text-secondary fs-5">A curated visual gallery of landscapes, seascapes, and cultural moments captured across my journeys.</p>
    </div>

    <!-- Filter Buttons -->
    <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-5">
        <a href="/travel-memories/gallery.php" class="btn btn-sm <?= empty($year) ? 'btn-primary-custom' : 'btn-outline-custom'; ?>">All Photos (<?= $totalPhotos; ?>)</a>
        <?php foreach ($filterYears as $yr): ?>
            <a href="/travel-memories/gallery.php?year=<?= $yr; ?>" class="btn btn-sm <?= ($year == $yr) ? 'btn-primary-custom' : 'btn-outline-custom'; ?>"><?= $yr; ?></a>
        <?php endforeach; ?>
    </div>

    <!-- Masonry Photo Grid -->
    <?php if (empty($photos)): ?>
        <div class="p-5 bg-white rounded-4 border text-center my-5">
            <i class="fas fa-camera-retro fa-3x text-muted mb-3"></i>
            <h4>No gallery photos found.</h4>
            <a href="/travel-memories/gallery.php" class="btn btn-primary-custom mt-3">Reset Filters</a>
        </div>
    <?php else: ?>
        <div class="masonry-grid mb-5">
            <?php foreach ($photos as $idx => $photo): 
                $medUrl   = get_image_url($photo['medium_path'] ?? $photo['image_path'], 'medium');
                $thumbUrl = get_image_url($photo['thumbnail_path'] ?? $photo['image_path'], 'thumb');
                $caption  = !empty($photo['caption']) ? $photo['caption'] : $photo['trip_title'];
            ?>
            <div class="masonry-item" 
                 data-lightbox="true" 
                 data-lightbox-group="global-gallery"
                 data-lightbox-src="<?= $medUrl; ?>"
                 data-caption="<?= e($caption); ?> &bull; <?= e($photo['trip_title']); ?> (<?= e($photo['district']); ?>)">
                <img src="<?= $thumbUrl; ?>" alt="<?= e($caption); ?>" loading="lazy">
                <div class="masonry-overlay">
                    <div>
                        <div class="badge bg-gold text-dark mb-1"><i class="fas fa-map-marker-alt me-1"></i> <?= e($photo['district']); ?></div>
                        <h6 class="mb-0 text-white fw-semibold"><?= e($photo['trip_title']); ?></h6>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <nav class="d-flex justify-content-center mt-5">
        <ul class="pagination">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= $page - 1; ?>&year=<?= e($year); ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?= $i; ?>&year=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?= $page + 1; ?>&year=<?= e($year); ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
