<?php
session_start();

// Si no está logueada, redirigir al login
if (!isset($_SESSION['logueado']) || !$_SESSION['logueado']) {
    header('Location: login.html');
    exit;
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:wght@600&display=swap"
        rel="stylesheet" />
    <style>
        :root {
            --rosa: #f472b6;
            --rosa-oscuro: #db2777;
            --crema: #fff7f0;
        }

        body {
            background: var(--crema);
            font-family: 'DM Sans', sans-serif;
            background-image: radial-gradient(circle at 20% 20%, #fce7f3 0%, transparent 50%);
        }

        .navbar-custom {
            background: white;
            border-bottom: 1px solid #fce7f3;
            padding: 16px 32px;
        }

        .navbar-brand {
            font-family: 'Playfair Display', serif;
            color: #1e1e2e !important;
        }

        .btn-logout {
            background: linear-gradient(135deg, var(--rosa), var(--rosa-oscuro));
            color: white;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 500;
            font-size: 0.88rem;
            transition: all 0.2s;
        }

        .btn-logout:hover {
            opacity: 0.85;
            color: white;
        }

        .card-bienvenida {
            background: white;
            border-radius: 24px;
            padding: 40px;
            border: 1px solid #fce7f3;
            box-shadow: 0 8px 40px rgba(244, 114, 182, 0.1);
            animation: fadeUp 0.5s ease;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .titulo {
            font-family: 'Playfair Display', serif;
            color: #1e1e2e;
        }

        .badge-sesion {
            background: #fce7f3;
            color: var(--rosa-oscuro);
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 0.8rem;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <nav class="navbar-custom d-flex justify-content-between align-items-center">
        <span class="navbar-brand">✦ Mi sistema</span>
        <div class="d-flex align-items-center gap-3">
            <span class="badge-sesion"> <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></span>
            <a href="sistema/logout.php" class="btn-logout">Cerrar sesión</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card-bienvenida">
            <h1 class="titulo">¡Hola, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>! 🌸</h1>
            <p class="text-muted mt-2">Estás logueado/a correctamente. Tu sesión está activa.</p>
            <hr style="border-color:#fce7f3;">
            <p class="mb-1" style="font-size:0.9rem;color:#6b7280;">
                <strong>Usuario:</strong> <?= htmlspecialchars($_SESSION['usuario_usuario']) ?>
            </p>
            <p class="mb-0" style="font-size:0.9rem;color:#6b7280;">
                <strong>ID:</strong> <?= htmlspecialchars($_SESSION['usuario_id']) ?>
            </p>
        </div>
    </div>
</body>

</html>