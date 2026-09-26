<?php
$pageTitle = "Explore by Year";
$pageDesc  = "Browse travel journeys grouped by year.";
require_once __DIR__ . '/includes/header.php';

// Group trips by Year
$stmt = $pdo->query("SELECT YEAR(start_date) as yr, COUNT(*) as trip_count, MAX(cover_image) as sample_cover 
                    FROM trips 
                    WHERE published = 1 
                    GROUP BY yr 
                    ORDER BY yr DESC");
$years = $stmt->fetchAll();
?>

<div class="container py-5 mt-4">
    <div class="text-center max-w-700 mx-auto mb-5">
        <h1 class="display-4 font-serif fw-bold mb-3">Explore by Year</h1>
        <p class="text-secondary fs-5">Step through the years and relive past journeys and travel stories.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <?php foreach ($years as $y): 
            $coverImg = get_image_url($y['sample_cover'], 'medium');
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="travel-card text-center p-4">
                <div class="card-img-wrapper mb-3 rounded-3" style="padding-top: 50%;">
                    <img src="<?= $coverImg; ?>" alt="<?= $y['yr']; ?>">
                </div>
                <h2 class="display-5 font-serif fw-bold text-success mb-1"><?= $y['yr']; ?></h2>
                <p class="text-secondary mb-4"><?= $y['trip_count']; ?> <?= ($y['trip_count'] > 1 ? 'Trips Recorded' : 'Trip Recorded'); ?></p>
                <a href="/travel-memories/trips.php?year=<?= $y['yr']; ?>" class="btn btn-primary-custom w-100">
                    View <?= $y['yr']; ?> Journeys <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
