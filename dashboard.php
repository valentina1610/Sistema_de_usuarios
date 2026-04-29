<?php
session_start();

if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header('Location: login.html');
    exit;
}

$menu_items = [
    'General', 'Paciente', 'Clientes', 'Agenda', 'Productos',
    'Ordenes de Servicio', 'Facturacion', 'Libros', 'Proveedores',
    'Usuarios', 'Roles', 'Configuraciones',
];

$permisos   = $_SESSION['permisos']   ?? [];
$es_owner   = $_SESSION['es_owner']   ?? false;
$permiso_descripcion = $_SESSION['permiso_descripcion'] ?? null;
function tieneAcceso($modulo, $permisos, $es_owner) {
    if ($es_owner) return true;
    return isset($permisos[$modulo]) && $permisos[$modulo] == 1;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet" />
    <style>
        :root {
            --rosa: #f472b6; --rosa-oscuro: #db2777;
            --crema: #fff7f0; --texto: #1e1e2e; --muted: #6b7280;
        }
        * { box-sizing: border-box; }
        body { margin:0; background:var(--crema); font-family:'DM Sans',sans-serif; color:var(--texto); background-image:radial-gradient(circle at 20% 20%,#fce7f3 0%,transparent 50%); }
        .app-shell { min-height:100vh; display:flex; }

        /* Sidebar */
        .sidebar { width:290px; background:rgba(255,255,255,0.95); border-right:1px solid #fce7f3; padding:24px 18px; position:sticky; top:0; height:100vh; backdrop-filter:blur(14px); transition:width .25s ease,padding .25s ease; overflow-y:auto; }
        .sidebar.collapsed { width:96px; padding-left:14px; padding-right:14px; }
        .brand-wrap { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:24px; }
        .brand-title { font-family:'Playfair Display',serif; font-size:1.45rem; white-space:nowrap; margin:0; }
        .sidebar.collapsed .brand-title,.sidebar.collapsed .menu-label,.sidebar.collapsed .section-label,.sidebar.collapsed .menu-chevron,.sidebar.collapsed .menu-helper { display:none; }
        .toggle-btn { width:42px; height:42px; border:none; border-radius:14px; background:#fce7f3; color:var(--rosa-oscuro); font-size:1.2rem; display:inline-flex; align-items:center; justify-content:center; transition:transform .2s,background .2s; }
        .toggle-btn:hover { background:#fbcfe8; transform:translateY(-1px); }
        .sidebar-section { background:linear-gradient(180deg,#fff8fb 0%,#ffffff 100%); border:1px solid #fce7f3; border-radius:22px; padding:14px; }
        .sidebar.collapsed .sidebar-section { padding:10px 8px; }
        .section-label { display:inline-block; font-size:0.78rem; letter-spacing:.08em; text-transform:uppercase; color:var(--muted); margin-bottom:12px; }
        .menu-list { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:8px; }
        .menu-button { width:100%; border:none; background:transparent; border-radius:16px; padding:12px 14px; display:flex; align-items:center; gap:12px; color:var(--texto); transition:background .2s,transform .2s; text-align:left; }
        .menu-button:hover,.menu-button.active { background:#fdf2f8; transform:translateX(2px); }
        .menu-button.sin-acceso { opacity:.4; cursor:not-allowed; }
        .menu-button.sin-acceso:hover { background:transparent; transform:none; }
        .menu-icon { width:38px; height:38px; border-radius:12px; background:#fce7f3; color:var(--rosa-oscuro); display:inline-flex; align-items:center; justify-content:center; font-weight:700; flex-shrink:0; }
        .menu-button.sin-acceso .menu-icon { background:#f3f4f6; color:#9ca3af; }
        .menu-label-wrap { min-width:0; flex:1; }
        .menu-label { display:block; font-weight:600; line-height:1.2; }
        .menu-helper { display:block; color:var(--muted); font-size:0.78rem; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .menu-chevron { color:#d946ef; font-size:.9rem; }
        .sidebar.collapsed .menu-button { justify-content:center; padding-left:8px; padding-right:8px; }

        /* Main */
        .main-content { flex:1; min-width:0; }
        .navbar-custom { background:rgba(255,255,255,0.82); border-bottom:1px solid #fce7f3; padding:16px 32px; display:flex; justify-content:space-between; align-items:center; gap:20px; backdrop-filter:blur(14px); }
        .navbar-brand { font-family:'Playfair Display',serif; color:var(--texto)!important; margin:0; }
        .btn-logout { background:linear-gradient(135deg,var(--rosa),var(--rosa-oscuro)); color:white; border:none; border-radius:10px; padding:8px 20px; font-weight:500; font-size:.88rem; transition:all .2s; text-decoration:none; }
        .btn-logout:hover { opacity:.85; color:white; }

        /* Secciones */
        .content-section { display:none; }
        .content-section.active { display:block; animation:fadeUp .35s ease; }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }

        .card-bienvenida { background:white; border-radius:24px; padding:40px; border:1px solid #fce7f3; box-shadow:0 8px 40px rgba(244,114,182,.1); }
        .titulo { font-family:'Playfair Display',serif; color:var(--texto); }
        .badge-sesion { background:#fce7f3; color:var(--rosa-oscuro); border-radius:20px; padding:4px 14px; font-size:.8rem; font-weight:500; }
        .badge-owner { background:linear-gradient(135deg,#fce7f3,#fbcfe8); color:var(--rosa-oscuro); border-radius:20px; padding:4px 14px; font-size:.8rem; font-weight:700; }

        /* Cards de config/tabla */
        .config-card { background:white; border-radius:24px; border:1px solid #fce7f3; box-shadow:0 8px 40px rgba(244,114,182,.08); overflow:hidden; }
        .config-toolbar { display:flex; align-items:center; gap:10px; padding:20px 24px; border-bottom:1px solid #fce7f3; background:#fff8fb; }
        .config-toolbar h2 { font-family:'Playfair Display',serif; font-size:1.2rem; margin:0; flex:1; }
        .btn-nuevo { background:linear-gradient(135deg,var(--rosa),var(--rosa-oscuro)); color:white; border:none; border-radius:10px; padding:8px 18px; font-weight:600; font-size:.88rem; display:inline-flex; align-items:center; gap:6px; transition:opacity .2s,transform .2s; cursor:pointer; }
        .btn-nuevo:hover { opacity:.88; transform:translateY(-1px); }
        .dropdown-acciones .dropdown-toggle { background:white; border:1.5px solid #fce7f3; color:var(--texto); border-radius:10px; padding:8px 14px; font-size:.88rem; font-weight:500; display:inline-flex; align-items:center; gap:6px; }
        .dropdown-acciones .dropdown-toggle:hover { background:#fdf2f8; }
        .dropdown-acciones .dropdown-menu { border:1px solid #fce7f3; border-radius:14px; box-shadow:0 8px 30px rgba(244,114,182,.15); padding:6px; min-width:160px; }
        .dropdown-acciones .dropdown-item { border-radius:10px; padding:9px 14px; font-size:.88rem; display:flex; align-items:center; gap:8px; }
        .dropdown-acciones .dropdown-item:hover { background:#fdf2f8; }
        .dropdown-acciones .dropdown-item.text-danger:hover { background:#fff1f2; }

        /* Tabla */
        .data-table { width:100%; border-collapse:collapse; }
        .data-table thead tr { background:#fdf2f8; }
        .data-table th { padding:12px 20px; font-size:.8rem; text-transform:uppercase; letter-spacing:.07em; color:var(--muted); font-weight:600; text-align:left; border-bottom:1px solid #fce7f3; }
        .data-table th:first-child { width:48px; text-align:center; }
        .data-table td { padding:14px 20px; border-bottom:1px solid #fce7f3; font-size:.92rem; vertical-align:middle; }
        .data-table td:first-child { text-align:center; }
        .data-table tbody tr { transition:background .15s; cursor:pointer; }
        .data-table tbody tr:hover { background:#fff8fb; }
        .data-table tbody tr.selected { background:#fdf2f8; }
        .data-table tbody tr:last-child td { border-bottom:none; }
        .perm-check { width:18px; height:18px; accent-color:var(--rosa-oscuro); cursor:pointer; }
        .badge-permisos { background:#fce7f3; color:var(--rosa-oscuro); border-radius:20px; padding:3px 10px; font-size:.78rem; font-weight:500; }
        .badge-activo { background:#dcfce7; color:#16a34a; border-radius:20px; padding:3px 10px; font-size:.78rem; font-weight:500; }
        .badge-inactivo { background:#fee2e2; color:#dc2626; border-radius:20px; padding:3px 10px; font-size:.78rem; font-weight:500; }
        .badge-sin-permiso { background:#f3f4f6; color:#6b7280; border-radius:20px; padding:3px 10px; font-size:.78rem; font-weight:500; }

        /* Spinner / empty */
        .spinner-rosa { width:28px; height:28px; border:3px solid #fce7f3; border-top-color:var(--rosa-oscuro); border-radius:50%; animation:spin .7s linear infinite; margin:40px auto; display:block; }
        @keyframes spin { to{transform:rotate(360deg)} }
        .empty-state { text-align:center; padding:60px 20px; color:var(--muted); }
        .empty-state .empty-icon { font-size:2.5rem; margin-bottom:12px; }
        .empty-state p { margin:0; font-size:.95rem; }

        /* Toast */
        .toast-container-custom { position:fixed; bottom:24px; right:24px; z-index:9999; display:flex; flex-direction:column; gap:10px; }
        .toast-custom { background:white; border:1px solid #fce7f3; border-radius:14px; padding:14px 20px; box-shadow:0 8px 30px rgba(244,114,182,.18); font-size:.9rem; display:flex; align-items:center; gap:10px; animation:slideInToast .3s ease; min-width:240px; }
        .toast-custom.error { border-color:#fca5a5; }
        @keyframes slideInToast { from{opacity:0;transform:translateX(30px)} to{opacity:1;transform:translateX(0)} }

        /* Modales */
        .modal-content { border:1px solid #fce7f3; border-radius:24px; box-shadow:0 20px 60px rgba(244,114,182,.15); }
        .modal-header { border-bottom:1px solid #fce7f3; padding:20px 24px; }
        .modal-title { font-family:'Playfair Display',serif; }
        .modal-footer { border-top:1px solid #fce7f3; }
        .form-control:focus,.form-select:focus { border-color:var(--rosa); box-shadow:0 0 0 3px rgba(244,114,182,.15); }
        .btn-rosa { background:linear-gradient(135deg,var(--rosa),var(--rosa-oscuro)); color:white; border:none; border-radius:10px; padding:9px 22px; font-weight:600; font-size:.9rem; transition:opacity .2s; }
        .btn-rosa:hover { opacity:.88; color:white; }
        .btn-rosa:disabled { opacity:.6; }

        /* Preview módulos */
        .modulos-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:8px; }
        .modulo-item { display:flex; align-items:center; justify-content:space-between; background:#fff8fb; border:1px solid #fce7f3; border-radius:12px; padding:10px 14px; font-size:.88rem; font-weight:500; }

        /* Acceso denegado */
        .acceso-denegado { text-align:center; padding:80px 20px; }
        .acceso-denegado .icon { font-size:3rem; margin-bottom:16px; }
        .acceso-denegado h3 { font-family:'Playfair Display',serif; color:var(--muted); }
        .acceso-denegado p { color:var(--muted); font-size:.9rem; }

        @media (max-width:991.98px) {
            .app-shell { flex-direction:column; }
            .sidebar,.sidebar.collapsed { width:100%; height:auto; position:relative; padding:16px; }
            .sidebar.collapsed .brand-title,.sidebar.collapsed .menu-label,.sidebar.collapsed .section-label,.sidebar.collapsed .menu-chevron,.sidebar.collapsed .menu-helper { display:initial; }
            .sidebar.collapsed .menu-button { justify-content:flex-start; padding-left:14px; padding-right:14px; }
            .sidebar.collapsed .sidebar-section { padding:14px; }
            .sidebar-menu-wrap.collapsed-mobile { display:none; }
            .navbar-custom { padding:16px 20px; flex-wrap:wrap; }
        }
        @media (max-width:575.98px) {
            .card-bienvenida { padding:26px; }
            .container { padding-left:16px; padding-right:16px; }
            .modulos-grid { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>
<div class="app-shell">

    <aside class="sidebar" id="sidebar">
        <div class="brand-wrap">
            <h2 class="brand-title">✦ Mi sistema</h2>
            <button class="toggle-btn" id="sidebarToggle" type="button">☰</button>
        </div>
        <div class="sidebar-menu-wrap" id="sidebarMenuWrap">
            <div class="sidebar-section">
                <span class="section-label">Menú contextual</span>
                <ul class="menu-list">
                    <?php foreach ($menu_items as $index => $item):
                        $acceso    = tieneAcceso($item, $permisos, $es_owner);
                        $sectionId = strtolower(str_replace(' ', '-', $item));
                    ?>
                    <li>
                        <button
                            class="menu-button<?= $index === 0 ? ' active' : '' ?><?= !$acceso ? ' sin-acceso' : '' ?>"
                            type="button"
                            data-section="<?= htmlspecialchars($sectionId) ?>"
                            data-acceso="<?= $acceso ? '1' : '0' ?>"
                            <?= !$acceso ? 'title="Sin acceso a este módulo"' : '' ?>
                        >
                            <span class="menu-icon"><?= htmlspecialchars(mb_substr($item, 0, 1)) ?></span>
                            <span class="menu-label-wrap">
                                <span class="menu-label"><?= htmlspecialchars($item) ?></span>
                                <span class="menu-helper"><?= !$acceso ? 'Sin acceso' : '' ?></span>
                            </span>
                            <span class="menu-chevron">›</span>
                        </button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <nav class="navbar-custom">
            <p class="navbar-brand" id="navbarTitle">Dashboard</p>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <?php if ($es_owner): ?>
                    <span class="badge-owner">Owner</span>
                <?php else: ?>
                    <span class="badge-sesion"><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
                <?php endif; ?>
                <a href="sistema/logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </nav>

        <div class="container mt-4 mt-md-5">

            <!-- General -->
            <div class="content-section active" id="section-general">
                <div class="card-bienvenida">
                    <h1 class="titulo">¡Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>! 🌸</h1>
                    <p class="text-muted mt-2">Estás logueado/a correctamente.</p>
                    <hr style="border-color:#fce7f3;">
                    <p class="mb-1" style="font-size:.9rem;color:#6b7280;"><strong>Usuario:</strong> <?= htmlspecialchars($_SESSION['usuario_usuario']) ?></p>
                    <p class="mb-0" style="font-size:.9rem;color:#6b7280;"><strong>ID:</strong> <?= htmlspecialchars($_SESSION['usuario_id']) ?></p>
                    <?php if ($es_owner): ?>
                        <p class="mb-0 mt-1" style="font-size:.9rem;color:var(--rosa-oscuro);"><strong>Owner</strong> — acceso total</p>
                    <?php elseif ($permiso_descripcion): ?>
                        <p class="mb-0 mt-1" style="font-size:.9rem;color:#6b7280;">
                            <strong>Perfil:</strong> <?= htmlspecialchars($permiso_descripcion) ?>
                        </p>
                    <?php else: ?>
                        <p class="mb-0 mt-1" style="font-size:.9rem;color:#dc2626;"><strong>⚠ Perfil sin permisos asignados</strong></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Usuarios -->
            <div class="content-section" id="section-usuarios">
                <?php if (tieneAcceso('Usuarios', $permisos, $es_owner)): ?>
                <div class="config-card">
                    <div class="config-toolbar">
                        <h2>Usuarios</h2>
                        <div class="dropdown dropdown-acciones">
                            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown">⋯ Acciones</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item text-danger" type="button" id="btnBorrarUsuarios">🗑 Borrar seleccionados</button></li>
                            </ul>
                        </div>
                    </div>
                    <div id="usuariosTableWrap"><div class="spinner-rosa"></div></div>
                </div>
                <?php else: ?>
                <div class="acceso-denegado"><div class="icon"></div><h3>Acceso restringido</h3><p>No tenés permisos para ver esta sección.</p></div>
                <?php endif; ?>
            </div>

            <!-- Configuraciones -->
            <div class="content-section" id="section-configuraciones">
                <?php if (tieneAcceso('Configuraciones', $permisos, $es_owner)): ?>
                <div class="config-card">
                    <div class="config-toolbar">
                        <h2>Permisos</h2>
                        <button class="btn-nuevo" type="button" id="btnNuevoPermiso">＋ Nuevo</button>
                        <div class="dropdown dropdown-acciones">
                            <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown">⋯ Acciones</button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item text-danger" type="button" id="btnBorrarPermisos">🗑 Borrar seleccionados</button></li>
                            </ul>
                        </div>
                    </div>
                    <div id="permisosTableWrap"><div class="spinner-rosa"></div></div>
                </div>
                <?php else: ?>
                <div class="acceso-denegado"><div class="icon"></div><h3>Acceso restringido</h3><p>No tenés permisos para ver esta sección.</p></div>
                <?php endif; ?>
            </div>

            <!-- Resto de secciones -->
            <?php
            $otras = array_filter($menu_items, fn($i) => !in_array($i, ['General','Usuarios','Configuraciones']));
            foreach ($otras as $item):
                $sid    = 'section-' . strtolower(str_replace(' ', '-', $item));
                $acceso = tieneAcceso($item, $permisos, $es_owner);
            ?>
            <div class="content-section" id="<?= $sid ?>">
                <?php if ($acceso): ?>
                <div class="card-bienvenida">
                    <h2 class="titulo"><?= htmlspecialchars($item) ?></h2>
                    <p class="text-muted">Esta sección está en construcción.</p>
                </div>
                <?php else: ?>
                <div class="acceso-denegado"><div class="icon"></div><h3>Acceso restringido</h3><p>No tenés permisos para ver esta sección.</p></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

        </div>
    </main>
</div>

<!-- Modal: Nuevo permiso -->
<div class="modal fade" id="modalNuevoPermiso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo permiso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <label class="form-label fw-semibold">Nombre / Descripción</label>
                <input type="text" class="form-control" id="inputDescripcion" placeholder="Ej: Administrador, Recepcionista…" maxlength="100" />
                <div class="invalid-feedback" id="inputDescripcionError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-rosa" id="btnGuardarPermiso">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Editar usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="editUserId" />
                <input type="hidden" id="editUserEsOwner" />
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" class="form-control" id="editNombre" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Usuario</label>
                        <input type="text" class="form-control" id="editUsuario" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nueva contraseña <small class="text-muted">(vacío = no cambiar)</small></label>
                        <input type="password" class="form-control" id="editClave" placeholder="••••••" />
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Estado</label>
                        <select class="form-select" id="editActivo">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Perfil de permisos</label>
                        <select class="form-select" id="editPermisoId">
                            <option value="">Sin permiso</option>
                        </select>
                    </div>
                </div>
                <div id="modulosPreviewWrap" class="mt-4" style="display:none;">
                    <p class="fw-semibold mb-2" style="font-size:.9rem;">Módulos habilitados por este perfil:</p>
                    <div class="modulos-grid" id="modulosPreview"></div>
                </div>
                <div id="ownerNotice" class="mt-3 p-3 rounded-3" style="background:#fdf2f8;border:1px solid #fce7f3;display:none;">
                    <p class="mb-0" style="font-size:.88rem;color:var(--rosa-oscuro);">Este usuario es el <strong>Owner</strong>. Su perfil de permisos no puede modificarse y siempre tendrá acceso total.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-rosa" id="btnGuardarUsuario">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container-custom" id="toastContainer"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const SESSION_ES_OWNER = <?= json_encode($es_owner) ?>;
const SESSION_USER_ID  = <?= json_encode($_SESSION['usuario_id']) ?>;

// ── Sidebar ──────────────────────────────────
const sidebar         = document.getElementById('sidebar');
const sidebarToggle   = document.getElementById('sidebarToggle');
const sidebarMenuWrap = document.getElementById('sidebarMenuWrap');
function syncSidebarState() {
    if (window.innerWidth <= 991.98)
        sidebarMenuWrap.classList.toggle('collapsed-mobile', sidebar.classList.contains('collapsed'));
    else
        sidebarMenuWrap.classList.remove('collapsed-mobile');
}
sidebarToggle.addEventListener('click', () => { sidebar.classList.toggle('collapsed'); syncSidebarState(); });
window.addEventListener('resize', syncSidebarState);
syncSidebarState();

// ── Navegación SPA ────────────────────────────
const menuButtons = document.querySelectorAll('.menu-button');
const allSections = document.querySelectorAll('.content-section');
const navbarTitle = document.getElementById('navbarTitle');
let configuracionesCargadas = false;
let usuariosCargados        = false;
let listaPermisosCached     = [];

menuButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        if (btn.dataset.acceso === '0') return;
        menuButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        allSections.forEach(s => s.classList.remove('active'));
        const target = document.getElementById('section-' + btn.dataset.section);
        if (target) target.classList.add('active');
        navbarTitle.textContent = btn.querySelector('.menu-label').textContent;
        if (btn.dataset.section === 'configuraciones' && !configuracionesCargadas) cargarPermisos();
        if (btn.dataset.section === 'usuarios'        && !usuariosCargados)        cargarUsuarios();
    });
});

// ── Toast ─────────────────────────────────────
function showToast(msg, tipo = 'ok') {
    const c = document.getElementById('toastContainer');
    const el = document.createElement('div');
    el.className = 'toast-custom' + (tipo === 'error' ? ' error' : '');
    el.innerHTML = (tipo === 'ok' ? '✅' : '❌') + ' <span>' + escapeHtml(msg) + '</span>';
    c.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}
function escapeHtml(str) {
    const d = document.createElement('div');
    d.appendChild(document.createTextNode(String(str)));
    return d.innerHTML;
}

// ════════════════════════════════════════════════
// PERMISOS
// ════════════════════════════════════════════════
function renderTablaPermisos(lista) {
    listaPermisosCached = lista;
    const wrap = document.getElementById('permisosTableWrap');
    if (!lista || lista.length === 0) {
        wrap.innerHTML = `<div class="empty-state"><div class="empty-icon"></div><p>No hay permisos creados aún.</p><p style="margin-top:6px;font-size:.85rem;">Usá el botón <strong>Nuevo</strong> para agregar uno.</p></div>`;
        return;
    }
    const rows = lista.map(p => {
        const activos = Object.values(p.permisos || {}).filter(v => v == 1).length;
        const total   = Object.keys(p.permisos  || {}).length;
        return `<tr data-id="${p.id}">
            <td><input type="checkbox" class="perm-check" data-id="${p.id}"></td>
            <td>${escapeHtml(p.descripcion)}</td>
            <td><span class="badge-permisos">${activos} / ${total} módulos</span></td>
            <td style="color:var(--muted);font-size:.8rem;font-family:monospace;">${p.id.substring(0,8)}…</td>
        </tr>`;
    }).join('');
    wrap.innerHTML = `<table class="data-table">
        <thead><tr>
            <th><input type="checkbox" class="perm-check" id="checkAllPermisos"></th>
            <th>Descripción</th><th>Módulos</th><th>ID</th>
        </tr></thead><tbody>${rows}</tbody>
    </table>`;
    initCheckboxes('checkAllPermisos', '#permisosTableWrap .perm-check:not(#checkAllPermisos)');
}

function cargarPermisos() {
    document.getElementById('permisosTableWrap').innerHTML = '<div class="spinner-rosa"></div>';
    fetch('sistema/include/permisos/config_permisos.php?accion=listar')
        .then(r => r.json())
        .then(data => { configuracionesCargadas = true; renderTablaPermisos(data); })
        .catch(() => { document.getElementById('permisosTableWrap').innerHTML = '<div class="empty-state"><div class="empty-icon">⚠️</div><p>Error al cargar permisos.</p></div>'; });
}

const modalPermisoBS = new bootstrap.Modal(document.getElementById('modalNuevoPermiso'));
const inputDesc      = document.getElementById('inputDescripcion');
const btnGuardarPerm = document.getElementById('btnGuardarPermiso');

document.getElementById('btnNuevoPermiso').addEventListener('click', () => {
    inputDesc.value = ''; inputDesc.classList.remove('is-invalid');
    btnGuardarPerm.disabled = false; btnGuardarPerm.textContent = 'Guardar';
    modalPermisoBS.show(); setTimeout(() => inputDesc.focus(), 400);
});
inputDesc.addEventListener('keydown', e => { if (e.key === 'Enter') btnGuardarPerm.click(); });
btnGuardarPerm.addEventListener('click', () => {
    const desc = inputDesc.value.trim();
    if (!desc) { inputDesc.classList.add('is-invalid'); document.getElementById('inputDescripcionError').textContent = 'Requerido.'; return; }
    inputDesc.classList.remove('is-invalid');
    btnGuardarPerm.disabled = true; btnGuardarPerm.textContent = 'Guardando…';
    const f = new FormData(); f.append('accion','crear'); f.append('descripcion', desc);
    fetch('sistema/include/permisos/config_permisos.php', { method:'POST', body:f })
        .then(r => r.json()).then(d => {
            if (d.error) throw new Error(d.error);
            modalPermisoBS.hide(); showToast('Permiso "' + desc + '" creado.');
            configuracionesCargadas = false; cargarPermisos();
        }).catch(err => { showToast(err.message||'Error.','error'); btnGuardarPerm.disabled=false; btnGuardarPerm.textContent='Guardar'; });
});
document.getElementById('btnBorrarPermisos').addEventListener('click', () =>
    borrarSeleccionados('#permisosTableWrap .perm-check:not(#checkAllPermisos):checked',
        'sistema/include/permisos/config_permisos.php',
        () => { configuracionesCargadas = false; cargarPermisos(); })
);

// ════════════════════════════════════════════════
// USUARIOS
// ════════════════════════════════════════════════
function renderTablaUsuarios(lista) {
    const wrap = document.getElementById('usuariosTableWrap');
    if (!lista || lista.length === 0) {
        wrap.innerHTML = `<div class="empty-state"><div class="empty-icon">👤</div><p>No hay usuarios.</p></div>`;
        return;
    }
    const rows = lista.map(u => {
        const esOwner     = u.id == 1;
        const activoBadge = u.activo == 1 ? '<span class="badge-activo">Activo</span>' : '<span class="badge-inactivo">Inactivo</span>';
        let permBadge;
        if (esOwner) {
            permBadge = '<span class="badge-owner" style="font-size:.78rem;">👑 Owner</span>';
        } else if (u.permiso_id) {
            const perfil = listaPermisosCached.find(p => p.id === u.permiso_id);
            permBadge = '<span class="badge-permisos">' + escapeHtml(perfil ? perfil.descripcion : u.permiso_id.substring(0,8)+'…') + '</span>';
        } else {
            permBadge = '<span class="badge-sin-permiso">Sin permiso</span>';
        }
        const uEncoded = encodeURIComponent(JSON.stringify(u));
        return `<tr data-id="${u.id}">
            <td><input type="checkbox" class="perm-check" data-id="${u.id}"${esOwner ? ' disabled' : ''}></td>
            <td>${escapeHtml(u.nombre)}</td>
            <td style="color:var(--muted)">${escapeHtml(u.usuario)}</td>
            <td>${activoBadge}</td>
            <td>${permBadge}</td>
            <td>
                <button class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.8rem;"
                    onclick='abrirModalEditarUsuario(${JSON.stringify(u)})'>
                    ✏️ Editar
                </button>
            </td>
        </tr>`;
    }).join('');
    wrap.innerHTML = `<table class="data-table">
        <thead><tr>
            <th><input type="checkbox" class="perm-check" id="checkAllUsuarios"></th>
            <th>Nombre</th><th>Usuario</th><th>Estado</th><th>Perfil</th><th></th>
        </tr></thead><tbody>${rows}</tbody>
    </table>`;
    initCheckboxes('checkAllUsuarios', '#usuariosTableWrap .perm-check:not(#checkAllUsuarios):not(:disabled)');
}

function cargarUsuarios() {
    document.getElementById('usuariosTableWrap').innerHTML = '<div class="spinner-rosa"></div>';
    // Cargar permisos también si no están cargados (para mostrar nombres en la tabla)
    const promPermisos = listaPermisosCached.length > 0
        ? Promise.resolve(listaPermisosCached)
        : fetch('sistema/include/permisos/config_permisos.php?accion=listar').then(r => r.json()).then(d => { listaPermisosCached = d; return d; });

    promPermisos.then(() =>
        fetch('sistema/include/usuario/listar_usuarios.php').then(r => r.json())
    ).then(data => {
        usuariosCargados = true;
        renderTablaUsuarios(data);
    }).catch(() => {
        document.getElementById('usuariosTableWrap').innerHTML = '<div class="empty-state"><div class="empty-icon">⚠️</div><p>Error al cargar usuarios.</p></div>';
    });
}

const modalUsuarioBS = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));

function abrirModalEditarUsuario(u) {
    const esOwner = u.id == 1;
    document.getElementById('editUserId').value      = u.id;
    document.getElementById('editUserEsOwner').value = esOwner ? '1' : '0';
    document.getElementById('editNombre').value      = u.nombre;
    document.getElementById('editUsuario').value     = u.usuario;
    document.getElementById('editClave').value       = '';
    document.getElementById('editActivo').value      = u.activo;
    document.getElementById('editActivo').disabled   = esOwner;
    document.getElementById('editPermisoId').disabled = esOwner;
    document.getElementById('ownerNotice').style.display = esOwner ? 'block' : 'none';

    // Poblar select permisos
    const sel = document.getElementById('editPermisoId');
    sel.innerHTML = '<option value="">Sin permiso</option>';
    listaPermisosCached.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id; opt.textContent = p.descripcion;
        if (u.permiso_id === p.id) opt.selected = true;
        sel.appendChild(opt);
    });

    actualizarPreviewModulos(u.permiso_id);

    // Remover listener anterior y agregar nuevo
    const selNew = sel.cloneNode(true);
    sel.parentNode.replaceChild(selNew, sel);
    selNew.disabled = esOwner;
    selNew.addEventListener('change', function() { actualizarPreviewModulos(this.value); });

    modalUsuarioBS.show();
}

function actualizarPreviewModulos(permiso_id) {
    const wrap    = document.getElementById('modulosPreviewWrap');
    const preview = document.getElementById('modulosPreview');
    if (!permiso_id) { wrap.style.display = 'none'; return; }
    const perfil = listaPermisosCached.find(p => p.id === permiso_id);
    if (!perfil)  { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';
    preview.innerHTML  = Object.entries(perfil.permisos).map(([mod, val]) =>
        `<div class="modulo-item"><span>${escapeHtml(mod)}</span><span>${val == 1 ? '✅' : '❌'}</span></div>`
    ).join('');
}

document.getElementById('btnGuardarUsuario').addEventListener('click', () => {
    const id      = document.getElementById('editUserId').value;
    const esOwner = document.getElementById('editUserEsOwner').value === '1';
    const btn     = document.getElementById('btnGuardarUsuario');
    btn.disabled  = true; btn.textContent = 'Guardando…';

    // Leer el select aunque esté disabled (guardamos valor original)
    const permisoIdVal = esOwner ? 'owner' : document.getElementById('editPermisoId').value;

    const f = new FormData();
    f.append('id',         id);
    f.append('nombre',     document.getElementById('editNombre').value.trim());
    f.append('usuario',    document.getElementById('editUsuario').value.trim());
    f.append('clave',      document.getElementById('editClave').value);
    f.append('activo',     esOwner ? '1' : document.getElementById('editActivo').value);
    f.append('permiso_id', permisoIdVal);

    fetch('sistema/include/usuario/edicion_ajax.php', { method:'POST', body:f })
        .then(r => r.json()).then(d => {
            if (!d.ok) throw new Error(d.mensaje);
            modalUsuarioBS.hide();
            showToast(d.mensaje);
            usuariosCargados = false; cargarUsuarios();
        }).catch(err => showToast(err.message||'Error.','error'))
        .finally(() => { btn.disabled = false; btn.textContent = 'Guardar cambios'; });
});

document.getElementById('btnBorrarUsuarios').addEventListener('click', () =>
    borrarSeleccionados(
        '#usuariosTableWrap .perm-check:not(#checkAllUsuarios):not(:disabled):checked',
        'sistema/include/usuario/eliminar_usuario.php',
        () => { usuariosCargados = false; cargarUsuarios(); }
    )
);

// ════════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════════
function initCheckboxes(checkAllId, rowCheckSelector) {
    const checkAll = document.getElementById(checkAllId);
    if (!checkAll) return;
    checkAll.addEventListener('change', function() {
        document.querySelectorAll(rowCheckSelector).forEach(c => {
            c.checked = this.checked;
            c.closest('tr').classList.toggle('selected', this.checked);
        });
    });
    document.querySelectorAll(rowCheckSelector).forEach(c => {
        c.addEventListener('change', function() {
            this.closest('tr').classList.toggle('selected', this.checked);
        });
        c.closest('tr').addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
            if (c.disabled) return;
            c.checked = !c.checked;
            this.classList.toggle('selected', c.checked);
        });
    });
}

function borrarSeleccionados(checkSelector, endpoint, onSuccess) {
    const checks = document.querySelectorAll(checkSelector);
    if (checks.length === 0) { showToast('Seleccioná al menos uno para borrar.', 'error'); return; }
    const ids = Array.from(checks).map(c => c.dataset.id);
    if (!confirm(`¿Eliminar ${ids.length} registro(s)?`)) return;
    const f = new FormData(); f.append('accion','eliminar'); ids.forEach(id => f.append('ids[]', id));
    fetch(endpoint, { method:'POST', body:f })
        .then(r => r.json()).then(d => {
            if (d.error) throw new Error(d.error);
            showToast(`${d.eliminados} eliminado(s).`);
            onSuccess();
        }).catch(err => showToast(err.message||'Error.','error'));
}
</script>
</body>
</html>