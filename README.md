# Sistema Naval de Novedades ABM — Gestión de Novedades y Usuarios (PHP OOP + PDO + MVC)

Aplicación web para la gestión de novedades, secciones y usuarios. Está desarrollada en PHP orientado a objetos, utiliza PDO para el acceso a MySQL/MariaDB y organiza el código con el patrón Modelo-Vista-Controlador (MVC). La interfaz utiliza Bootstrap 5.

## 🧱 Stack tecnológico

- **Backend:** PHP 8 o posterior, orientado a objetos.
- **Base de datos:** MySQL/MariaDB mediante PDO.
- **Arquitectura:** MVC, con `app/models`, `app/controllers` y `app/views`.
- **Frontend:** Bootstrap 5, Bootstrap Icons, DataTables y CSS propio.
- **Tipografía:** Google Fonts (Inter y Poppins).
- **Entorno local:** Apache y MySQL/MariaDB, por ejemplo mediante XAMPP.

## 📌 Estado actual del proyecto

### ✅ Completado

- Estructura MVC y autoload de clases por namespace.
- Conexión PDO mediante `config/Database.php`, implementada como Singleton.
- Front controller en `public/index.php`, con lista blanca de módulos y acciones.
- Gestión de usuarios: listado, búsqueda, filtros, consulta, alta, edición y eliminación.
- Gestión de novedades: listado, búsqueda, filtro por sección, consulta, alta, edición y eliminación.
- Gestión de secciones: creación y renombrado desde el formulario de novedades.
- Validaciones del lado del servidor para los formularios.
- Permisos por rol: Lector, Editor y Administrador.
- Revalidación del estado y rol del usuario en cada solicitud protegida.
- Diseño propio en `public/css/estilos.css` y componentes compartidos en `app/views/partials`.

### 🔜 Mejoras pendientes

- Incorporar autenticación con contraseña y almacenamiento seguro de credenciales.
- Agregar protección CSRF a las operaciones que modifican datos.
- Asegurar la unicidad de `secciones.nombre` mediante una restricción `UNIQUE` en la base.
- Corregir la inicialización de DataTables: actualmente usa jQuery, pero las vistas no cargan esa dependencia.
- Incorporar una suite de pruebas automatizadas.

## 📁 Estructura del proyecto

```text
ProyectoIntegradorPOOM2026/
├── app/
│   ├── controllers/
│   │   ├── AutenticacionController.php  # Acceso de demostración y cierre de sesión
│   │   ├── NovedadController.php        # Acciones de novedades y secciones
│   │   └── UsuarioController.php        # Acciones de usuarios
│   ├── models/
│   │   ├── Novedad.php                  # Consultas PDO de novedades y secciones
│   │   └── Usuario.php                  # Consultas PDO de usuarios
│   └── views/
│       ├── auth/
│       │   ├── acceso_denegado.php      # Vista de acceso denegado
│       │   └── login.php                # Selector de usuario para la demostración
│       ├── novedades/
│       │   ├── index.php                # Listado, búsqueda, filtro y reportes
│       │   ├── crear.php                # Alta de novedad
│       │   ├── editar.php                # Edición de novedad
│       │   ├── Formulario.php           # Formulario de alta y edición
│       │   └── FormularioVer.php        # Consulta de novedad
│       ├── partials/
│       │   ├── header.php               # Metadatos, fuentes, Bootstrap y CSS
│       │   ├── navbar.php               # Barra de navegación
│       │   ├── encabezado2.php          # Encabezado compartido
│       │   └── footer.php               # Pie de página y scripts
│       └── usuarios/
│           ├── index.php                # Listado, búsqueda y filtros
│           ├── crear.php                # Alta de usuario
│           ├── editar.php                # Edición de usuario
│           ├── formulario.php           # Formulario de alta y edición
│           └── ver.php                  # Consulta de usuario
├── config/
│   ├── config.php                       # Define BASE_URL
│   ├── Database.php                     # Conexión PDO (patrón Singleton)
│   └── database.local.php               # Credenciales locales
├── core/
│   └── autoload.php                     # Autoloader por namespace
├── public/                              # Document root recomendado
│   ├── index.php                        # Front controller y permisos
│   ├── css/
│   │   └── estilos.css                  # Estilos propios
│   └── assets/
│       └── img/                         # Imágenes de la interfaz
└── README.md
```

## Rutas disponibles

Las rutas se atienden desde `public/index.php`. Las acciones de escritura requieren solicitudes POST; el acceso se limita además según el rol.

| Módulo | URL base | Acciones permitidas por el router |
| --- | --- | --- |
| Autenticación | `public/index.php?ctrl=auth&accion=…` | `login`, `iniciar`, `cerrar` |
| Usuarios | `public/index.php?ctrl=usuarios&accion=…` | `index`, `crear`, `guardar`, `ver`, `editar`, `actualizar`, `eliminar` |
| Novedades | `public/index.php?ctrl=novedades&accion=…` | `index`, `crear`, `guardar`, `crearSeccion`, `renombrarSeccion`, `ver`, `editar`, `actualizar`, `eliminar` |

El router define las acciones aceptadas; los permisos por rol pueden restringirlas aún más.

## ⚙️ Instalación local (XAMPP)

1. Copiá el proyecto en `C:\xampp2\htdocs\ProyectoIntegradorPOOM2026` o en otra carpeta dentro de `htdocs`.
2. Iniciá Apache y MySQL/MariaDB desde XAMPP.
3. Creá una base de datos y prepará el esquema requerido. Debe incluir las tablas `usuarios`, `novedades`, `secciones` y `novedades_correlativos`.
4. Configurá `config/database.local.php` con los datos de tu instalación:

   ```php
   <?php
   return [
       'host' => 'localhost',
       'puerto' => '3306',
       'basedatos' => 'nombre_de_la_base',
       'usuario' => 'usuario_local',
       'clave' => 'clave_local',
   ];
   ```

   No publiques ni compartas este archivo: contiene credenciales locales.
5. Abrí `http://localhost/ProyectoIntegradorPOOM2026/public/index.php`.

La aplicación debe servirse desde `public/`. `BASE_URL` se calcula en `config/config.php` a partir de la ruta desde la que se accede a `public/index.php`.

## 📝 Notas y limitaciones

- **Acceso de demostración:** el inicio de sesión permite elegir un usuario activo y no solicita contraseña. Cualquier visitante puede elegir una cuenta con permisos administrativos. No usar en un entorno compartido o público.
- **Roles:** Lector consulta novedades y usuarios; Editor puede crear y editar novedades y consultar usuarios; Administrador administra usuarios, novedades y secciones.
- **Base de datos:** debe existir una restricción `UNIQUE` en `secciones.nombre` para evitar duplicados en operaciones concurrentes. La comprobación de la aplicación por sí sola no ofrece esa garantía.
- **DataTables:** las listas se renderizan desde PHP, pero su inicialización JavaScript puede fallar porque el footer usa jQuery sin que las vistas lo carguen.
- **Dependencias frontend:** Bootstrap, iconos, fuentes y DataTables se cargan desde CDN, por lo que su disponibilidad depende de la conexión a Internet.
- **Pruebas:** no hay una suite de pruebas automatizadas.
