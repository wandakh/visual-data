import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/**
 * Loading state generik: tiap tombol submit yang punya attribute
 * data-loading-text bakal otomatis ke-disable + teksnya ganti pas form-nya
 * di-submit. Mencegah double-submit (misal double-klik "Hapus") sekaligus
 * ngasih feedback visual kalau prosesnya lagi jalan.
 */
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    const btn = form.querySelector('[data-loading-text]');
    if (!btn) return;

    // Kasih waktu 1 tick biar validasi HTML5 (required, dst) sempat jalan
    // dulu -- kalau form gagal validasi, submit event ini gak akan lanjut
    // ke server, jadi kita gak mau kepalang nge-disable tombolnya.
    requestAnimationFrame(() => {
        if (!form.checkValidity || form.checkValidity()) {
            btn.dataset.originalText = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            btn.innerHTML = `<svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> ${btn.dataset.loadingText}`;
        }
    });
});
