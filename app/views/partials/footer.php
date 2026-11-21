<?php ?>
 
<footer class="app-footer mt-5">
    <div class="container-fluid px-3 px-lg-4 py-4">
        <div class="row gy-4">
         <!-- Columna 1: Info del sistema -->
    <div class="col-12 col-md-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="brand-mark"><i class="bi bi-diagram-3-fill"></i></span>
            <span class="brand-text">Sistema Naval de Novedades <strong>ABM</strong></span>
        </div>
        
        <!-- Descripción principal -->
        <p class="footer-text mb-2 text-white-50 small" style="line-height: 1.5;">
            Módulo de gestión de datos (Alta, baja, modificación) desarrollado en PHP orientado a objetos con PDO, bajo el patrón MVC.
        </p>

        <!-- Bloque de datos del alumno con mejor distinción visual -->
        <div class="border-start border-success border-3 ps-2 mt-2 text-white-50" style="font-size: 0.82rem;">
            <div class="fw-semibold text-white">Alumno: Gabriel Garin</div>
            <div class="fst-italic">Futuro Licenciado en gestión de herramientas digitales para la seguridad marítima.</div>
        </div>
    </div>
            
            <!-- Columna 2: Accesos -->
            <div class="col-6 col-md-4">
                <h6 class="footer-heading">Accesos</h6>
                <ul class="list-unstyled footer-text footer-links">
                    <li><a href="<?= BASE_URL ?>/index.php">Inicio</a></li>
                    <li><a href="<?= BASE_URL ?>/index.php?ctrl=usuarios&amp;accion=index">Usuarios</a></li>
                    <li><a href="<?= BASE_URL ?>/index.php?ctrl=novedades&amp;accion=index#resumen-reportes">Reportes</a></li>
                    <li><a href="<?= BASE_URL ?>/index.php?ctrl=novedades&amp;accion=index">Novedades</a></li>
                </ul>
            </div>

            <!-- Columna 3: Contacto y Redes -->
            <div class="col-12 col-md-4">
                <h6 class="footer-heading mb-2">Contacto</h6>
                <p class="footer-text mb-1 text-nowrap">
                    <strong class="fw-bold">Dirección:</strong> Edificio Guardacostas, Av. Eduardo Madero 235
                </p>
                <p class="footer-text mb-1">
                    <strong class="fw-bold">Código postal:</strong> C1106ACC
                </p>
                <p class="footer-text mb-1">
                    <strong class="fw-bold">Teléfono:</strong> (+5411) 4318-7400 / 0800-888-7730 Lunes a Viernes 9 a 17 hs.
                </p>
                <p class="footer-text mb-3">
                    <strong class="fw-bold">Correo electrónico:</strong> 
                    <a href="mailto:info@prefecturanaval.gob.ar" class="text-primary text-decoration-underline">
                        info@prefecturanaval.gob.ar
                    </a>
                </p>

                <h6 class="footer-heading mb-2">Seguinos</h6>
                <div class="d-flex gap-3 align-items-center fs-5 text-primary">
                    <a href="https://www.facebook.com/prefecturanavalar" target="_blank" rel="noopener noreferrer" class="text-primary" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://x.com/PrefecturaNaval" target="_blank" rel="noopener noreferrer" class="text-primary" title="X (Twitter)"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://www.instagram.com/prefecturanaval" target="_blank" rel="noopener noreferrer" class="text-primary" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="https://www.youtube.com/prefecturanavalar" target="_blank" rel="noopener noreferrer" class="text-primary" title="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="https://www.linkedin.com/company/prefecturanavalargentina" target="_blank" rel="noopener noreferrer" class="text-primary" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="https://www.tiktok.com/@prefecturanaval.ar" target="_blank" rel="noopener noreferrer" class="text-primary" title="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

<!-- DataTables Core + Integración oficial con Bootstrap 5 -->
<script src="https://cdn.datatables.net/v/dt/dt-2.0.8/datatables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
    // Inicialización de DataTable con soporte para Bootstrap 5 e idioma en español
    $(document).ready(function() {
        $('#example').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json'
            }
        });
    });
</script>
</body>
</html>