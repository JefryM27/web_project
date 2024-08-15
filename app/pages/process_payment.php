<?php include '../public/shared/header.html'; ?>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_data'])) {
    $cart = json_decode($_POST['cart_data'], true);
    $_SESSION['cart'] = $cart;
} elseif (isset($_SESSION['cart'])) {
    $cart = $_SESSION['cart'];
} else {
    $cart = [];
}
// Función para calcular el total
function calcularTotal($cart) {
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

$totalColones = calcularTotal($cart);
$tasaCambio = 0.0019;  // Tasa de cambio de colones a dólares
$totalDolares = $totalColones * $tasaCambio;
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
                                    <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" style="max-width: 50px; max-height: 50px;">
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

    <script src="https://www.paypal.com/sdk/js?client-id=AaYQaAg6giHjDJitK2rnfQm66cm-ZjIouC2BXMPdxvzs_zb4jLfc2bnxwqm1R5g4w-6au0-CfvnaHEo_&currency=USD"></script>
    <script src="../public/js/dashboardScript.js"></script>
    <script>
        // PayPal
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo number_format($totalDolares, 2); ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    alert('Compra completada por ' + details.payer.name.given_name);
                    // una página de confirmación
                });
            }
        }).render('#paypal-button');
    </script>
</body>

</html>