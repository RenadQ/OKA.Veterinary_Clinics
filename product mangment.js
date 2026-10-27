// ======================= Toast Message =======================
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    toast.style.display = 'block';
    setTimeout(() => {
      toast.style.display = 'none';
    }, 3000);
  }
  
  // ======================= Modal Handling =======================
  function openModal(product = null) {
    const modal = document.getElementById('productModal');
    const title = document.getElementById('modal-title');
    const form = document.getElementById('product-form');
  
    form.reset();
    document.getElementById('product_id').value = '';
  
    if (product) {
      document.getElementById('product_id').value = product.product_id;
      document.getElementById('name').value = product.name;
      document.getElementById('price').value = product.price;
      document.getElementById('stock').value = product.stock;
      document.getElementById('animal_type').value = product.animal_type;
      document.getElementById('product_type').value = product.product_type;
      document.getElementById('description').value = product.description;
      title.textContent = 'Edit Product';
    } else {
      title.textContent = 'Add Product';
    }
  
    modal.style.display = 'block';
  }
  
  function closeModal() {
    document.getElementById('productModal').style.display = 'none';
  }
  
  // ======================= Pagination + Filter =======================
  let currentPage = 1;
  let currentFilter = 'all';
  
  document.getElementById('stock-filter')?.addEventListener('change', () => {
    currentFilter = document.getElementById('stock-filter').value;
    fetchProducts(1, currentFilter);
  });
  
  // ======================= Fetch Products =======================
  function fetchProducts(page = 1, filter = 'all') {
    currentPage = page;
    fetch(`fetch_products.php?page=${page}&filter=${filter}`)
      .then(res => res.json())
      .then(data => {
        const tbody = document.getElementById('product-table');
        tbody.innerHTML = '';
  
        data.products.forEach((product, index) => {
          const tr = document.createElement('tr');
          const isOut = product.is_out_of_stock == 1;
          const stockClass = isOut ? 'out-stock' : 'in-stock';
  
          tr.innerHTML = `
            <td>${(page - 1) * 50 + index + 1}</td>
            <td>${product.name}</td>
            <td>${product.price}</td>
            <td>${product.stock}</td>
            <td>${product.animal_type}</td>
            <td>${product.product_type}</td>
            <td><span class="${stockClass}">${isOut ? 'Out of Stock' : 'In Stock'}</span></td>
            <td><img src="${product.image_url}" alt="${product.name}" width="50"></td>
            <td>
              <button class="edit-btn" onclick='openModal(${JSON.stringify(product)})'>Edit</button>
              <button class="delete-btn" onclick='deleteProduct(${product.product_id})'>Delete</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
  
        renderPagination(data.total);
      });
  }
  
  // ======================= Pagination Buttons =======================
  function renderPagination(total) {
    const totalPages = Math.ceil(total / 50);
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';
  
    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      if (i === currentPage) btn.classList.add('active');
      btn.onclick = () => fetchProducts(i, currentFilter);
      pagination.appendChild(btn);
    }
  }
  
  // ======================= Submit Form =======================
  document.getElementById('product-form').addEventListener('submit', function (e) {
    e.preventDefault();
  
    const formData = new FormData(this);
  
    fetch('save_product.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.text())
      .then(msg => {
        if (msg.includes('success')) {
          showToast(msg, 'success');
          closeModal();
          fetchProducts(currentPage, currentFilter);
        } else {
          showToast('Error: ' + msg, 'error');
        }
      })
      .catch(() => {
        showToast('Server error while saving product', 'error');
      });
  });
  
  // ======================= Delete Product =======================
  function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    fetch(`delete_product.php?id=${id}`)
      .then(res => res.text())
      .then(msg => {
        if (msg.includes('Deleted')) {
          showToast(msg, 'success');
          fetchProducts(currentPage, currentFilter);
        } else {
          showToast('Error: ' + msg, 'error');
        }
      });
  }
  
  // Init
  document.addEventListener('DOMContentLoaded', () => {
    fetchProducts();
  });
  