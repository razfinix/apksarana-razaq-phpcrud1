(function () {
    'use strict';

    function init() {

        /* =====================================================
           BOOTSTRAP TOOLTIP
        ===================================================== */
        if (window.jQuery && jQuery.fn.tooltip) {
            jQuery('[data-toggle="tooltip"]').tooltip();
        }


        /* =====================================================
           ELEMENT
        ===================================================== */
        const peminjamToggle = document.getElementById('peminjamMenuToggle');
        const adminToggle = document.getElementById('adminMenuToggle');

        const peminjamSidebar = document.querySelector(
            '.peminjam-layout .main-sidebar, .peminjam-sidebar'
        );

        const adminSidebar = document.querySelector(
            '.admin-sidebar, .main-sidebar'
        );


        /* =====================================================
           BUAT OVERLAY
        ===================================================== */
        function createOverlay(id) {

            let overlay = document.getElementById(id);

            if (!overlay) {
                overlay = document.createElement('div');

                overlay.id = id;
                overlay.className = 'mobile-sidebar-overlay';

                document.body.appendChild(overlay);
            }

            return overlay;
        }


        /* =====================================================
           PEMINJAM
        ===================================================== */
        if (peminjamToggle && peminjamSidebar) {

            const overlay = createOverlay('peminjamSidebarOverlay');

            function openPeminjamSidebar() {

                peminjamSidebar.classList.add('sidebar-open');

                document.body.classList.add(
                    'mobile-sidebar-open'
                );

                overlay.classList.add('active');

                peminjamToggle.setAttribute(
                    'aria-expanded',
                    'true'
                );
            }


            function closePeminjamSidebar() {

                peminjamSidebar.classList.remove(
                    'sidebar-open'
                );

                document.body.classList.remove(
                    'mobile-sidebar-open'
                );

                overlay.classList.remove('active');

                peminjamToggle.setAttribute(
                    'aria-expanded',
                    'false'
                );
            }


            peminjamToggle.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();
                    event.stopPropagation();

                    if (
                        peminjamSidebar.classList.contains(
                            'sidebar-open'
                        )
                    ) {

                        closePeminjamSidebar();

                    } else {

                        openPeminjamSidebar();

                    }

                }
            );


            overlay.addEventListener(
                'click',
                function () {

                    closePeminjamSidebar();

                }
            );


            peminjamSidebar
                .querySelectorAll('a')
                .forEach(function (link) {

                    link.addEventListener(
                        'click',
                        function () {

                            closePeminjamSidebar();

                        }
                    );

                });


            window.addEventListener(
                'resize',
                function () {

                    if (window.innerWidth > 991) {

                        closePeminjamSidebar();

                    }

                }
            );

        }


        /* =====================================================
           ADMIN / PETUGAS
        ===================================================== */
        if (adminToggle) {

            /*
             * Cari sidebar yang dipakai Admin/Petugas.
             * Peminjam tidak ikut karena memiliki toggle sendiri.
             */
            let sidebar = document.querySelector(
                'body:not(.peminjam-layout) .main-sidebar'
            );

            if (sidebar) {

                const overlay = createOverlay(
                    'adminSidebarOverlay'
                );


                function openAdminSidebar() {

                    document.body.classList.add(
                        'admin-mobile-open'
                    );

                    sidebar.classList.add(
                        'sidebar-open'
                    );

                    overlay.classList.add(
                        'active'
                    );

                    adminToggle.setAttribute(
                        'aria-expanded',
                        'true'
                    );
                }


                function closeAdminSidebar() {

                    document.body.classList.remove(
                        'admin-mobile-open'
                    );

                    sidebar.classList.remove(
                        'sidebar-open'
                    );

                    overlay.classList.remove(
                        'active'
                    );

                    adminToggle.setAttribute(
                        'aria-expanded',
                        'false'
                    );
                }


                adminToggle.addEventListener(
                    'click',
                    function (event) {

                        event.preventDefault();
                        event.stopPropagation();

                        if (window.innerWidth <= 991) {

                            if (
                                sidebar.classList.contains(
                                    'sidebar-open'
                                )
                            ) {

                                closeAdminSidebar();

                            } else {

                                openAdminSidebar();

                            }

                        } else {

                            /*
                             * Desktop tetap memakai
                             * perilaku AdminLTE.
                             */
                            document.body.classList.toggle(
                                'sidebar-collapse'
                            );

                        }

                    }
                );


                overlay.addEventListener(
                    'click',
                    function () {

                        closeAdminSidebar();

                    }
                );


                sidebar
                    .querySelectorAll('a')
                    .forEach(function (link) {

                        link.addEventListener(
                            'click',
                            function () {

                                if (
                                    window.innerWidth <= 991
                                ) {

                                    closeAdminSidebar();

                                }

                            }
                        );

                    });


                window.addEventListener(
                    'resize',
                    function () {

                        if (window.innerWidth > 991) {

                            closeAdminSidebar();

                        }

                    }
                );

            }

        }


        /* =====================================================
           LANDING PAGE - CAHAYA MENGIKUTI MOUSE
        ===================================================== */
        const hero = document.querySelector('.hero-modern');
        const mouseLight = document.querySelector('.hero-mouse-light');

        if (hero && mouseLight && window.matchMedia('(pointer:fine)').matches) {
            let targetX = hero.clientWidth * 0.5;
            let targetY = hero.clientHeight * 0.45;
            let currentX = targetX;
            let currentY = targetY;
            let raf = null;

            function animateMouseLight() {
                currentX += (targetX - currentX) * 0.12;
                currentY += (targetY - currentY) * 0.12;
                mouseLight.style.transform = `translate3d(${currentX}px, ${currentY}px, 0) translate3d(-50%, -50%, 0)`;
                raf = requestAnimationFrame(animateMouseLight);
            }

            hero.addEventListener('mousemove', function (event) {
                const rect = hero.getBoundingClientRect();
                targetX = event.clientX - rect.left;
                targetY = event.clientY - rect.top;
                hero.classList.remove('cursor-away');
            });

            hero.addEventListener('mouseenter', function () {
                hero.classList.remove('cursor-away');
            });

            hero.addEventListener('mouseleave', function () {
                hero.classList.add('cursor-away');
            });

            animateMouseLight();
        }


        /* =====================================================
           LANDING V2 - TILT CARD
        ===================================================== */
        const tiltCard = document.querySelector('[data-tilt-card]');
        if (tiltCard && window.matchMedia('(pointer:fine)').matches && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const glass = tiltCard.querySelector('.scene-glass');
            tiltCard.addEventListener('mousemove', function (event) {
                const rect = tiltCard.getBoundingClientRect();
                const x = (event.clientX - rect.left) / rect.width - .5;
                const y = (event.clientY - rect.top) / rect.height - .5;
                if (glass) glass.style.transform = `rotateY(${-x * 9 - 6}deg) rotateX(${y * 7}deg) translateZ(4px)`;
            });
            tiltCard.addEventListener('mouseleave', function () {
                if (glass) glass.style.transform = 'rotateY(-6deg) rotateX(2deg)';
            });
        }

        /* =====================================================
           ESC UNTUK MENUTUP SIDEBAR
        ===================================================== */
        document.addEventListener(
            'keydown',
            function (event) {

                if (event.key === 'Escape') {

                    const pemSidebar =
                        document.querySelector(
                            '.peminjam-layout .main-sidebar.sidebar-open'
                        );

                    const adminSidebar =
                        document.querySelector(
                            '.main-sidebar.sidebar-open'
                        );


                    if (pemSidebar) {

                        pemSidebar.classList.remove(
                            'sidebar-open'
                        );

                    }


                    if (adminSidebar) {

                        adminSidebar.classList.remove(
                            'sidebar-open'
                        );

                    }


                    document.body.classList.remove(
                        'admin-mobile-open',
                        'mobile-sidebar-open'
                    );


                    document
                        .querySelectorAll(
                            '.mobile-sidebar-overlay'
                        )
                        .forEach(function (overlay) {

                            overlay.classList.remove(
                                'active'
                            );

                        });

                }

            }
        );

    }


    /* =====================================================
       JALANKAN SAAT HALAMAN SIAP
    ===================================================== */

    if (
        document.readyState === 'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            init
        );

    } else {

        init();

    }

})();