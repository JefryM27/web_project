<?php
include '../../utils/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sanitizar entradas del usuario
function sanitizeInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    // Validaciones
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Todos los campos son obligatorios.";
        header("Location: ../../index.php");
        exit;
    }

    $conn = get_mysql_connection();

    // Preparar la consulta SQL
    $sql = "SELECT id, name, password_hash FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Verificar si el usuario existe
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $user_name, $hashed_password);
            $stmt->fetch();

            // Verificar la contraseña
            if (password_verify($password, $hashed_password)) {
                // Iniciar sesión
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $user_name;

                if ($user_id == 1) {
                    header("Location: ../../pages/orders.php");
                } else {
                    header("Location: ../../pages/dashboard.php");
                }
                exit;
            } else {
                $_SESSION['error_message'] = "Contraseña incorrecta.";
                header("Location: ../../index.php");
                exit;
            }
        } else {
            $_SESSION['error_message'] = "No existe una cuenta con ese correo electrónico.";
            header("Location: ../../index.php");
            exit;
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error en la preparación de la consulta.";
        header("Location: ../../index.php");
        exit;
    }

    // Cerrar la conexión
    $conn->close();
} else {
    header("Location: ../../index.php");
    exit;
}
?>