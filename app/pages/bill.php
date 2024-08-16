<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }

        .invoice-container {
            width: 80%;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header .invoice-id {
            font-size: 24px;
            font-weight: bold;
        }

        .header .logo {
            height: 100px;
            width: auto;
        }

        .header .invoice-number {
            font-size: 18px;
            text-align: right;
        }

        .customer-info {
            margin-bottom: 20px;
        }

        .customer-info p {
            margin: 0;
            padding: 5px 0;
        }

        .invoice-date {
            text-align: right;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .product-details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .product-details th, .product-details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        .product-details th {
            background-color: #f0f0f0;
        }

        .total {
            text-align: right;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="header">
        <div class="invoice-id">Factura ID: 12345</div>
        <img src="logo.png" alt="Logo de la Tienda" class="logo">
        <div class="invoice-number">Número Consecutivo: 00000123</div>
    </div>

    <div class="customer-info">
        <p><strong>Nombre del Cliente:</strong> Juan Pérez</p>
        <p><strong>Correo del Cliente:</strong> juan.perez@example.com</p>
    </div>

    <div class="invoice-date">
        Fecha: 16 de Agosto de 2024
    </div>

    <table class="product-details">
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
            <tr>
                <td>Producto A</td>
                <td>2</td>
                <td>$50.00</td>
                <td>$100.00</td>
                <td>$15.00</td>
                <td>$115.00</td>
            </tr>
            <!-- Puedes agregar más productos aquí -->
        </tbody>
    </table>

    <div class="total">
        <p>Total a Pagar: $115.00</p>
    </div>

    <div class="footer">
        Dirección Física de la Tienda: Calle Falsa 123, Ciudad, País
    </div>
</div>

</body>
</html>
