<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous" />
</head>

<body class="d-flex justify-content-center align-items-center vh-100">
  <div style="
        border: 1px solid #ccc;
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
        border-radius: 10px;
      " class="container">
    <h1 class="mt-5">Iniciar secion</h1>
    <form method="post" action="sistema/login.php"> <!-- enviamos los datos del formulario a login.php -->
      <div class="mb-3">
        <label for="usuario" class="form-label">Usuario</label> <!-- obtenemos el usuario del formulario -->
        <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Ingrese su usuario" required />
      </div>

      <div class="mb-3">
        <label for="clave" class="form-label">Clave</label> <!-- obtenemos la clave del formulario -->
        <input type="password" id="clave" name="clave" class="form-control"
          placeholder="Ingrese su clave" required />
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" id="recordarme" class="form-check-input" />
        <label for="recordarme" class="form-check-label">Recordarme</label>
      </div>

      <div class="mb-3">
        <a href="#" class="d-block">Olvidaste tu clave?</a>
      </div>

      <button class="btn btn-primary w-100 mb-3">Login</button>

      <h6>
        No tienes una cuenta?
        <a href="#">Registrate aquí</a>
      </h6>
    </form>
  </div>
</body>

</html>