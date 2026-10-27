<!-- Modal Popup for Add/Edit Product -->
<div id="product-modal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3 id="modal-title">Add New Product</h3>
    <form id="product-form" enctype="multipart/form-data">
      <input type="hidden" name="product_id" id="product-id">
      <input type="text" name="name" id="product-name" placeholder="Product Name" required>
      <input type="number" step="0.01" name="price" id="product-price" placeholder="Price" required>
      <input type="number" name="stock" id="product-stock" placeholder="Stock" required>
      <select name="animal_type" id="product-animal" required>
        <option value="">Select Animal</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
        <option value="Bird">Bird</option>
        <option value="small animal">Small animal</option>
      </select>
      <select name="product_type" id="product-type" required>
        <option value="">Select Type</option>
        <option value="Food">Food</option>
        <option value="Toy">Toy</option>
        <option value="supplies">Supplies</option>
      </select>
      <textarea name="description" id="product-desc" placeholder="Description" required></textarea>
      <input type="file" name="image" id="product-image" accept="image/*">
      <button type="submit" class="custom-btn">Save</button>
    </form>
  </div>
</div>
