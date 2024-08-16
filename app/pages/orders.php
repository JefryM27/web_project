<?php
include '../utils/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = get_mysql_connection();
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Redirigir al inicio de sesión si no está autenticado
    exit();
}

$user_id = $_SESSION['user_id'];

// Consulta para obtener todos los pedidos del usuario
$query = "
    SELECT o.id, o.order_date, o.total_amount, o.status
    FROM orders o
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1>Mis Pedidos</h1>
        <?php if (count($orders) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID de Pedido</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo $order['order_date']; ?></td>
                            <td>₡<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td><?php echo $order['status']; ?></td>
                            <td>
                                <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="btn btn-info btn-sm">Ver
                                    Detalles</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes pedidos realizados.</p>
        <?php endif; ?>
        <a href="dashboard.php" class="btn btn-secondary">Volver al dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>