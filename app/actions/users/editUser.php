<?php include '../../public/shared/headerEditUser.html'; ?>
<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Incluye el archivo de conexión a la base de datos
require_once '../../utils/database.php';

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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="../../public/css/profileStyle.css">
</head>
<body>

<div class="container">

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="editUser.php" method="POST">
        <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña (Opcional)</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Nueva contraseña">
        </div>

        <button type="submit" class="btn">Actualizar Perfil</button>
    </form>
</div>
</body>
</html>
