<?php
if (!isset($tituloPagina)) {
    $tituloPagina = "Sistema Naval de Novedades ABM";
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($tituloPagina) ?></title>
        
        <!-- Favicon dinámico usando BASE_URL -->
        <link rel="icon" href="<?= BASE_URL ?>/assets/img/pna.png" type="image/x-icon">
        
        <!-- Google Fonts (Inter + Poppins) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 CSS (v5.3.3 Última versión estable v5.3) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        
        <!-- Bootstrap Icons (v1.11.3) -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        
        <!-- DataTables 2 CSS (v2.0.8) -->
        <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
        
        <!-- Estilos propios de la aplicación -->
        <link href="<?= BASE_URL ?>/css/estilos.css?v=<?= filemtime(__DIR__ . '/../../../public/css/estilos.css') ?>" rel="stylesheet">
    </head>
    <body>