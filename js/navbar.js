document.addEventListener('DOMContentLoaded', function () {
    let navToggle = document.getElementById('navToggle');
    let sidebar = document.getElementById('mascotas');
    let overlay = document.getElementById('navOverlay');
    let sidebarClose = document.getElementById('sidebarClose');

    if (!navToggle || !sidebar || !overlay) return;

    function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        navToggle.classList.add('is-active');
        navToggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        navToggle.classList.remove('is-active');
        navToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    function toggleSidebar() {
        if (sidebar.classList.contains('active')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }

    navToggle.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', closeSidebar);

    if (sidebarClose) {
        sidebarClose.addEventListener('click', closeSidebar);
    }

    // Cerrar con la tecla Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

    // Si la pantalla vuelve a tamaño de escritorio, resetea el estado
    window.addEventListener('resize', function () {
        if (window.innerWidth > 850) closeSidebar();
    });
});