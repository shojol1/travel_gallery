<?php
$pageTitle = "404 Page Not Found";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 my-5 text-center">
    <div class="max-w-600 mx-auto bg-white p-5 rounded-4 border shadow-sm">
        <h1 class="display-1 font-serif fw-bold text-success mb-2">404</h1>
        <h3 class="font-serif fw-bold mb-3">Memory Not Found</h3>
        <p class="text-secondary mb-4">The trip journal or page you were looking for could not be found or may have been moved.</p>
        <a href="/travel-memories/index.php" class="btn btn-primary-custom">
            <i class="fas fa-home me-2"></i> Return to Homepage
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
