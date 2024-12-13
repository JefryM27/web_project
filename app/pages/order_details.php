<?php
include '../utils/database.php';
include '../public/shared/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php"); // Redirigir al inicio de sesión si no está autenticado
    exit();
}

$conn = get_mysql_connection();
$user_id = $_SESSION['user_id'];

if (!isset($_GET['order_id'])) {
    header('Location: orders.php');
    exit;
}

$order_id = $_GET['order_id'];

// Consulta para obtener los detalles del pedido
$query_order = "
    SELECT o.id, o.order_date, o.total_amount, o.status
    FROM orders o
    WHERE o.id = ? ";

// Añadir condición extra si el usuario no es administrador (user_id != 1)
if ($user_id != 1) {
    $query_order .= " AND o.user_id = ?";
}

// Preparar la consulta
$stmt_order = $conn->prepare($query_order);

// Verificar si el usuario es admin
if ($user_id == 1) {
    // Si es admin, sólo se usa el order_id
    $stmt_order->bind_param("i", $order_id);
} else {
    // Si no es admin, se usa tanto order_id como user_id
    $stmt_order->bind_param("ii", $order_id, $user_id);
}

$stmt_order->execute();
$order_result = $stmt_order->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    header('Location: orders.php');
    exit;
}

// Consulta para obtener los elementos del pedido
$query_items = "
    SELECT p.name, oi.quantity, oi.price, p.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?";

$stmt_items = $conn->prepare($query_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

$order_items = [];
while ($row = $items_result->fetch_assoc()) {
    $order_items[] = $row;
}

$stmt_order->close();
$stmt_items->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/ordersStyle.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="mb-4">Detalles del Pedido #<?php echo $order['id']; ?></h1>
        <div class="mb-3">
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($order['order_date'])); ?></p>
            <p><strong>Total:</strong> ₡<?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Estado:</strong> <?php echo $order['status']; ?></p>
        </div>
        <hr>
        <h4 class="mb-3">Productos:</h4>
        <?php if (count($order_items) > 0): ?>
            <div class="table-responsive" style="max-height: 374px; overflow-y: auto;">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td class="d-flex align-items-center">
                                    <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" class="me-3"
                                        style="width: 50px; height: 50px;">
                                    <?php echo $item['name']; ?>
                                </td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>₡<?php echo number_format($item['price'], 2); ?></td>
                                <td>₡<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="no-items">No se encontraron productos en este pedido.</p>
        <?php endif; ?>
        <div class="d-flex justify-content-end mt-4">
            <a href="orders.php" class="btn btn-secondary mb-4">Volver a Mis Pedidos</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>