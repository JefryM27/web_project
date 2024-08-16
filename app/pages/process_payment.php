<?php include '../public/shared/header.php'; ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../utils/database.php'; // Incluye la conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Redirigir al inicio de sesión si no está autenticado
    exit();
}
$conn = get_mysql_connection();
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_data'])) {
    $cart = json_decode($_POST['cart_data'], true);
    $_SESSION['cart'] = $cart;
} elseif (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
} else {
    $cart = [];
}

// Función para calcular el total
function calcularTotal($cart)
{
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

$totalColones = calcularTotal($cart);
$tasaCambio = 0.0019;  // Tasa de cambio de colones a dólares
$totalDolares = $totalColones * $tasaCambio;

// Procesar el pago con PayPal y luego guardar el pedido en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'complete') {
    $conn->begin_transaction();

    try {
        // Insertar la orden en la tabla `orders`
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
        $status = 'Pendiente'; // o 'Completado' según el flujo de tu aplicación
        $stmt->bind_param('ids', $user_id, $totalColones, $status);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insertar los productos en la tabla order_items
        foreach ($cart as $item) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('iiid', $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();

        // Limpiar el carrito después de completar la compra
        unset($_SESSION['cart']);
        session_write_close();

        echo "<script>alert('Compra completada exitosamente.'); window.location.href = 'dashboard.php';</script>";

    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error al procesar la compra. Por favor, intenta nuevamente.'); window.location.href = 'process_payment.php';</script>";
    }

    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/dashboardStyle.css">
</head>

<body>
    <div class="container mt-4">
        <h1>Resumen</h1>
        <div class="row">
            <div class="col-md-8">
                <table class="table table-responsive table-bordered">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart as $productId => $item): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>"
                                        style="max-width: 50px; max-height: 50px;">
                                    <?php echo $item['name']; ?>
                                </td>
                                <td>₡<?php echo number_format($item['price'], 2); ?></td>
                                <td>
                                    <div class="align-items-center">
                                        <span class="mx-2"><?php echo $item['quantity']; ?></span>
                                    </div>
                                </td>
                                <td>₡<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4>Total a Pagar</h4>
                        <p>Subtotal: ₡<?php echo number_format($totalColones, 2); ?></p>
                        <p>Total en dólares: $<?php echo number_format($totalDolares, 2); ?></p>
                        <a class="confirm-button" id="paypal-button"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script
        src="https://www.paypal.com/sdk/js?client-id=AaYQaAg6giHjDJitK2rnfQm66cm-ZjIouC2BXMPdxvzs_zb4jLfc2bnxwqm1R5g4w-6au0-CfvnaHEo_&currency=USD"></script>
    <script src="../public/js/dashboardScript.js"></script>
    <script>
        // PayPal
        paypal.Buttons({
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo number_format($totalDolares, 2); ?>'
                        }
                    }]
                });
            },
            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    alert('Compra completada por ' + details.payer.name.given_name);

                    // Aquí podrías redirigir a un script que procese la compra
                    window.location.href = 'process_payment.php?action=complete';
                });
            }
        }).render('#paypal-button');
    </script>
</body>

</html>