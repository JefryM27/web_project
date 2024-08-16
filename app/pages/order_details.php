<?php
session_start();
include '../utils/database.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
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
    WHERE o.id = ? AND o.user_id = ?
";

$stmt_order = $conn->prepare($query_order);
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();
$order = $order_result->fetch_assoc();

if (!$order) {
    header('Location: my_orders.php');
    exit;
}

// Consulta para obtener los elementos del pedido
$query_items = "
    SELECT p.name, oi.quantity, oi.price, p.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
";

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
</head>

<body>
    <div class="container mt-4">
        <h1>Detalles del Pedido #<?php echo $order['id']; ?></h1>
        <p>Fecha: <?php echo $order['order_date']; ?></p>
        <p>Total: ₡<?php echo number_format($order['total_amount'], 2); ?></p>
        <p>Estado: <?php echo $order['status']; ?></p>
        <hr>
        <h4>Productos:</h4>
        <?php if (count($order_items) > 0): ?>
            <table class="table table-bordered">
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
                            <td>
                                <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>"
                                    style="max-width: 50px; max-height: 50px;">
                                <?php echo $item['name']; ?>
                            </td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₡<?php echo number_format($item['price'], 2); ?></td>
                            <td>₡<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron productos en este pedido.</p>
        <?php endif; ?>
        <a href="orders.php" class="btn btn-secondary">Volver a Mis Pedidos</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>