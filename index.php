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
    <link rel="stylesheet" href="app/public/css/loginStyle.css">
    <style>
        .alert {
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            max-width: 500px;
            animation: fadeInOut 5s forwards;
        }

        /* Animación para desvanecer el mensaje */
        @keyframes fadeInOut {
            0% { opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; }
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
            <form class="sign-in" action="app/actions/users/authenticadLogin.php" method="POST">
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
            <form id="registerForm" class="sign-up" action="app/actions/users/addUser.php" method="POST">
                <h2>Registrarse</h2>
                <span>Use su correo electrónico para registrarse</span>
                <div class="container-input">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="name" id="name" placeholder="Nombre" required>
                </div>
                <div class="container-input">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="text" name="email" id="email" placeholder="Email" required>
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" name="password" id="password" placeholder="Password" required>
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

    <script src="app/public/js/loginScript.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- Validaciones en JavaScript -->
    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            let validForm = true;
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            // Validar el nombre (solo letras y espacios)
            if (!name.match(/^[a-zA-Z\s]+$/)) {
                alert("El nombre debe contener solo letras y espacios.");
                validForm = false;
            }

            // Validar el formato del correo electrónico
            if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                alert("El correo electrónico no es válido.");
                validForm = false;
            }

            // Validar la longitud de la contraseña
            if (password.length < 8) {
                alert("La contraseña debe tener al menos 8 caracteres.");
                validForm = false;
            }

            if (!validForm) {
                event.preventDefault(); // Detener el envío del formulario si no es válido
            }
        });
    </script>

</body>

</html>
