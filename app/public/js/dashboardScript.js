document.querySelectorAll('.categoria-checkbox').forEach(function (checkbox) {
    checkbox.addEventListener('change', toggleSubcategories);
});

document.querySelectorAll('.subcategoria-checkbox, .categoria-checkbox').forEach(function (checkbox) {
    checkbox.addEventListener('change', filterProducts);
});

function toggleSubcategories() {
    const selectedCategory = document.querySelector('.categoria-checkbox:checked');

    // Ocultar todas las subcategorías y desmarcar las subcategorías
    document.querySelectorAll('.subcategoria-container').forEach(function (container) {
        container.style.display = 'none';
        container.querySelectorAll('.subcategoria-checkbox').forEach(function (checkbox) {
            checkbox.checked = false;
        });
    });

    // Vaciar el campo de búsqueda al seleccionar una categoría
    document.getElementById('search-input').value = '';
    searchProducts();

    // Mostrar y habilitar las subcategorías de la categoría seleccionada
    if (selectedCategory) {
        const category = selectedCategory.value;
        document.getElementById('subcategorias-' + category).style.display = 'block';

        // Ocultar las otras categorías excepto la seleccionada
        document.querySelectorAll('.categoria-checkbox').forEach(function (checkbox) {
            if (checkbox !== selectedCategory) {
                checkbox.parentElement.style.display = 'none';
            }
        });
    } else {
        // Mostrar todas las categorías si ninguna está seleccionada
        document.querySelectorAll('.categoria-checkbox').forEach(function (checkbox) {
            checkbox.parentElement.style.display = 'block';
        });
    }

    filterProducts(); // Actualizar productos al cambiar las categorías
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
            product.closest('.col-md-3').style.display = ''; // Muestra toda la columna
            productCount++;
        } else {
            product.closest('.col-md-3').style.display = 'none'; // Oculta toda la columna
        }
    });

    document.getElementById('product-count').innerText = productCount + ' productos';
}

function searchProducts() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    let productCount = 0;

    document.querySelectorAll('.product-card').forEach(function (product) {
        const productName = product.getAttribute('data-name').toLowerCase();

        if (productName.includes(searchTerm)) {
            product.style.display = '';
            productCount++;
        } else {
            product.style.display = 'none';
        }
    });

    document.getElementById('product-count').innerText = productCount + ' productos';
}

function sortProductsByPrice() {
    const sortOrder = document.getElementById('sort-price').value;
    const productContainer = document.getElementById('product-container');
    const products = Array.from(productContainer.getElementsByClassName('product-card'));

    products.sort((a, b) => {
        const priceA = parseFloat(a.getAttribute('data-price'));
        const priceB = parseFloat(b.getAttribute('data-price'));

        if (sortOrder === 'asc') {
            return priceA - priceB;
        } else if (sortOrder === 'desc') {
            return priceB - priceA;
        } else {
            return 0;
        }
    });

    products.forEach(product => productContainer.appendChild(product));
}

function toggleSpinner(button) {
    const spinnerContainer = button.nextElementSibling;
    button.style.display = 'none';
    spinnerContainer.style.display = 'flex';
}

function incrementCount(button) {
    const spinnerValueElement = button.previousElementSibling;
    let count = parseInt(spinnerValueElement.textContent);
    count++;
    spinnerValueElement.textContent = count;

    updatePrice(button, count);
}

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

function updatePrice(button, count) {
    const productCard = button.closest('.product-card');
    const productPriceElement = productCard.querySelector('.product-price-value');
    const basePrice = parseFloat(productPriceElement.getAttribute('data-base-price').replace(/[^0-9.-]+/g, ""));

    const newPrice = basePrice * count;
    productPriceElement.textContent = newPrice.toFixed(2);
}

function toggleBackToButton(button) {
    const spinnerContainer = button.closest('.spinner-container');
    const addButton = spinnerContainer.previousElementSibling;
    const productCard = button.closest('.product-card');
    const productPriceElement = productCard.querySelector('.product-price-value');
    const basePrice = parseFloat(productPriceElement.getAttribute('data-base-price').replace(/[^0-9.-]+/g, ""));

    addButton.style.display = 'inline-block';
    spinnerContainer.style.display = 'none';
    productPriceElement.textContent = basePrice.toFixed(2);
}