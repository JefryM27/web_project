<?php
session_start();

// Verificar si se ha enviado la solicitud de cierre de sesión
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../index.php");
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
    <div class="header">
        <div class="header-left">
            <form method="post" action="">
                <button type="submit" name="logout"><img src="../public/img/logout.png" alt="Logout"></button>
            </form>
            <h2><img src="../public/img/icon_cart.svg" alt="Mi Cuenta"> La Lico</h2>
        </div>
        <div class="header-center">
            <input type="text" id="search-input" placeholder="¿Qué estás buscando?">
            <button onclick="searchProducts()"><img src="../public/img/icon_search.png" alt="Buscar"></button>
        </div>
        <div class="header-right">
            <a href="#"><img src="../public/img/icon_user.svg" alt="Mi Cuenta">Mi Cuenta</a>
            <a href="#"><img src="../public/img/icon_box.svg" alt="Mis Pedidos">Mis Pedidos</a>
            <div class="cart">
                <a href="#"><img src="../public/img/icon_cart.svg" alt="Carrito">₡17.690</a>
                <span>1</span>
            </div>
        </div>
    </div>

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
                    <select id="sort-price" class="form-select w-auto" onchange="sortProductsByPrice()">
                        <option value="none">Ordenar por</option>
                        <option value="asc">Precio: Menor a Mayor</option>
                        <option value="desc">Precio: Mayor a Menor</option>
                    </select>
                </div>
                <div class="row" id="product-container">
                    <?php foreach ($productos as $producto): ?>
                        <div class="col-md-3 justify-content-center">
                            <div class="product-card" data-category="<?php echo $producto['category']; ?>"
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
    <script src="../public/js/dashboardScript.js"></script>
</body>

</html>