<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Incluye el archivo de conexión a la base de datos
require_once '/Jefry/OneDrive/Desktop/2.UTN/2.Cuatrimestre/Web/Proyecto/web_project/app/utils/database.php';

$user_id = $_SESSION['user_id'];

$conn = get_mysql_connection();
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../public/css/profileStyle.css">
</head>
<body>

<div class="container">
    <h2>Mi Perfil</h2>

    <div class="profile-info">
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($email); ?></p>
    </div>

    <a href="edit.php" class="btn">Editar Perfil</a>
</div>

</body>
</html>
