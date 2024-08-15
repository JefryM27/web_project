document.querySelectorAll('.categoria-checkbox').forEach(function (checkbox) {
    checkbox.addEventListener('change', toggleSubcategories);
});

document.querySelectorAll('.subcategoria-checkbox, .categoria-checkbox').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        resetSortOrder();
        filterProducts();
    });
});

function toggleSubcategories() {
    const selectedCategory = document.querySelector('.categoria-checkbox:checked');

    document.querySelectorAll('.subcategoria-container').forEach(function (container) {
        container.style.display = 'none';
        container.querySelectorAll('.subcategoria-checkbox').forEach(function (checkbox) {
            checkbox.checked = false;
        });
    });

    document.getElementById('search-input').value = '';
    searchProducts();

    if (selectedCategory) {
        const category = selectedCategory.value;
        document.getElementById('subcategorias-' + category).style.display = 'block';

        document.querySelectorAll('.categoria-checkbox').forEach(function (checkbox) {
            if (checkbox !== selectedCategory) {
                checkbox.parentElement.style.display = 'none';
            }
        });
    } else {
        document.querySelectorAll('.categoria-checkbox').forEach(function (checkbox) {
            checkbox.parentElement.style.display = 'block';
        });
    }

    filterProducts();
}

function filterProducts() {
    const selectedCategory = document.querySelector('.categoria-checkbox:checked');
    const selectedSubcategories = Array.from(document.querySelectorAll('.subcategoria-checkbox:checked')).map(cb => cb.value);

    let productCount = 0;

    document.querySelectorAll('.product-card').forEach(function (product) {
        const productCategory = product.getAttribute('data-category');
        const productSubcategory = product.getAttribute('data-subcategory');

        const categoryMatch = !selectedCategory || selectedCategory.value === productCategory;
        const subcategoryMatch = selectedSubcategories.length === 0 || selectedSubcategories.includes(productSubcategory);

        if (categoryMatch && subcategoryMatch) {
            product.closest('.col-md-3').style.display = '';
            productCount++;
        } else {
            product.closest('.col-md-3').style.display = 'none';
        }
    });

    document.getElementById('product-count').innerText = productCount + ' productos';
    sortProductsByPrice();
}

function searchProducts() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    let productCount = 0;

    document.querySelectorAll('.product-card').forEach(function (product) {
        const productName = product.getAttribute('data-name').toLowerCase();

        if (productName.includes(searchTerm)) {
            product.style.display = '';
            product.closest('.col-md-3').style.display = '';
            productCount++;
        } else {
            product.style.display = 'none';
            product.closest('.col-md-3').style.display = 'none';
        }
    });

    document.getElementById('product-count').innerText = productCount + ' productos';
    resetSortOrder();
}

function resetSortOrder() {
    document.getElementById('sort-price').value = 'none';
}

function sortProductsByPrice() {
    const sortOrder = document.getElementById('sort-price').value;
    const productContainer = document.getElementById('product-container');
    const products = Array.from(productContainer.getElementsByClassName('product-card'))
        .filter(product => product.closest('.col-md-3').style.display !== 'none');

    if (sortOrder === 'none') {
        return;
    }

    products.sort((a, b) => {
        const priceA = parseFloat(a.getAttribute('data-price'));
        const priceB = parseFloat(b.getAttribute('data-price'));

        if (sortOrder === 'asc') {
            return priceA - priceB;
        } else if (sortOrder === 'desc') {
            return priceB - priceA;
        }
    });

    products.forEach(product => productContainer.appendChild(product));
}

let cart = {};

// Función para mostrar/ocultar el botón de "Agregar" y el spinner
function toggleSpinner(button) {
    const spinnerContainer = button.nextElementSibling;
    button.style.display = 'none';
    spinnerContainer.style.display = 'flex';
    addToCart(button);
}

// Función para incrementar la cantidad de un producto en el carrito
function incrementCount(button) {
    const spinnerValueElement = button.previousElementSibling;
    let count = parseInt(spinnerValueElement.textContent);
    count++;
    spinnerValueElement.textContent = count;

    updatePrice(button, count);
}

// Función para decrementar la cantidad de un producto en el carrito
function decrementCount(button) {
    const spinnerValueElement = button.nextElementSibling;
    let count = parseInt(spinnerValueElement.textContent);
    if (count > 1) {
        count--;
        spinnerValueElement.textContent = count;

        updatePrice(button, count);
    } else {
        toggleBackToButton(button);
    }
}

// Función para actualizar el precio de un producto en función de la cantidad seleccionada
function updatePrice(button, count) {
    const productCard = button.closest('.product-card');
    const productPriceElement = productCard.querySelector('.product-price-value');
    const basePrice = parseFloat(productPriceElement.getAttribute('data-base-price').replace(/[^0-9.-]+/g, ""));

    const newPrice = basePrice * count;
    productPriceElement.textContent = newPrice.toFixed(2);

    // Actualizar cantidad en el carrito
    const productId = productCard.getAttribute('data-name');
    cart[productId].quantity = count;
    updateCartModal();
}

