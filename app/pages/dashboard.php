<?php
// Leer el archivo JSON
$productos = json_decode(file_get_contents('../utils/productos.json'), true);

// Obtener categorías y subcategorías únicas
$categorias = [];
$subcategorias = [];
foreach ($productos as $producto) {
    $categorias[$producto['category']] = $producto['category'];
    $subcategorias[$producto['sub_category']] = $producto['sub_category'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtrar Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header {
            background-color: #43b02a;
            padding: 10px;
            color: white;
            text-align: center;
            margin-bottom: 20px;
        }

        .product-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            margin: 10px;
            padding: 14px 14px 0px;
            transition: transform 0.3s ease;
            height: 409px;
            width: 216.2px;
        }

        .product-img {
            height: 180px;
            width: 180px;
            object-fit: contain;
            margin-bottom: 15px;
            margin: 10px 0px 0px;
            padding: 20px;
        }
        
        .product-price {
            font-weight: bold;
            color: #525252;
            font-size: 18px;
            letter-spacing: 0em;
            line-height: 25px;
            text-align: left;
        }


        .product-title {
            color: #525252;
            font-size: 16px;
            margin-top: 10px;
        }

        .add-button {
            background-color: #43b02a;
            margin-bottom: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            width: 105px;
            height: 32px;
            color: #fff !important;
            border-color: #43b02a;
            outline: none !important;
        }

        .add-button:hover {
            background-color: #0056b3;
        }

        .filter-section h5 {
            margin-top: 20px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>La Lico</h2>
    </div>

    <div class="container">
        <div class="row">
            <!-- Sidebar de Filtros -->
            <div class="col-md-3">
                <h4>Filtros</h4>
                <hr>
                <h5>Categoría</h5>
                <?php foreach ($categorias as $categoria): ?>
                    <div class="form-check">
                        <input class="form-check-input filter-checkbox" type="checkbox" value="<?php echo $categoria; ?>"
                            id="categoria-<?php echo $categoria; ?>">
                        <label class="form-check-label" for="categoria-<?php echo $categoria; ?>">
                            <?php echo $categoria; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                <hr>
                <h5>Subcategoría</h5>
                <?php foreach ($subcategorias as $subcategoria): ?>
                    <div class="form-check">
                        <input class="form-check-input filter-checkbox" type="checkbox" value="<?php echo $subcategoria; ?>"
                            id="subcategoria-<?php echo $subcategoria; ?>">
                        <label class="form-check-label" for="subcategoria-<?php echo $subcategoria; ?>">
                            <?php echo $subcategoria; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Sección de Productos -->
            <div class="col">
                <h4 id="product-count"><?php echo count($productos); ?> productos</h4>
                <div class="row" id="product-container">
                    <?php foreach ($productos as $producto): ?>
                        <div class="col-md-3 justify-content-center">
                            <div class="product-card" data-category="<?php echo $producto['category']; ?>"
                                data-subcategory="<?php echo $producto['sub_category']; ?>">
                                <img src="<?php echo $producto['image_url']; ?>" class="product-img"
                                    alt="<?php echo $producto['name']; ?>">
                                <div class="add-button">+ Agregar</div>
                                <div class="product-price mt-4">₡<?php echo number_format($producto['price'], 2); ?></div>
                                <div class="product-title mt-4"><?php echo $producto['name']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.filter-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', filterProducts);
        });

        function filterProducts() {
            const selectedCategories = Array.from(document.querySelectorAll('input[id^="categoria-"]:checked')).map(cb => cb.value);
            const selectedSubcategories = Array.from(document.querySelectorAll('input[id^="subcategoria-"]:checked')).map(cb => cb.value);

            let productCount = 0;

            document.querySelectorAll('.product-card').forEach(function (product) {
                const productCategory = product.getAttribute('data-category');
                const productSubcategory = product.getAttribute('data-subcategory');

                const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(productCategory);
                const subcategoryMatch = selectedSubcategories.length === 0 || selectedSubcategories.includes(productSubcategory);

                if (categoryMatch && subcategoryMatch) {
                    product.closest('.col-md-3').style.display = ''; // Muestra toda la columna
                    productCount++;
                } else {
                    product.closest('.col-md-3').style.display = 'none'; // Oculta toda la columna
                }
            });

            document.getElementById('product-count').innerText = productCount + ' productos';
        }
    </script>
</body>

</html>
