<?php
session_start(); // Abrimos la sesión para poder destruirla
session_destroy(); // Destruimos todos los datos de la sesión
header('Location: ../login.html'); // Redirigimos al login
exit; // Cortamos la ejecución del script
?>