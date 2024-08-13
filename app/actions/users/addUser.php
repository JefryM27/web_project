<?php
session_start(); // Iniciar la sesión para usar variables de sesión

// Incluye el archivo de conexión a la base de datos
require_once __DIR__ . '/../../utils/database.php';

// Función para sanitizar entradas del usuario
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "El formato del correo electrónico es inválido.";
    } elseif (strlen($password) < 8) {
        $_SESSION['error_message'] = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        try {
            $conn = get_mysql_connection();

            $sql = "SELECT id FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $_SESSION['error_message'] = "El correo electrónico ya está registrado.";
            } else {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $sql = "INSERT INTO users (name, email, password_hash, created_at) VALUES (?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $name, $email, $password_hash);

                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Registro exitoso.";
                } else {
                    $_SESSION['error_message'] = "Error al registrar el usuario. Por favor, intente nuevamente.";
                }
            }
            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error en la conexión a la base de datos: " . $e->getMessage();
        }
    }

    // Redirigir de vuelta a index.php para mostrar el mensaje
    header("Location:  /../../index.php");
    exit;
}
?>
