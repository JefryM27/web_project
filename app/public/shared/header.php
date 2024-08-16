<?php
session_start();
?>
<link rel="stylesheet" href="../public/css/header.css">
<div class="header">
    <div class="header-left">
        <form action="../utils/logout.php" method="post">
            <button type="submit" name="logout" class="logout-button">
                Cerrar Sesión
            </button>
        </form>
        <h2 class="logo-title">
            <img src="../public/img/mainLogo.png" alt="Mi Cuenta" class="logo-img">
            <span>La Lico</span>
        </h2>
    </div>
    <div class="header-center">
        <input type="text" id="search-input" placeholder="¿Qué estás buscando?" class="search-input">
        <button onclick="searchProducts()" class="search-button">
            <img src="../public/img/icon_search.png" alt="Buscar" class="search-icon">
        </button>
    </div>
    <div class="header-right">
        <a href="../pages/profile.php" class="header-link">
            <img src="../public/img/user.jpg" alt="Mi Cuenta" class="header-icon">Mi Cuenta
        </a>
        <a href="../pages/orders.php" class="header-link">
            <img src="../public/img/box.jpg" alt="Mis Pedidos" class="header-icon">Mis Pedidos
        </a>
        <div class="cart">
            <a class="header-link" id="cart-icon">
                <img src="../public/img/cart.png" alt="Carrito" class="header-icon">₡<h5 id="cart-total-header">0.00
                </h5>
            </a>
            <span class="cart-badge text-white" id="cart-count">0</span>
        </div>
    </div>
</div>