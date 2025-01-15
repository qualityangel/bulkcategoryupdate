document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category-select');
    const productTableBody = document.querySelector('#product-table tbody');
    const selectAllCheckbox = document.getElementById('select-all');

    if (!categorySelect || !productTableBody || !selectAllCheckbox) {
        console.error('Required elements not found in the DOM.');
        return;
    }

    // Luister naar categorie wijzigingen
    categorySelect.addEventListener('change', function () {
        const categoryId = categorySelect.value;

        if (categoryId && categoryId !== "0") {
            productTableBody.innerHTML = '<tr><td colspan="6">Loading products...</td></tr>';
            fetchProducts(categoryId);
        } else {
            productTableBody.innerHTML = '<tr><td colspan="6">Please select a category.</td></tr>';
        }
    });

    // Selecteer/Deselecteer alle producten
    selectAllCheckbox.addEventListener('click', function () {
        const checkboxes = productTableBody.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    });

    // Functie om producten op te halen
    function fetchProducts(categoryId) {
        const url = `../index.php?fc=module&module=bulkcategoryupdate&controller=ajax&action=getProductsByCategory&category_id=${categoryId}`;
        
        fetch(url, {
            method: 'GET',
            headers: { 'Content-Type': 'application/json' },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                productTableBody.innerHTML = '';
                if (data.error) {
                    console.error('Error:', data.error);
                    productTableBody.innerHTML = `<tr><td colspan="6">${data.error}</td></tr>`;
                    return;
                }

                if (data.length > 0) {
                    data.forEach(product => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td><input type="checkbox" name="bulk_category_selected_products[]" value="${product.id_product}" /></td>
                            <td>${product.id_product}</td>
                            <td><img src="${product.image_url}" alt="${product.name}" style="width: 50px; height: 50px;" /></td>
                            <td>${product.name}</td>
                            <td>${product.categories || '-'}</td>
                            <td>${product.default_category || '-'}</td>
                        `;
                        productTableBody.appendChild(row);
                    });
                } else {
                    productTableBody.innerHTML = '<tr><td colspan="6">No products found in this category.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                productTableBody.innerHTML = '<tr><td colspan="6">Error loading products. Please try again later.</td></tr>';
            });
    }
});
