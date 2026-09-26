<?php
$pageTitle = "Explore by Location";
$pageDesc  = "Explore travel memories grouped by location and district.";
require_once __DIR__ . '/includes/header.php';

// Group trips by District
$stmt = $pdo->query("SELECT district, COUNT(*) as trip_count, MAX(cover_image) as sample_cover 
                    FROM trips 
                    WHERE published = 1 
                    GROUP BY district 
                    ORDER BY trip_count DESC");
$locations = $stmt->fetchAll();
?>

<div class="container py-5 mt-4">
    <div class="text-center max-w-700 mx-auto mb-5">
        <h1 class="display-4 font-serif fw-bold mb-3">Explore by Location</h1>
        <p class="text-secondary fs-5">Browse trips and galleries categorized by district and region.</p>
    </div>

    <div class="row g-4">
        <?php foreach ($locations as $loc): 
            $coverImg = get_image_url($loc['sample_cover'], 'medium');
        ?>
        <div class="col-md-6 col-lg-3">
            <div class="travel-card text-center p-3">
                <div class="card-img-wrapper mb-3 rounded-3">
                    <img src="<?= $coverImg; ?>" alt="<?= e($loc['district']); ?>">
                </div>
                <h4 class="font-serif fw-bold mb-1"><?= e($loc['district']); ?></h4>
                <p class="text-muted small mb-3"><?= $loc['trip_count']; ?> <?= ($loc['trip_count'] > 1 ? 'Trips' : 'Trip'); ?></p>
                <a href="/travel-memories/trips.php?district=<?= urlencode($loc['district']); ?>" class="btn btn-sm btn-outline-custom w-100">
                    Explore Region <i class="fas fa-compass ms-1"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
