<footer class="footer-custom">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-lg-5">
                <a href="<?= BASE_URL; ?>/index.php" class="footer-brand d-inline-block mb-3">
                    MY <span style="color: var(--color-gold);">JOURNEY</span>
                </a>
                <p class="text-secondary pe-lg-4">
                    <?= e(get_site_setting('author_bio', 'A personal collection of travel memories, captured landscapes, and timeless moments across Bangladesh and beyond.')); ?>
                </p>
            </div>
            
            <div class="col-6 col-lg-3">
                <h6 class="text-white text-uppercase tracking-wider mb-3 font-serif">Quick Navigation</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= BASE_URL; ?>/index.php" class="text-secondary text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL; ?>/trips.php" class="text-secondary text-decoration-none">Travel Experiences</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL; ?>/gallery.php" class="text-secondary text-decoration-none">Photo Gallery</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL; ?>/timeline.php" class="text-secondary text-decoration-none">Chronological Timeline</a></li>
                    <li class="mb-2"><a href="<?= BASE_URL; ?>/about.php" class="text-secondary text-decoration-none">About Journal</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h6 class="text-white text-uppercase tracking-wider mb-3 font-serif">Explore Regions</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= BASE_URL; ?>/search.php?q=Cox%27s+Bazar" class="badge bg-dark border border-secondary text-secondary p-2">Cox's Bazar</a>
                    <a href="<?= BASE_URL; ?>/search.php?q=Bandarban" class="badge bg-dark border border-secondary text-secondary p-2">Bandarban</a>
                    <a href="<?= BASE_URL; ?>/search.php?q=Sylhet" class="badge bg-dark border border-secondary text-secondary p-2">Sylhet</a>
                    <a href="<?= BASE_URL; ?>/search.php?q=Rajshahi" class="badge bg-dark border border-secondary text-secondary p-2">Rajshahi</a>
                    <a href="<?= BASE_URL; ?>/search.php?q=Sajek" class="badge bg-dark border border-secondary text-secondary p-2">Sajek</a>
                </div>
            </div>
        </div>

        <hr class="border-secondary opacity-25">

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center py-3">
            <p class="small mb-0 text-secondary">&copy; <?= date('Y'); ?> <?= e(get_site_setting('site_title', 'MY TRAVEL MEMORIES')); ?>. All rights reserved.</p>
            <p class="small mb-0 text-secondary">Powered by PHP &amp; MySQL</p>
        </div>
    </div>
</footer>

<!-- JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL; ?>/assets/js/lightbox.js"></script>
<script src="<?= BASE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
