// Tombol layar penuh untuk halaman masuk kuis
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btn-fullscreen');
    if (!btn) return;

    const root = document.documentElement;
    const canRequest = root.requestFullscreen || root.webkitRequestFullscreen;

    // iPhone Safari tidak mendukung fullscreen API -> sembunyikan tombol
    if (!canRequest) {
        btn.hidden = true;
        return;
    }

    const isFullscreen = () =>
        !!(document.fullscreenElement || document.webkitFullscreenElement);

    const toggle = () => {
        if (isFullscreen()) {
            (document.exitFullscreen || document.webkitExitFullscreen).call(document);
        } else {
            (root.requestFullscreen || root.webkitRequestFullscreen).call(root);
        }
    };

    const updateLabel = () => {
        btn.setAttribute('aria-label', isFullscreen() ? 'Keluar layar penuh' : 'Layar penuh');
    };

    btn.addEventListener('click', toggle);
    document.addEventListener('fullscreenchange', updateLabel);
    document.addEventListener('webkitfullscreenchange', updateLabel);
});