<?php
session_start(); // Iniciar la sesión para manejar los mensajes

// Mostrar mensajes de éxito o error si existen
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']); // Eliminar el mensaje después de mostrarlo
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']); // Eliminar el mensaje después de mostrarlo
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio de Sesión</title>
    <link rel="stylesheet" href="public/css/loginStyle.css">
    <style>
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }

        .alert-success {
            background-color: #4CAF50;
            color: white;
        }

        .alert-danger {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="container-form">
            <form class="sign-in" action="/app/actions/users/authenticadLogin.php" method="POST">
                <h2>Iniciar Sesión</h2>
                <span>Use su correo y contraseña</span>
                <div class="container-input">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="text" name="email" placeholder="Email" required>
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <a href="#">¿Olvidaste tu contraseña?</a>
                <button class="button" type="submit">INICIAR SESIÓN</button>
            </form>
        </div>

        <div class="container-form">
            <form class="sign-up" action="addUser.php" method="POST">
                <h2>Registrarse</h2>
                <span>Use su correo electrónico para registrarse</span>
                <div class="container-input">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="name" placeholder="Nombre" required>
                </div>
                <div class="container-input">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="text" name="email" placeholder="Email" required>
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button class="button" type="submit">REGISTRARSE</button>
            </form>
        </div>

        <div class="container-welcome">
            <div class="welcome-sign-up welcome">
                <h3>¡Bienvenido!</h3>
                <p>Ingrese sus datos personales para usar todas las funciones del sitio</p>
                <button class="button" id="btn-sign-up">Registrarse</button>
            </div>
            <div class="welcome-sign-in welcome">
                <h3>¡Hola!</h3>
                <p>Regístrese con sus datos personales para usar todas las funciones del sitio</p>
                <button class="button" id="btn-sign-in">Iniciar Sesión</button>
            </div>
        </div>

    </div>

    <script src="public/js/loginScript.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>
