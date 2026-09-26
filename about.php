<?php
$pageTitle = "About My Journey";
$pageDesc  = "Learn about my travel philosophy and passion for photography.";
require_once __DIR__ . '/includes/header.php';

$stats = get_trip_stats();
$authorName = get_site_setting('author_name', 'Travel Explorer');
$authorBio  = get_site_setting('author_bio', 'Welcome to my travel journal. Here I document my journeys across Bangladesh and beyond, capturing timeless landscapes, hidden trails, and unforgettable cultural experiences.');
?>

<div class="container py-5 mt-4">
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <div class="pe-lg-4">
                <span class="badge bg-gold text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">Personal Travel Journal</span>
                <h1 class="display-4 font-serif fw-bold mb-4">About My Travel Journal</h1>
                <p class="fs-5 text-secondary leading-relaxed mb-4">
                    <?= nl2br(e($authorBio)); ?>
                </p>
                <p class="text-secondary mb-4">
                    Traveling to me is not just about visiting famous landmarks—it is about connecting with people, feeling the tranquility of nature, and capturing genuine moments through photography. Every photo tells a story, and every trip becomes a chapter in my life's journal.
                </p>
                <div class="d-flex gap-3">
                    <a href="/travel-memories/trips.php" class="btn btn-primary-custom">Explore Trips</a>
                    <a href="/travel-memories/gallery.php" class="btn btn-outline-custom">View Gallery</a>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="bg-white p-4 p-md-5 rounded-4 border shadow-sm text-center">
                <div class="mb-4">
                    <img src="/travel-memories/uploads/trips/coxsbazar_cover.jpg" alt="<?= e($authorName); ?>" class="rounded-circle shadow" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--color-gold);">
                </div>
                <h3 class="font-serif fw-bold mb-1"><?= e($authorName); ?></h3>
                <p class="text-muted mb-4">Landscape &amp; Travel Enthusiast</p>
                
                <div class="row g-3 border-top pt-4">
                    <div class="col-6">
                        <div class="h3 font-serif fw-bold text-success mb-0"><?= $stats['trips']; ?>+</div>
                        <div class="small text-muted text-uppercase">Trips</div>
                    </div>
                    <div class="col-6">
                        <div class="h3 font-serif fw-bold text-success mb-0"><?= $stats['photos']; ?>+</div>
                        <div class="small text-muted text-uppercase">Photos</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
