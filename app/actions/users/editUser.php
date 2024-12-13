<?php 
include '../../utils/database.php';
include '../../public/shared/headerEditUser.html';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../index.php"); // Redirigir al inicio de sesión si no está autenticado
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = get_mysql_connection();
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = htmlspecialchars(trim($_POST['name']));
    $new_email = htmlspecialchars(trim($_POST['email']));
    $new_password = htmlspecialchars(trim($_POST['password']));

    $update_sql = "UPDATE users SET name = ?, email = ?";
    $types = "ss";
    $params = [$new_name, $new_email];

    if (!empty($new_password)) {
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $update_sql .= ", password_hash = ?";
        $types .= "s";
        $params[] = $password_hash;
    }

    $update_sql .= " WHERE id = ?";
    $types .= "i";
    $params[] = $user_id;

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Perfil actualizado exitosamente.";
        $_SESSION['user_name'] = $new_name;
        header("Location:../../pages/profile.php"); // Redirigir a profile.php después de actualizar
        exit;
    } else {
        $_SESSION['error_message'] = "Error al actualizar el perfil.";
    }

    $stmt->close();
}

$conn->close();