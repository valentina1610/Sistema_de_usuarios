<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous" />
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
  <div style="border: 1px solid #ccc; max-width: 400px; margin: 50px auto; padding: 20px; border-radius: 10px;"
    class="container">
    <h1 class="mt-5">Registro</h1>
    <form method="post" action="sistema/crear_cuenta.php">
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre completo</label>
        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ingrese su nombre y apellido"
          required />
      </div>

      <div class="mb-3">
        <label for="usuario" class="form-label">Usuario</label>
        <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Ingrese su usuario" required />
      </div>

      <div class="mb-3">
        <label for="clave" class="form-label">Clave</label>
        <input type="password" id="clave" name="clave" class="form-control"
          placeholder="Ingrese su clave" required />
      </div>

      <div class="mb-3">
        <label for="confirmarClave" class="form-label">Confirmar clave</label>
        <input type="password" id="confirmarClave" name="confirmarClave" class="form-control"
          placeholder="Confirme su clave" required />
      </div>
      <button class="btn btn-primary w-100 mb-3">Registrarme</button>
    </form>
  </div>
</body>

</html>