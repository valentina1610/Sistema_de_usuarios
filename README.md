# ✦ Sistema de Usuarios

Sistema web de gestión con autenticación, permisos por roles y administración de usuarios. Desarrollado en PHP puro con archivos JSON como base de datos.

---

## 📁 Estructura de carpetas

```
Sistema_de_usuarios/
├── db/
│   ├── usuario/              # Un .json por usuario (nombre: {id}.json)
│   └── permisos/             # Un .json por perfil de permisos (nombre: {uuid}.json)
├── sistema/
│   ├── login.php             # Autenticación + carga de permisos en sesión
│   ├── logout.php            # Cierre de sesión
│   └── include/
│       ├── usuario/
│       │   ├── usuario.php           # Clase Usuario
│       │   ├── crear_cuenta.php      # Registro de nuevo usuario
│       │   ├── edicion_ajax.php      # Edición de usuario (AJAX)
│       │   ├── listar_usuarios.php   # Lista todos los usuarios (AJAX)
│       │   └── eliminar_usuario.php  # Elimina usuarios por ID (AJAX)
│       └── permisos/
│           ├── Permisos.php          # Clase Permisos
│           ├── repo_permisos.php     # Repositorio: guardar, listar, eliminar
│           └── config_permisos.php   # Controlador AJAX de permisos
├── dashboard.php             # Panel principal (SPA sin recarga)
├── login.html                # Formulario de login
└── registro.html             # Formulario de registro
```

---

## 👤 Usuarios

Cada usuario se guarda en `db/usuario/{id}.json` con esta estructura:

```json
{
    "id": 2,
    "nombre": "Maria Perez",
    "usuario": "maria123",
    "clave": "$2y$10$...",
    "activo": 1,
    "permiso_id": "uuid-del-perfil"
}
```

### Reglas importantes

- El usuario con **ID 1 es el Owner**. Tiene acceso total a todos los módulos y su perfil no puede modificarse ni puede ser eliminado.
- Los usuarios nuevos reciben automáticamente el perfil **"Usuario normal"** al registrarse.
- La clave se guarda siempre hasheada con `password_hash()`.

---

## 🔐 Permisos

Cada perfil de permisos se guarda en `db/permisos/{uuid}.json`:

```json
{
    "id": "uuid-generado",
    "descripcion": "Recepcionista",
    "permisos": {
        "General": 1,
        "Paciente": 1,
        "Clientes": 0,
        "Agenda": 1,
        "Productos": 0,
        "Ordenes de Servicio": 0,
        "Facturacion": 0,
        "Libros": 0,
        "Proveedores": 0,
        "Usuarios": 0,
        "Roles": 0,
        "Configuraciones": 0
    }
}
```

- `1` = tiene acceso al módulo
- `0` = sin acceso
- El ID se genera como UUID aleatorio y es también el nombre del archivo

---

## 🚀 Cómo funciona el sistema de permisos

1. El usuario inicia sesión en `sistema/login.php`
2. El login busca el `permiso_id` en el JSON del usuario
3. Carga el archivo `db/permisos/{permiso_id}.json`
4. Guarda en `$_SESSION`:
   - `$_SESSION['permisos']` → array con cada módulo y su valor 0/1
   - `$_SESSION['es_owner']` → true/false
   - `$_SESSION['permiso_descripcion']` → nombre del perfil (ej: "Recepcionista")
5. El `dashboard.php` lee la sesión y muestra u oculta cada módulo del sidebar

---

## 🛠 Instalación

1. Cloná o copiá la carpeta en `C:\xampp\htdocs\Sistema_de_usuarios\`
2. Iniciá Apache desde XAMPP
3. Accedé a `http://localhost/Sistema_de_usuarios/login.html`
4. Creá el primer usuario — ese será el **Owner** (ID 1) con acceso total

### Requisitos

- PHP 7.4 o superior
- XAMPP (o cualquier servidor con Apache + PHP)
- No requiere base de datos

---

## 📋 Módulos del sistema

| Módulo | Descripción |
|---|---|
| General | Pantalla de inicio / bienvenida |
| Paciente | Gestión de pacientes |
| Clientes | Gestión de clientes |
| Agenda | Agenda y turnos |
| Productos | Inventario de productos |
| Ordenes de Servicio | Órdenes de trabajo |
| Facturacion | Facturación |
| Libros | Registros contables |
| Proveedores | Gestión de proveedores |
| Usuarios | Administración de usuarios |
| Roles | Gestión de roles |
| Configuraciones | Perfiles de permisos |

---

## 🔄 Flujo de navegación (SPA)

El dashboard funciona como una **Single Page Application** — al hacer clic en un módulo del sidebar el contenido cambia sin recargar la página. Los datos de usuarios y permisos se cargan una sola vez via AJAX y se cachean en memoria.

---

## ⚠️ Notas de seguridad

- Todos los endpoints AJAX verifican que exista una sesión activa antes de responder
- El Owner (ID 1) no puede ser eliminado ni se le puede cambiar el perfil
- Las claves nunca se devuelven al frontend (`listar_usuarios.php` las omite)
- Los permisos se validan tanto en PHP (sidebar) como se bloquea la navegación en JS
