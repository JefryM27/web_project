<?php 
include '../public/shared/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Redirigir al inicio de sesión si no está autenticado
    exit();
}

// Leer el archivo JSON
$productos = json_decode(file_get_contents('../utils/productos.json'), true);

// Obtener categorías y subcategorías únicas
$categorias = [];
$subcategorias = [];
foreach ($productos as $producto) {
    $categorias[$producto['category']] = $producto['category'];
    if (!isset($subcategorias[$producto['category']])) {
        $subcategorias[$producto['category']] = [];
    }
    $subcategorias[$producto['category']][] = $producto['sub_category'];
}

// Asegurarse de que las subcategorías sean únicas para cada categoría
foreach ($subcategorias as $categoria => $subs) {
    $subcategorias[$categoria] = array_unique($subs);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtrar Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/dashboardStyle.css">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar de Filtros -->
            <div class="col-md-3">
                <h4>Filtrado Por</h4>
                <hr>
                <h5>Categoría</h5>
                <?php foreach ($categorias as $categoria): ?>
                    <div class="form-check">
                        <input class="form-check-input categoria-checkbox" type="checkbox" value="<?php echo $categoria; ?>"
                            id="categoria-<?php echo $categoria; ?>">
                        <label class="form-check-label" for="categoria-<?php echo $categoria; ?>">
                            <?php echo $categoria; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                <hr>
                <h5>Subcategoría</h5>
                <?php foreach ($subcategorias as $categoria => $subs): ?>
                    <div class="subcategoria-container" id="subcategorias-<?php echo $categoria; ?>">
                        <?php foreach ($subs as $subcategoria): ?>
                            <div class="form-check">
                                <input class="form-check-input subcategoria-checkbox" type="checkbox"
                                    value="<?php echo $subcategoria; ?>" id="subcategoria-<?php echo $subcategoria; ?>">
                                <label class="form-check-label" for="subcategoria-<?php echo $subcategoria; ?>">
                                    <?php echo $subcategoria; ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Sección de Productos -->
            <div class="col">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 id="product-count"><?php echo count($productos); ?> productos</h4>
                </div>
                <div class="row" id="product-container">
                    <?php foreach ($productos as $producto): ?>
                        <div class="col-md-3 justify-content-center">
                            <div class="product-card" data-id="<?php echo $producto['id']; ?>"
                                data-category="<?php echo $producto['category']; ?>"
                                data-subcategory="<?php echo $producto['sub_category']; ?>"
                                data-name="<?php echo strtolower($producto['name']); ?>"
                                data-price="<?php echo $producto['price']; ?>">
                                <img src="<?php echo $producto['image_url']; ?>" class="product-img"
                                    alt="<?php echo $producto['name']; ?>">
                                <div class="add-button" onclick="toggleSpinner(this)">+ Agregar</div>
                                <div class="spinner-container" style="display:none;">
                                    <button class="btn btn-light btn-spinner" onclick="decrementCount(this)">-</button>
                                    <span class="spinner-value">1</span>
                                    <button class="btn btn-light btn-spinner" onclick="incrementCount(this)">+</button>
                                </div>
                                <div class="product-price mt-4">₡<span class="product-price-value"
                                        data-base-price="<?php echo number_format($producto['price'], 2); ?>"><?php echo number_format($producto['price'], 2); ?></span>
                                </div>
                                <div class="product-title mt-4"><?php echo $producto['name']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal del Carrito -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Mi carrito</h5>
                    <button type="button" class="btn-close" id="close-cart" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cart-items" class="cart-items-container"></div>
                    <div class="d-flex justify-content-between mt-4">
                        <h5>Subtotal</h5>
                        <h5>₡<span id="cart-total-modal">0.00</span></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="cart-form" action="process_payment.php" method="post">
                        <input type="hidden" name="cart_data" id="cart-data">
                        <button type="submit" class="custom-confirm-btn" id="confirm-purchase">Continuar con la
                            compra</button>
                    </form>
                    <button type="button" class="custom-clear-btn" id="clear-cart">Vaciar carrito</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="../public/js/dashboardScript.js"></script>
    <?php include '../public/shared/footer.html'; ?>

</body>

</html>