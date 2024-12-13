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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_data'])) {
    $cart = json_decode($_POST['cart_data'], true);
    $_SESSION['cart'] = $cart;
} elseif (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
} else {
    $cart = [];
}

// Función para calcular el subtotal
function calcularSubtotal($cart)
{
    $subtotal = 0;
    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    return $subtotal;
}

$subtotalColones = calcularSubtotal($cart);
$tasaIva = 0.13;  // Tasa de IVA en Costa Rica
$iva = $subtotalColones * $tasaIva;
$costoEnvio = rand(900, 1900); // Costo de envío aleatorio

$totalColones = $subtotalColones + $iva + $costoEnvio;
$tasaCambio = 0.0019;  // Tasa de cambio de colones a dólares
$totalDolares = $totalColones * $tasaCambio;

// Procesar el pago con PayPal y luego guardar el pedido en la base de datos
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'complete') {
    $conn->begin_transaction();

    try {
        // Insertar la orden en la tabla `orders`
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
        $status = 'Completado';
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

        echo "<script>alert('Compra completada exitosamente.'); window.location.href = 'bill.php?order_id=$order_id';</script>";

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
    <link href="../public/css/ordersStyle.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1>Resumen</h1>
        <div class="row">
            <div class="col-md-8">
                <div class="table-responsive mt-4" style="max-height: 374px; overflow-y: auto;">
                    <table class="table table-bordered table-striped">
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
                                    <td class="d-flex align-items-center">
                                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>"
                                            style="max-width: 50px; max-height: 50px;" class="me-3">
                                        <?php echo $item['name']; ?>
                                    </td>
                                    <td>₡<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>₡<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4>Total a Pagar</h4>
                        <p>Subtotal: ₡<?php echo number_format($subtotalColones, 2); ?></p>
                        <p>IVA: ₡<?php echo number_format($iva, 2); ?></p>
                        <p>Costo de Envío: ₡<?php echo number_format($costoEnvio, 2); ?></p>
                        <p>Total: ₡<?php echo number_format($totalColones, 2); ?></p>
                        <p>Total en dólares: $<?php echo number_format($totalDolares, 2); ?></p>
                        <div class="paypal-button-container" id="paypal-button"></div>
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
                    window.location.href = 'process_payment.php?action=complete';
                });
            }
        }).render('#paypal-button');
    </script>
</body>

</html>