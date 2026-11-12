(function () {
    var wurzel = document.documentElement;
    var gespeichert = null;
    try { gespeichert = localStorage.getItem('fms-theme'); } catch (e) {}
    var theme = gespeichert || (window.matchMedia && matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    wurzel.setAttribute('data-bs-theme', theme);

    document.addEventListener('DOMContentLoaded', function () {
        var knopf = document.getElementById('themeToggle');
        if (!knopf) { return; }
        function symbol() {
            var dunkel = wurzel.getAttribute('data-bs-theme') === 'dark';
            knopf.innerHTML = '<i class="bi ' + (dunkel ? 'bi-sun-fill' : 'bi-moon-stars-fill') + '"></i>';
            knopf.setAttribute('aria-pressed', dunkel ? 'true' : 'false');
        }
        symbol();
        knopf.addEventListener('click', function () {
            var neu = wurzel.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            wurzel.setAttribute('data-bs-theme', neu);
            try { localStorage.setItem('fms-theme', neu); } catch (e) {}
            symbol();
        });
    });
})();
