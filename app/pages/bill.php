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

// Obtener el ID de la orden desde la URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Consulta para obtener los detalles de la orden y del usuario
    $query_order = "
        SELECT o.id, o.order_date, o.total_amount, o.status, u.name, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ? ";

    // Añadir la condición adicional sólo si el usuario no es administrador (user_id != 1)
    if ($user_id != 1) {
        $query_order .= " AND o.user_id = ?";
    }

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
        echo "No se encontró la orden.";
        exit();
    }

    // Consulta para obtener los detalles de los productos en la orden
    $query_items = "
        SELECT p.name, oi.quantity, oi.price
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
} else {
    echo "No se ha especificado ninguna orden.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Pedido #<?php echo $order['id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../public/css/bill.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="invoice-container">
            <div class="invoice-header d-flex justify-content-between align-items-center">
                <h1>Factura</h1>
                <img src="../public/img/mainLogo.png" alt="Logo de la Tienda">
            </div>

            <div class="invoice-details mb-4">
                <p><strong>Nombre del Cliente:</strong> <?php echo $order['name']; ?></p>
                <p><strong>Correo del Cliente:</strong> <?php echo $order['email']; ?></p>
                <p><strong>Número Consecutivo:</strong> <?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></p>
                <p><strong>Fecha:</strong> <?php echo $order['order_date']; ?></p>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre del Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                        <th>IVA (15%)</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($order_items as $item):
                        $subtotal = $item['quantity'] * $item['price'];
                        $iva = $subtotal * 0.15;
                        $total_producto = $subtotal + $iva;
                        $total += $total_producto;
                        ?>
                        <tr>
                            <td><?php echo $item['name']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₡<?php echo number_format($item['price'], 2); ?></td>
                            <td>₡<?php echo number_format($subtotal, 2); ?></td>
                            <td>₡<?php echo number_format($iva, 2); ?></td>
                            <td>₡<?php echo number_format($total_producto, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <th colspan="5">Total a Pagar:</th>
                        <th>₡<?php echo number_format($total, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
            <p class="text-center">Disfrute su pedido!</p>
            <p class="text-center">Dirección Física de la Tienda: Costado Sur del Parque Santa Rosa Pocosol, San Carlos</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>

