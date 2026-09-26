<?php
require_once __DIR__ . '/includes/admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_title'       => trim($_POST['site_title'] ?? ''),
        'site_subtitle'    => trim($_POST['site_subtitle'] ?? ''),
        'author_name'      => trim($_POST['author_name'] ?? ''),
        'author_bio'       => trim($_POST['author_bio'] ?? ''),
        'hero_button_text' => trim($_POST['hero_button_text'] ?? '')
    ];

    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

    foreach ($settings as $k => $v) {
        $stmt->execute([$k, $v]);
    }

    set_flash('success', 'Website settings updated successfully!');
    header("Location: /travel-memories/admin/settings.php");
    exit;
}

$title    = get_site_setting('site_title', 'MY TRAVEL MEMORIES');
$subtitle = get_site_setting('site_subtitle', 'Places I\'ve Been, Moments I\'ve Captured.');
$name     = get_site_setting('author_name', 'Travel Explorer');
$bio      = get_site_setting('author_bio', 'Welcome to my travel journal.');
$btnText  = get_site_setting('hero_button_text', 'Explore My Journey');
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Website Settings</h2>
        <p class="text-muted small mb-0">Configure site branding, hero text, and author biography.</p>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white p-4 p-md-5 max-w-800">
    <form action="" method="POST">
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Website Title</label>
                <input type="text" name="site_title" class="form-control" value="<?= e($title); ?>" required>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Hero Subtitle</label>
                <input type="text" name="site_subtitle" class="form-control" value="<?= e($subtitle); ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Hero Button Text</label>
                <input type="text" name="hero_button_text" class="form-control" value="<?= e($btnText); ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Author / Traveler Name</label>
                <input type="text" name="author_name" class="form-control" value="<?= e($name); ?>">
            </div>

            <div class="col-md-12">
                <label class="form-label fw-semibold">Author Bio (Displayed on About &amp; Footer)</label>
                <textarea name="author_bio" class="form-control" rows="4"><?= e($bio); ?></textarea>
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-success px-4" style="background-color: #2d5a4c; border: none;">
                    <i class="fas fa-save me-1"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
