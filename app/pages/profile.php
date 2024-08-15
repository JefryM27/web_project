<?php include '../public/shared/headerProfile.html'; ?>
<?php
session_start();

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../utils/database.php';

$user_id = $_SESSION['user_id'];

$conn = get_mysql_connection();
$sql = "SELECT name, email, created_at, updated_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $created_at, $updated_at);
$stmt->fetch();
$stmt->close();

$conn->close();

// Verifica si hay mensajes de éxito o error
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;

// Limpiar los mensajes después de usarlos
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="../public/css/profileStyle.css">
    <!-- Añadir Bootstrap para manejo de modales -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <!-- Información del perfil -->
    <div class="profile-info">
        <label for="name">Nombre:</label>
        <p id="name"><?php echo htmlspecialchars($name); ?></p>

        <label for="email">Correo Electrónico:</label>
        <p id="email"><?php echo htmlspecialchars($email); ?></p>

        <label for="password">Contraseña:</label>
        <!-- Campo de contraseña con puntitos -->
        <input type="password" id="password" value="••••••••••" readonly>

        <label for="created_at">Fecha de Creación:</label>
        <p id="created_at"><?php echo htmlspecialchars($created_at); ?></p>

        <label for="updated_at">Última Actualización:</label>
        <p id="updated_at"><?php echo htmlspecialchars($updated_at); ?></p>
    </div>

    <!-- Botón para abrir el modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
        Editar Perfil
    </button>

    <!-- Mensajes de éxito/error -->
    <?php if ($success_message): ?>
        <div class="alert alert-success mt-3">
            <?php echo $success_message; ?>
        </div>
    <?php elseif ($error_message): ?>
        <div class="alert alert-danger mt-3">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario de edición del perfil -->
                <form id="editProfileForm" action="../actions/users/editUser.php" method="POST">
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

                    <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Añadir scripts de Bootstrap y jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        <?php if ($success_message): ?>
            $('#editProfileModal').modal('hide'); // Cierra el modal
            alert('<?php echo $success_message; ?>'); // Muestra un mensaje de éxito
        <?php endif; ?>
    });
</script>

</body>
</html>
