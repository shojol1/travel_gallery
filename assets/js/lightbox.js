/**
 * Professional Ultra-Premium Lightbox Image Viewer
 * Features: Smooth Animated Zoom In/Out, 360 Rotate, Drag/Pan when zoomed, Mousewheel zoom, Fullscreen, Touch Swipe
 * Travel Memories Website
 */

class TravelLightbox {
    constructor() {
        this.images = [];
        this.currentIndex = 0;
        this.zoomLevel = 1;
        this.rotation = 0;
        this.panX = 0;
        this.panY = 0;
        this.isDragging = false;
        this.startX = 0;
        this.startY = 0;
        this.modal = null;
        this.imgEl = null;

        this.initDOM();
        this.bindEvents();
    }

    initDOM() {
        if (document.getElementById('travel-lightbox')) return;

        const modalHtml = `
            <div id="travel-lightbox" class="lightbox-modal">
                <!-- Top Toolbar -->
                <div class="lightbox-toolbar">
                    <div class="lightbox-counter" id="lb-counter">1 / 1</div>
                    <div class="lightbox-actions">
                        <button class="lb-tool-btn" id="lb-rotate-left" title="Rotate Left (90°)"><i class="fas fa-undo"></i></button>
                        <button class="lb-tool-btn" id="lb-rotate-right" title="Rotate Right (90°)"><i class="fas fa-redo"></i></button>
                        <span class="lb-tool-divider"></span>
                        <button class="lb-tool-btn" id="lb-zoom-out" title="Zoom Out (-)"><i class="fas fa-search-minus"></i></button>
                        <span class="lb-zoom-level" id="lb-zoom-indicator">100%</span>
                        <button class="lb-tool-btn" id="lb-zoom-in" title="Zoom In (+)"><i class="fas fa-search-plus"></i></button>
                        <button class="lb-tool-btn" id="lb-zoom-reset" title="Reset Zoom"><i class="fas fa-sync-alt"></i></button>
                        <span class="lb-tool-divider"></span>
                        <button class="lb-tool-btn" id="lb-fullscreen" title="Toggle Fullscreen"><i class="fas fa-expand"></i></button>
                        <button class="lb-tool-btn lb-btn-close" id="lb-close" title="Close (Esc)"><i class="fas fa-times"></i></button>
                    </div>
                </div>

                <!-- Navigation Arrows -->
                <button class="lightbox-btn lightbox-prev" id="lb-prev" title="Previous (Left Arrow)"><i class="fas fa-chevron-left"></i></button>
                <button class="lightbox-btn lightbox-next" id="lb-next" title="Next (Right Arrow)"><i class="fas fa-chevron-right"></i></button>
                
                <!-- Main Stage -->
                <div class="lightbox-stage" id="lb-stage">
                    <div class="lightbox-img-container" id="lb-img-container">
                        <img id="lb-image" src="" alt="Gallery Preview" draggable="false">
                    </div>
                </div>

                <!-- Bottom Caption -->
                <div class="lightbox-bottom-bar">
                    <div class="lightbox-caption" id="lb-caption"></div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        this.modal = document.getElementById('travel-lightbox');
        this.imgEl = document.getElementById('lb-image');
    }

    bindEvents() {
        // Trigger click event for elements with data-lightbox attribute
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-lightbox]');
            if (trigger) {
                e.preventDefault();
                this.openFromTrigger(trigger);
            }
        });

        // Toolbar Button Events
        document.getElementById('lb-close')?.addEventListener('click', () => this.close());
        document.getElementById('lb-prev')?.addEventListener('click', () => this.prev());
        document.getElementById('lb-next')?.addEventListener('click', () => this.next());
        document.getElementById('lb-zoom-in')?.addEventListener('click', () => this.zoomIn());
        document.getElementById('lb-zoom-out')?.addEventListener('click', () => this.zoomOut());
        document.getElementById('lb-zoom-reset')?.addEventListener('click', () => this.resetTransform());
        document.getElementById('lb-rotate-left')?.addEventListener('click', () => this.rotateLeft());
        document.getElementById('lb-rotate-right')?.addEventListener('click', () => this.rotateRight());
        document.getElementById('lb-fullscreen')?.addEventListener('click', () => this.toggleFullscreen());

        // Double Click to Toggle Zoom
        this.imgEl?.addEventListener('dblclick', () => {
            if (this.zoomLevel === 1) {
                this.setZoom(2);
            } else {
                this.resetTransform();
            }
        });

        // Mousewheel Smooth Zooming
        const stage = document.getElementById('lb-stage');
        stage?.addEventListener('wheel', (e) => {
            e.preventDefault();
            if (e.deltaY < 0) {
                this.zoomIn(0.2);
            } else {
                this.zoomOut(0.2);
            }
        }, { passive: false });

        // Drag to Pan when Zoomed in
        stage?.addEventListener('mousedown', (e) => {
            if (this.zoomLevel > 1 && e.target === this.imgEl) {
                this.isDragging = true;
                this.startX = e.clientX - this.panX;
                this.startY = e.clientY - this.panY;
                stage.style.cursor = 'grabbing';
            }
        });

        window.addEventListener('mousemove', (e) => {
            if (this.isDragging) {
                this.panX = e.clientX - this.startX;
                this.panY = e.clientY - this.startY;
                this.applyTransform(false); // No transition while dragging for instant feedback
            }
        });

        window.addEventListener('mouseup', () => {
            if (this.isDragging) {
                this.isDragging = false;
                if (stage) stage.style.cursor = this.zoomLevel > 1 ? 'grab' : 'default';
            }
        });

        // Close on clicking backdrop
        this.modal?.addEventListener('click', (e) => {
            if (e.target === this.modal || e.target === stage) {
                this.close();
            }
        });

        // Keyboard Shortcuts
        document.addEventListener('keydown', (e) => {
            if (!this.modal || !this.modal.classList.contains('active')) return;
            switch (e.key) {
                case 'Escape': this.close(); break;
                case 'ArrowLeft': this.prev(); break;
                case 'ArrowRight': this.next(); break;
                case '+': case '=': this.zoomIn(); break;
                case '-': case '_': this.zoomOut(); break;
                case 'r': case 'R': this.rotateRight(); break;
                case '0': this.resetTransform(); break;
            }
        });

        // Touch Swipe & Pinch Support for Mobile
        let touchStartX = 0;
        let touchEndX = 0;

        stage?.addEventListener('touchstart', (e) => {
            if (e.touches.length === 1) {
                touchStartX = e.touches[0].screenX;
            }
        }, { passive: true });

        stage?.addEventListener('touchend', (e) => {
            if (e.changedTouches.length === 1 && this.zoomLevel === 1) {
                touchEndX = e.changedTouches[0].screenX;
                if (touchStartX - touchEndX > 50) this.next();
                if (touchEndX - touchStartX > 50) this.prev();
            }
        }, { passive: true });
    }

    openFromTrigger(trigger) {
        const group = trigger.getAttribute('data-lightbox-group') || 'default';
        const groupElements = Array.from(document.querySelectorAll(`[data-lightbox-group="${group}"]`));

        this.images = groupElements.map(el => ({
            src: el.getAttribute('data-lightbox-src') || el.getAttribute('href') || el.src,
            caption: el.getAttribute('data-caption') || el.getAttribute('alt') || '',
        }));

        this.currentIndex = groupElements.indexOf(trigger);
        if (this.currentIndex < 0) this.currentIndex = 0;

        this.resetTransform();
        this.render();
        this.modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    render() {
        if (!this.images.length) return;
        const current = this.images[this.currentIndex];
        
        const capEl = document.getElementById('lb-caption');
        const countEl = document.getElementById('lb-counter');

        this.resetTransform();

        // Smooth image swap transition
        this.imgEl.style.opacity = '0';
        setTimeout(() => {
            this.imgEl.src = current.src;
            this.imgEl.style.opacity = '1';
        }, 150);

        capEl.textContent = current.caption;
        countEl.textContent = `${this.currentIndex + 1} / ${this.images.length}`;
    }

    // Zoom Methods with Animated Transitions
    zoomIn(step = 0.3) {
        this.setZoom(Math.min(this.zoomLevel + step, 4.0));
    }

    zoomOut(step = 0.3) {
        this.setZoom(Math.max(this.zoomLevel - step, 0.5));
    }

    setZoom(level) {
        this.zoomLevel = Math.round(level * 10) / 10;
        if (this.zoomLevel === 1) {
            this.panX = 0;
            this.panY = 0;
        }
        this.applyTransform(true);
        this.updateZoomIndicator();
    }

    // Rotation Methods
    rotateLeft() {
        this.rotation = (this.rotation - 90) % 360;
        this.applyTransform(true);
    }

    rotateRight() {
        this.rotation = (this.rotation + 90) % 360;
        this.applyTransform(true);
    }

    // Reset Transformation
    resetTransform() {
        this.zoomLevel = 1;
        this.rotation = 0;
        this.panX = 0;
        this.panY = 0;
        this.applyTransform(true);
        this.updateZoomIndicator();
        const stage = document.getElementById('lb-stage');
        if (stage) stage.style.cursor = 'default';
    }

    // Apply Transformation Matrix with CSS Transition
    applyTransform(animate = true) {
        if (!this.imgEl) return;
        
        if (animate) {
            this.imgEl.style.transition = 'transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease';
        } else {
            this.imgEl.style.transition = 'none';
        }

        this.imgEl.style.transform = `translate(${this.panX}px, ${this.panY}px) scale(${this.zoomLevel}) rotate(${this.rotation}deg)`;

        const stage = document.getElementById('lb-stage');
        if (stage) {
            stage.style.cursor = this.zoomLevel > 1 ? (this.isDragging ? 'grabbing' : 'grab') : 'default';
        }
    }

    updateZoomIndicator() {
        const indicator = document.getElementById('lb-zoom-indicator');
        if (indicator) {
            indicator.textContent = `${Math.round(this.zoomLevel * 100)}%`;
        }
    }

    toggleFullscreen() {
        if (!document.fullscreenElement) {
            this.modal.requestFullscreen?.() || this.modal.webkitRequestFullscreen?.();
        } else {
            document.exitFullscreen?.() || document.webkitExitFullscreen?.();
        }
    }

    next() {
        if (!this.images.length) return;
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.render();
    }

    prev() {
        if (!this.images.length) return;
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.render();
    }

    close() {
        this.modal.classList.remove('active');
        document.body.style.overflow = '';
        if (document.fullscreenElement) {
            document.exitFullscreen?.();
        }
        this.resetTransform();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.travelLightbox = new TravelLightbox();
});
