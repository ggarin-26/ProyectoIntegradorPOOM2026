# Sistema Naval de Novedades

Aplicación web en PHP orientado a objetos para administrar novedades y usuarios. Utiliza arquitectura MVC, PDO para acceder a MySQL/MariaDB y Bootstrap 5 para la interfaz.

## Requisitos

- PHP 8 o posterior, con la extensión PDO MySQL habilitada.
- Apache y MySQL/MariaDB (por ejemplo, mediante XAMPP).
- Una base de datos configurada con las tablas `usuarios`, `novedades` y `secciones`.

El proyecto no incluye un volcado ni un script de creación del esquema de la base de datos.

## Configuración local

1. Copiá el proyecto en `C:\xampp2\htdocs\ProyectoIntegradorPOOM2026`.
2. Iniciá Apache y MySQL/MariaDB.
3. Configurá `app/config/database.local.php` para que devuelva los datos de conexión:

   ```php
   <?php
   return [
       'host' => '127.0.0.1',
       'puerto' => '3306',
       'basedatos' => 'nombre_de_la_base',
       'usuario' => 'usuario_local',
       'clave' => 'clave_local',
   ];
   ```

   Este archivo contiene credenciales locales: no las publiques ni las compartas.
4. Abrí `http://localhost/ProyectoIntegradorPOOM2026/public/index.php`.

La conexión PDO se administra mediante `app/config/Database.php`. Las vistas y enlaces usan `BASE_URL`, calculada desde la ruta de `public/index.php`.

## Funcionalidades

- **Novedades:** listado, búsqueda y filtro por sección; creación, consulta y edición según el rol. El número se asigna al crear y no cambia al editar. La fecha y hora de alta también se conserva.
- **Secciones:** creación y renombrado desde el formulario de novedades, con validación para evitar nombres duplicados. Para garantizar la unicidad ante operaciones simultáneas, la base de datos también debe tener una restricción `UNIQUE` sobre `secciones.nombre`.
- **Reportes:** resumen de usuarios registrados, novedades totales y novedades del mes.
- **Usuarios:** listado, búsqueda, filtros por rol y estado, consulta y administración según permisos.
- **Usuarios inactivos:** no aparecen en el selector de ingreso; el sistema los rechaza al iniciar sesión y vuelve a comprobar el estado en cada solicitud.

## Roles

| Rol | Permisos |
| --- | --- |
| Lector | Consultar novedades y usuarios. |
| Editor | Consultar, crear y editar novedades; consultar usuarios. |
| Administrador | Administrar usuarios, novedades y secciones. |

Las acciones no permitidas también se bloquean en el servidor y muestran una respuesta de acceso denegado.

### Limitación del acceso de demostración

El ingreso actual permite seleccionar un usuario activo, pero no solicita contraseña ni verifica la identidad. Cualquier persona puede elegir una cuenta Administrador. Este mecanismo es únicamente para demostración local y **no debe usarse como autenticación en un entorno compartido o público**.

## Estructura principal

```text
app/
  config/       Configuración y conexión PDO
  controllers/  Controladores MVC
  models/       Modelos de usuarios y novedades
  views/        Vistas y componentes compartidos
core/
  autoload.php  Autocargador de clases
public/
  assets/       Imágenes
  css/          Estilos de la aplicación
  index.php     Front controller y control de permisos
```

## Estado y limitaciones conocidas

- La aplicación está conectada a la base configurada localmente; el esquema no se distribuye con el proyecto.
- La restricción de nombre único para las secciones debe existir también en la base de datos. No hay una migración SQL incluida actualmente.
- No se incluye una suite de pruebas automatizadas.
- El pie de página carga scripts de DataTables que requieren jQuery; si jQuery no está disponible, el navegador muestra errores de JavaScript y la inicialización de DataTables falla.