// Función para volver a mostrar el botón de "Agregar" si la cantidad se reduce a 0
function toggleBackToButton(button) {
    const spinnerContainer = button.closest('.spinner-container');
    const addButton = spinnerContainer.previousElementSibling;
    const productCard = button.closest('.product-card');
    const productPriceElement = productCard.querySelector('.product-price-value');
    const basePrice = parseFloat(productPriceElement.getAttribute('data-base-price').replace(/[^0-9.-]+/g, ""));

    addButton.style.display = 'inline-block';
    spinnerContainer.style.display = 'none';
    productPriceElement.textContent = basePrice.toFixed(2);

    // Eliminar del carrito
    const productId = productCard.getAttribute('data-name');
    delete cart[productId];
    updateCartModal();
}

// Función para agregar un producto al carrito
function addToCart(button) {
    const productCard = button.closest('.product-card');
    const productId = productCard.getAttribute('data-name');
    const productName = productCard.querySelector('.product-title').textContent;
    const productPrice = parseFloat(productCard.querySelector('.product-price-value').getAttribute('data-base-price').replace(/[^0-9.-]+/g, ""));
    const quantity = parseInt(productCard.querySelector('.spinner-value').textContent);

    if (cart[productId]) {
        cart[productId].quantity += quantity;
    } else {
        cart[productId] = {
            name: productName,
            price: productPrice,
            quantity: quantity,
            image: productCard.querySelector('img').src // Guardar la imagen del producto
        };
    }

    updateCartModal();
}

// Función para actualizar el modal del carrito y el contador pequeño del carrito en la cabecera
function updateCartModal() {
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total-modal');
    const cartTotalHeaderElement = document.getElementById('cart-total-header');
    const cartCountElement = document.getElementById('cart-count');
    const cartDataElement = document.getElementById('cart-data');  // Campo oculto para los datos del carrito

    cartItemsContainer.innerHTML = '';

    let total = 0;
    let itemCount = 0;

    for (const productId in cart) {
        const item = cart[productId];
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        itemCount++;

        const itemElement = document.createElement('div');
        itemElement.classList.add('cart-item', 'd-flex', 'justify-content-between', 'align-items-center', 'mb-2');
        itemElement.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="img-fluid cart-item-img">
            <div class="flex-grow-1 ms-3">
                <h5 class="mb-1 cart-item-name">${item.name}</h5>
                <div class="d-flex align-items-center justify-content-between">
                    <div class="cart-item-spinner">
                        <button class="btn btn-light btn-sm" onclick="decrementCartItem('${productId}')">-</button>
                        <span class="mx-2">${item.quantity}</span>
                        <button class="btn btn-light btn-sm" onclick="incrementCartItem('${productId}')">+</button>
                    </div>
                    <div class="cart-item-price text-end">₡${itemTotal.toFixed(2)}</div>
                </div>
            </div>
            <button class="btn btn-danger btn-sm cart-item-delete" onclick="removeFromCart('${productId}')">🗑️</button>
        `;
        cartItemsContainer.appendChild(itemElement);
    }

    cartTotalElement.textContent = total.toFixed(2);
    cartTotalHeaderElement.textContent = total.toFixed(2);
    cartCountElement.textContent = itemCount;

    // Convertir el carrito a JSON y almacenarlo en el campo oculto del formulario
    cartDataElement.value = JSON.stringify(cart);
}

function removeFromCart(productId) {
    delete cart[productId];
    updateCartModal();
    updateMainProductQuantity(productId);
}

// Función para decrementar la cantidad de un producto desde el modal
function decrementCartItem(productId) {
    if (cart[productId].quantity > 1) {
        cart[productId].quantity--;
    } else {
        delete cart[productId];
    }
    updateCartModal();
    updateMainProductQuantity(productId);
}

// Función para incrementar la cantidad de un producto desde el modal
function incrementCartItem(productId) {
    cart[productId].quantity++;
    updateCartModal();
    updateMainProductQuantity(productId);
}

// Función para actualizar la cantidad en la página principal si se modifica desde el modal
function updateMainProductQuantity(productId) {
    const productCard = document.querySelector(`.product-card[data-name="${productId}"]`);
    if (productCard) {
        const spinnerValueElement = productCard.querySelector('.spinner-value');
        if (cart[productId]) {
            spinnerValueElement.textContent = cart[productId].quantity;
        } else {
            toggleBackToButton(productCard.querySelector('.btn-spinner')); // Mostrar el botón de agregar
        }
    }
}

// Función para vaciar el carrito completamente
document.getElementById('clear-cart').addEventListener('click', function() {
    cart = {};
    updateCartModal();
    document.querySelectorAll('.spinner-container').forEach(container => {
        toggleBackToButton(container.querySelector('.btn-spinner'));
    });
});

document.getElementById('cart-icon').addEventListener('click', function () {
    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
    cartModal.show();
});

document.getElementById('clear-cart').addEventListener('click', function () {
    cart = {};
    updateCartModal();
    const cartModal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
    cartModal.hide();
});

document.getElementById('close-cart').addEventListener('click', function () {
    const cartModal = bootstrap.Modal.getInstance(document.getElementById('cartModal'));
    cartModal.hide();
});

document.getElementById('confirm-purchase').addEventListener('click', function (event) {
    event.preventDefault(); // Prevenir el envío inmediato del formulario
    const userConfirmed = confirm('¿Estás seguro de que deseas continuar con la compra?');

    if (userConfirmed) {
        document.getElementById('cart-form').submit(); // Enviar el formulario si el usuario confirma
    } else {
        // El usuario canceló, no hacemos nada
    }
});
