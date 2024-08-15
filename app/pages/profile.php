<?php include '../public/shared/headerProfile.html'; ?>
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
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            padding: 20px;
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }

        .profile-info p {
            margin: 10px 0;
            font-size: 18px;
            color: #555;
        }

        .profile-info p strong {
            color: #333;
        }

        .btn {
            display: inline-block;
            width: 100%;
            padding: 10px;
            text-align: center;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Mi Perfil</h2>

    <!-- Información del perfil -->
    <div class="profile-info">
        <p><strong>Nombre:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Correo Electrónico:</strong> <?php echo htmlspecialchars($email); ?></p>
    </div>

    <a href="../actions/users/editUser.php" class="btn">Editar Perfil</a>
</div>
<?php include '../public/shared/footer.html'; ?>
</body>
</html>
