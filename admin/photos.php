<?php
require_once __DIR__ . '/includes/admin_header.php';

// Fetch all trips for dropdown selector
$stmtTrips = $pdo->query("SELECT id, title, district FROM trips ORDER BY start_date DESC");
$allTrips = $stmtTrips->fetchAll();

$selectedTripId = isset($_GET['trip_id']) ? (int)$_GET['trip_id'] : ($allTrips[0]['id'] ?? 0);

// Update captions if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_captions'])) {
    if (!empty($_POST['captions']) && is_array($_POST['captions'])) {
        $stmtUp = $pdo->prepare("UPDATE photos SET caption = ? WHERE id = ?");
        foreach ($_POST['captions'] as $pId => $cap) {
            $stmtUp->execute([trim($cap), (int)$pId]);
        }
        set_flash('success', 'Photo captions updated successfully!');
    }
}

// Fetch photos for selected trip
$photos = [];
if ($selectedTripId > 0) {
    $stmtPhotos = $pdo->prepare("SELECT * FROM photos WHERE trip_id = ? ORDER BY display_order ASC, id DESC");
    $stmtPhotos->execute([$selectedTripId]);
    $photos = $stmtPhotos->fetchAll();
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Photo Gallery Manager</h2>
        <p class="text-muted small mb-0">Drag and drop multiple photos to batch upload into any trip.</p>
    </div>

    <!-- Trip Selection Dropdown -->
    <div class="mt-3 mt-md-0">
        <form action="" method="GET" class="d-flex align-items-center gap-2">
            <label class="small fw-bold text-nowrap">Select Trip:</label>
            <select name="trip_id" class="form-select" onchange="this.form.submit()">
                <?php foreach ($allTrips as $at): ?>
                    <option value="<?= $at['id']; ?>" <?= ($at['id'] == $selectedTripId) ? 'selected' : ''; ?>>
                        <?= e($at['title']); ?> (<?= e($at['district']); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<?php if ($selectedTripId <= 0): ?>
    <div class="alert alert-warning">Please create a trip first before uploading photos.</div>
<?php else: ?>

<!-- Drag & Drop Upload Zone -->
<div class="card border-0 shadow-sm bg-white p-4 mb-4">
    <h5 class="fw-bold mb-3"><i class="fas fa-cloud-upload-alt text-success me-2"></i> Batch Drag &amp; Drop Photo Uploader</h5>
    
    <div id="drop-zone" class="p-5 text-center rounded-3 border-2 border-dashed bg-light cursor-pointer">
        <i class="fas fa-images fa-3x text-muted mb-3"></i>
        <h5>Drag &amp; Drop Images Here</h5>
        <p class="text-muted small mb-3">or click to select multiple files from your computer (JPG, PNG, WebP up to 10MB each)</p>
        <input type="file" id="file-input" multiple accept="image/jpeg,image/png,image/webp" class="d-none">
        <button type="button" class="btn btn-outline-dark" onclick="document.getElementById('file-input').click()">
            <i class="fas fa-folder-open me-1"></i> Browse Files
        </button>
    </div>

    <!-- Upload Progress Bar -->
    <div id="upload-progress-container" class="mt-3 d-none">
        <div class="d-flex justify-content-between small text-muted mb-1">
            <span id="upload-status-text">Uploading photos...</span>
            <span id="upload-percent">0%</span>
        </div>
        <div class="progress" style="height: 10px;">
            <div id="upload-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 0%"></div>
        </div>
    </div>
</div>

<!-- Existing Photos Grid & Captions Form -->
<div class="card border-0 shadow-sm bg-white p-4">
    <form action="" method="POST">
        <input type="hidden" name="update_captions" value="1">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Uploaded Trip Photos (<?= count($photos); ?>)</h5>
            <?php if (!empty($photos)): ?>
                <button type="submit" class="btn btn-sm btn-primary-custom" style="background-color: #2d5a4c; color: white;">
                    <i class="fas fa-save me-1"></i> Save Captions
                </button>
            <?php endif; ?>
        </div>

        <div class="row g-3" id="photo-cards-container">
            <?php if (empty($photos)): ?>
                <div class="col-12 text-center text-muted py-5" id="no-photos-msg">
                    <i class="fas fa-camera fa-2x mb-2 opacity-50"></i>
                    <p class="mb-0">No photos uploaded for this trip yet. Use the uploader above to add photos.</p>
                </div>
            <?php else: ?>
                <?php foreach ($photos as $p): 
                    $thumb = !empty($p['thumbnail_path']) ? '/travel-memories/' . e($p['thumbnail_path']) : '/travel-memories/' . e($p['image_path']);
                ?>
                <div class="col-6 col-md-4 col-lg-3 position-relative">
                    <div class="border rounded p-2 bg-light">
                        <div class="rounded overflow-hidden mb-2" style="height: 160px;">
                            <img src="<?= $thumb; ?>" class="w-100 h-100 object-fit-cover" alt="Photo">
                        </div>
                        <input type="text" name="captions[<?= $p['id']; ?>]" value="<?= e($p['caption']); ?>" class="form-control form-control-sm mb-2" placeholder="Photo Caption">
                        <a href="/travel-memories/admin/delete-photo.php?id=<?= $p['id']; ?>&trip_id=<?= $selectedTripId; ?>" 
                           class="btn btn-sm btn-outline-danger w-100" 
                           onclick="return confirm('Delete this photo?');">
                            <i class="fas fa-trash me-1"></i> Delete
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </form>
</div>

<style>
    .border-dashed { border-style: dashed !important; border-color: #ced4da; }
    .border-dashed.dragover { border-color: #2d5a4c !important; background-color: #e8f5e9 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const progressContainer = document.getElementById('upload-progress-container');
    const progressBar = document.getElementById('upload-progress-bar');
    const percentText = document.getElementById('upload-percent');
    const statusText = document.getElementById('upload-status-text');
    const tripId = <?= $selectedTripId; ?>;

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
        });
    });

    dropZone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        if (files.length) uploadFiles(files);
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) uploadFiles(fileInput.files);
    });

    function uploadFiles(files) {
        progressContainer.classList.remove('d-none');
        let uploadedCount = 0;
        const totalFiles = files.length;

        Array.from(files).forEach((file, index) => {
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('trip_id', tripId);

            fetch('/travel-memories/admin/upload-photos.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                uploadedCount++;
                const pct = Math.round((uploadedCount / totalFiles) * 100);
                progressBar.style.width = pct + '%';
                percentText.textContent = pct + '%';
                statusText.textContent = `Uploaded ${uploadedCount} of ${totalFiles} photos...`;

                if (uploadedCount === totalFiles) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                }
            })
            .catch(err => {
                console.error('Upload failed:', err);
                uploadedCount++;
            });
        });
    }
});
</script>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
