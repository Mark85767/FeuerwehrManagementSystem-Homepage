(function () {
    var menue = document.getElementById('hauptmenue');
    if (!menue || !window.bootstrap) { return; }
    menue.querySelectorAll('a.nav-link, a.btn').forEach(function (link) {
        link.addEventListener('click', function () {
            if (menue.classList.contains('show')) {
                bootstrap.Collapse.getOrCreateInstance(menue).hide();
            }
        });
    });
})();
