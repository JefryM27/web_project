<?php
include '../utils/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Redirigir al inicio de sesión si no está autenticado
    exit();
}

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../public/css/profileStyle.css">
    <link rel="stylesheet" href="../public/css/headerProfile.css">
</head>

<body>

    <!-- Incluye el header aquí -->
    <?php include '../public/shared/headerProfile.html'; ?>

    <div class="container" style="margin-top: 50px;">
        <!-- Información del perfil -->
        <div class="profile-info">
            <label for="name">Nombre:</label>
            <p class="mb-3" id="name"><?php echo htmlspecialchars($name); ?></p>

            <label for="email">Correo Electrónico:</label>
            <p class="mb-3" id="email"><?php echo htmlspecialchars($email); ?></p>

            <label for="password">Contraseña:</label>
            <input class="mb-3" type="password" id="password" value="••••••••••" readonly>

            <label for="created_at">Fecha de Creación:</label>
            <p class="mb-3" id="created_at"><?php echo htmlspecialchars($created_at); ?></p>

            <label for="updated_at">Última Actualización:</label>
            <p class="mb-3" id="updated_at"><?php echo htmlspecialchars($updated_at); ?></p>
        </div>

        <!-- Botón para abrir el modal -->
        <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#editProfileModal">
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
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Editar Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm" action="../actions/users/editUser.php" method="POST">
                        <div class="mb-3">
                            <label for="modalName" class="form-label">Nombre</label>
                            <input type="text" name="name" id="modalName" class="form-control"
                                value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalEmail" class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" id="modalEmail" class="form-control"
                                value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="modalPassword" class="form-label">Contraseña (Opcional)</label>
                            <input type="password" name="password" id="modalPassword" class="form-control"
                                placeholder="Nueva contraseña">
                        </div>
                        <button type="submit" class="btn btn-modal">Actualizar Perfil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Validaciones en JavaScript -->
    <script>
        document.getElementById('editProfileForm').addEventListener('submit', function(event) {
            let validForm = true;
            const name = document.getElementById('modalName').value.trim();
            const email = document.getElementById('modalEmail').value.trim();
            const password = document.getElementById('modalPassword').value.trim();

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

            // Validar la longitud de la contraseña si se proporciona
            if (password.length > 0 && password.length < 8) {
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

