<?php
include '../utils/database.php';
include '../public/shared/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Redirigir al inicio de sesión si no está autenticado
    exit();
}

$conn = get_mysql_connection();
$user_id = $_SESSION['user_id'];

if ($user_id == 1) {
    // Consulta para obtener todas las órdenes si el usuario es administrador
    $query = "
        SELECT o.id, o.order_date, o.total_amount, o.status, u.name as user_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.order_date DESC
    ";
    $stmt = $conn->prepare($query);
} else {
    // Consulta para obtener las órdenes del usuario normal
    $query = "
        SELECT o.id, o.order_date, o.total_amount, o.status
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
}

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
    <title><?php echo $user_id == 1 ? 'Todas las Órdenes' : 'Mis Pedidos'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/ordersStyle.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1><?php echo $user_id == 1 ? 'Todas las Órdenes' : 'Mis Pedidos'; ?></h1>
        <?php if (count($orders) > 0): ?>
            <div class="table-responsive mt-4" style="max-height: 374px; overflow-y: auto;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <?php if ($user_id == 1): ?>
                                <th>Usuario</th>
                            <?php endif; ?>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <?php if ($user_id == 1): ?>
                                    <td><?php echo $order['user_name']; ?></td>
                                <?php endif; ?>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                <td>₡<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $order['status'] == 'Completado' ? 'success' : 'warning'; ?>">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order_details.php?order_id=<?php echo $order['id']; ?>"
                                        class="btn btn-info btn-sm">Ver Detalles</a>
                                    <a href="bill.php?order_id=<?php echo $order['id']; ?>" class="btn btn-secondary btn-sm">Ver
                                        Factura</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-orders">No hay pedidos disponibles.</p>
        <?php endif; ?>
        <div class="d-flex justify-content-end mt-4">
            <a href="<?php echo $user_id == 1 ? 'generate_report.php' : 'dashboard.php'; ?>" class="btn btn-danger">
                <?php echo $user_id == 1 ? 'Generar Reportes de Ventas' : 'Volver al dashboard'; ?>
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>
