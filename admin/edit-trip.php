<?php
require_once __DIR__ . '/includes/admin_header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM trips WHERE id = ?");
$stmt->execute([$id]);
$trip = $stmt->fetch();

if (!$trip) {
    set_flash('danger', 'Trip not found.');
    header("Location: /travel-memories/admin/trips.php");
    exit;
}

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
        $error = "Please fill in all required fields.";
    } else {
        $coverImagePath = $trip['cover_image'];

        if (!empty($_FILES['cover_image']['name'])) {
            $uploaded = process_image_upload($_FILES['cover_image']);
            if ($uploaded) {
                $coverImagePath = $uploaded['medium'];
            }
        }

        $stmtUpdate = $pdo->prepare("UPDATE trips SET 
            title = ?, start_date = ?, end_date = ?, country = ?, division = ?, district = ?, location = ?, companions = ?, description = ?, cover_image = ?, featured = ?, published = ? 
            WHERE id = ?");
        
        $stmtUpdate->execute([
            $title, $startDate, $endDate, $country, $division, $district, $location, $companions, $description, $coverImagePath, $featured, $published, $id
        ]);

        set_flash('success', 'Trip updated successfully!');
        header("Location: /travel-memories/admin/trips.php");
        exit;
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Edit Trip: <?= e($trip['title']); ?></h2>
        <p class="text-muted small mb-0">Modify trip details and story.</p>
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
                <input type="text" name="title" class="form-control form-control-lg" value="<?= e($trip['title']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control" value="<?= e($trip['start_date']); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">End Date (Optional)</label>
                <input type="date" name="end_date" class="form-control" value="<?= e($trip['end_date']); ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                <input type="text" name="location" class="form-control" value="<?= e($trip['location']); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">District <span class="text-danger">*</span></label>
                <input type="text" name="district" class="form-control" value="<?= e($trip['district']); ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Division</label>
                <input type="text" name="division" class="form-control" value="<?= e($trip['division']); ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Country</label>
                <input type="text" name="country" class="form-control" value="<?= e($trip['country']); ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Travel Companions</label>
                <input type="text" name="companions" class="form-control" value="<?= e($trip['companions']); ?>">
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Trip Description &amp; Journal Story</label>
                <textarea name="description" class="form-control" rows="5"><?= e($trip['description']); ?></textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Cover Image</label>
                <?php if (!empty($trip['cover_image'])): ?>
                    <div class="mb-2">
                        <img src="/travel-memories/<?= e($trip['cover_image']); ?>" style="height: 100px; border-radius: 6px; object-fit: cover;">
                    </div>
                <?php endif; ?>
                <input type="file" name="cover_image" class="form-control" accept="image/jpeg,image/png,image/webp">
            </div>

            <div class="col-md-6 mt-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="featured" id="featuredCheck" <?= $trip['featured'] ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-semibold" for="featuredCheck">Feature on Homepage</label>
                </div>
            </div>

            <div class="col-md-6 mt-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="published" id="publishedCheck" <?= $trip['published'] ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-semibold" for="publishedCheck">Published</label>
                </div>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success btn-lg px-4" style="background-color: #2d5a4c; border: none;">
                    <i class="fas fa-save me-1"></i> Update Trip Details
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
