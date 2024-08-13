<?php
// Cargar y decodificar el archivo JSON
$productos = json_decode(file_get_contents('productos.json'), true);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Productos</title>
    <style>
        .product-card {
            width: 200px;
            padding: 10px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            display: inline-block;
            vertical-align: top;
        }

        .product-img {
            max-width: 100%;
            height: auto;
        }

        .product-name {
            font-size: 1rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<h1>Productos</h1>

<div class="product-container">
    <?php foreach ($productos as $producto): ?>
        <div class="product-card">
            <img src="<?php echo $producto['image_url']; ?>" alt="<?php echo $producto['name']; ?>" class="product-img">
            <div class="product-name"><?php echo $producto['name']; ?></div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
