<div class="modal fade" id="galleryLightbox" tabindex="-1" aria-labelledby="galleryLightboxTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 py-2">
                <h2 class="h6 mb-0 text-white" id="galleryLightboxTitle">गैलरी</h2>
                <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="बंद करें"></button>
            </div>
            <div class="modal-body position-relative text-center p-0">
                <button class="btn gallery-lightbox-nav gallery-lightbox-prev" type="button" id="galleryLightboxPrev" aria-label="पिछली छवि">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <img class="img-fluid" id="galleryLightboxImage" src="" alt="">
                <button class="btn gallery-lightbox-nav gallery-lightbox-next" type="button" id="galleryLightboxNext" aria-label="अगली छवि">
                    <i class="bi bi-chevron-right"></i>
                </button>
                <div class="gallery-lightbox-counter">
                    <i class="bi bi-mouse me-1"></i><span id="galleryLightboxCounter"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalElement = document.getElementById('galleryLightbox');
        if (!modalElement || typeof bootstrap === 'undefined') return;

        var modal = new bootstrap.Modal(modalElement);
        var image = document.getElementById('galleryLightboxImage');
        var title = document.getElementById('galleryLightboxTitle');
        var counter = document.getElementById('galleryLightboxCounter');
        var prevButton = document.getElementById('galleryLightboxPrev');
        var nextButton = document.getElementById('galleryLightboxNext');
        var items = [];
        var index = 0;

        function render() {
            var item = items[index];
            if (!item) return;

            image.src = item.href;
            image.alt = item.dataset.title || 'गैलरी छवि';
            title.textContent = item.dataset.title || 'गैलरी';
            counter.textContent = (index + 1) + ' / ' + items.length;
            prevButton.disabled = items.length < 2;
            nextButton.disabled = items.length < 2;
        }

        function go(step) {
            if (items.length < 2) return;
            index = (index + step + items.length) % items.length;
            render();
        }

        document.querySelectorAll('[data-gallery]').forEach(function (group) {
            group.querySelectorAll('.js-gallery-item').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    items = Array.prototype.slice.call(group.querySelectorAll('.js-gallery-item'));
                    index = items.indexOf(link);
                    if (index < 0) index = 0;
                    render();
                    modal.show();
                });
            });
        });

        prevButton.addEventListener('click', function () { go(-1); });
        nextButton.addEventListener('click', function () { go(1); });

        document.addEventListener('keydown', function (event) {
            if (!modalElement.classList.contains('show')) return;
            if (event.key === 'ArrowLeft') go(-1);
            if (event.key === 'ArrowRight') go(1);
        });

        var lastWheel = 0;
        modalElement.addEventListener('wheel', function (event) {
            if (!modalElement.classList.contains('show')) return;
            var now = Date.now();
            if (now - lastWheel < 350 || Math.abs(event.deltaY) < 8) return;
            lastWheel = now;
            event.preventDefault();
            go(event.deltaY > 0 ? 1 : -1);
        }, { passive: false });

        var touchStartX = 0;
        modalElement.addEventListener('touchstart', function (event) {
            touchStartX = event.changedTouches[0].screenX;
        }, { passive: true });
        modalElement.addEventListener('touchend', function (event) {
            var delta = event.changedTouches[0].screenX - touchStartX;
            if (Math.abs(delta) > 40) go(delta > 0 ? -1 : 1);
        }, { passive: true });

    });
</script>
