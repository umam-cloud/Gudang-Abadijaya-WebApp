        </main>
    </div>
    <!-- Global Confirm Modal -->
    <div id="globalConfirmModal" class="modal-overlay fixed inset-0 z-[100] hidden opacity-0 transition-opacity duration-300 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl shadow-slate-900/10 w-full max-w-md p-6 transform scale-95 transition-transform duration-300">
            <div class="flex items-start gap-4 mb-2">
                <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-500/10 text-danger flex items-center justify-center flex-shrink-0">
                    <i class="ph-fill ph-warning-circle text-2xl"></i>
                </div>
                <div class="pt-1">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-gray-100" id="confirmModalTitle">Konfirmasi Tindakan</h3>
                    <p class="text-sm text-slate-500 dark:text-gray-400 mt-1 leading-relaxed" id="confirmModalMessage">Apakah Anda yakin?</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-8">
                <button type="button" class="btn-secondary" onclick="closeModal('globalConfirmModal')">Batal</button>
                <a href="#" id="confirmModalBtn" class="btn-primary !bg-danger hover:!bg-red-600 !shadow-danger/20 border-0">Ya, Lanjutkan</a>
            </div>
        </div>
    </div>

    <!-- Choices.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.choices-select');
            selects.forEach(function(select) {
                new Choices(select, {
                    searchEnabled: true,
                    itemSelectText: '',
                    shouldSort: false,
                    placeholder: true,
                    placeholderValue: select.options[0].text,
                    searchPlaceholderValue: 'Ketik untuk mencari...',
                    noResultsText: 'Tidak ada hasil yang ditemukan',
                    noChoicesText: 'Tidak ada pilihan untuk dipilih',
                });
            });
        });
    </script>

    <!-- Main Script -->
    <script src="<?= BASE_URL ?>public/js/main.js?v=1.2"></script>

    <!-- Global Drag-to-Scroll Script for Tables -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const sliders = document.querySelectorAll('.overflow-x-auto');
        let isDown = false;
        let startX;
        let scrollLeft;

        sliders.forEach(slider => {
            // Add hint cursor
            slider.style.cursor = 'grab';

            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                slider.style.cursor = 'grabbing';
                // Remove selection to prevent text highlighting while dragging
                window.getSelection().removeAllRanges();
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });
            
            slider.addEventListener('mouseleave', () => {
                isDown = false;
                slider.style.cursor = 'grab';
            });
            
            slider.addEventListener('mouseup', () => {
                isDown = false;
                slider.style.cursor = 'grab';
            });
            
            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - slider.offsetLeft;
                const walk = (x - startX) * 2; // Scroll-fast multiplier
                slider.scrollLeft = scrollLeft - walk;
            });
        });
    });
    </script>
</body>
</html>
