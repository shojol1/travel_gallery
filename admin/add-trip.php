<?php
require_once __DIR__ . '/includes/admin_header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $startDate   = trim($_POST['start_date'] ?? '');
    $endDate     = !empty($_POST['end_date']) ? trim($_POST['end_date']) : null;
    $country     = trim($_POST['country'] ?? 'Bangladesh');
    $division    = trim($_POST['division'] ?? '');
    $district    = trim($_POST['district'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $companions  = trim($_POST['companions'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $published   = isset($_POST['published']) ? 1 : 0;

    if (empty($title) || empty($startDate) || empty($district) || empty($location)) {
        $error = "Please fill in all required fields (Title, Start Date, District, Location).";
    } else {
        $slug = slugify($title);
        
        // Ensure unique slug
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM trips WHERE slug = ?");
        $stmtCheck->execute([$slug]);
        if ($stmtCheck->fetchColumn() > 0) {
            $slug .= '-' . time();
        }

        $coverImagePath = null;

        // Process Cover Image Upload
        if (!empty($_FILES['cover_image']['name'])) {
            $uploaded = process_image_upload($_FILES['cover_image']);
            if ($uploaded) {
                $coverImagePath = $uploaded['medium'];
            }
        }

        $stmtInsert = $pdo->prepare("INSERT INTO trips 
            (title, slug, start_date, end_date, country, division, district, location, companions, description, cover_image, featured, published) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmtInsert->execute([
            $title, $slug, $startDate, $endDate, $country, $division, $district, $location, $companions, $description, $coverImagePath, $featured, $published
        ]);

        $tripId = $pdo->lastInsertId();
        set_flash('success', 'Trip successfully created! You can now upload photos for this trip.');
        header("Location: /travel-memories/admin/photos.php?trip_id=" . $tripId);
        exit;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Add New Trip</h2>
        <p class="text-muted small mb-0">Record a new travel journey into your journal.</p>
    </div>
    <a href="/travel-memories/admin/trips.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Trips
    </a>
</div>

<div class="card border-0 shadow-sm bg-white p-4 p-md-5 max-w-900">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mb-4"><?= e($error); ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Trip Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control form-control-lg" placeholder="e.g. Cox's Bazar Coastal Expedition" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">End Date (Optional)</label>
                <input type="date" name="end_date" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Location / Landmark <span class="text-danger">*</span></label>
                <input type="text" name="location" class="form-control" placeholder="e.g. Laboni & Inani Beach" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                <input type="text" name="district" class="form-control" placeholder="e.g. Cox's Bazar" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Division</label>
                <input type="text" name="division" class="form-control" placeholder="e.g. Chittagong">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Country</label>
                <input type="text" name="country" class="form-control" value="Bangladesh">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Travel Companions</label>
                <input type="text" name="companions" class="form-control" placeholder="e.g. Friends, Family, Solo">
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Trip Description &amp; Journal Story</label>
                <textarea name="description" class="form-control" rows="5" placeholder="Write about your journey, moments captured, feelings, and highlights..."></textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Cover Image</label>
                <input type="file" name="cover_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                <div class="form-text">Allowed formats: JPG, JPEG, PNG, WebP (Max 10MB). Automatically resized &amp; optimized.</div>
            </div>

            <div class="col-md-6 mt-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="featured" id="featuredCheck">
                    <label class="form-check-label fw-semibold" for="featuredCheck">Feature on Homepage</label>
                </div>
            </div>

            <div class="col-md-6 mt-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="published" id="publishedCheck" checked>
                    <label class="form-check-label fw-semibold" for="publishedCheck">Publish Immediately</label>
                </div>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success btn-lg px-4" style="background-color: #2d5a4c; border: none;">
                    <i class="fas fa-save me-1"></i> Save Trip &amp; Proceed to Upload Photos
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
