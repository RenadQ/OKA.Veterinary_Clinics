// document.addEventListener('DOMContentLoaded', () => {
//   const cartContainer = document.getElementById('cart-content');
//   const emptyMessage = document.getElementById('empty-message');
//   const totalAmountDisplay = document.getElementById('total-amount');
//   const checkoutBtn = document.querySelector('.checkout-btn');

//   // Update total price of all items in the cart
//   function updateSubtotal() {
//       let subtotal = 0;
//       const items = document.querySelectorAll('.cart-item');

//       items.forEach(item => {
//           const price = parseFloat(item.querySelector('.item-price').textContent);
//           const quantity = parseInt(item.querySelector('input[type="number"]').value);
//           subtotal += price * quantity;
//       });

//       totalAmountDisplay.textContent = subtotal.toFixed(2);
//   }

//   // Add event listeners to quantity inputs and remove buttons
//   function setupCartItems() {
//       const items = document.querySelectorAll('.cart-item');

//       items.forEach(item => {
//           const quantityInput = item.querySelector('input[type="number"]');
//           const removeBtn = item.querySelector('.remove-btn');

//           // Update quantity in the database when the input changes
//           quantityInput.addEventListener('input', () => {
//               const cartId = quantityInput.getAttribute('data-cart-id');
//               const newQuantity = quantityInput.value;

//               // Log the data being sent for debugging
//               console.log('Updating cart item with cart_id:', cartId, 'and quantity:', newQuantity);

//               // Send the updated quantity to the server
//               fetch('update_cart_quantity.php', {
//                   method: 'POST',
//                   body: new URLSearchParams({
//                       cart_id: cartId,
//                       quantity: newQuantity
//                   })
//               }).then(response => response.json())
//                 .then(data => {
//                     if (data.status === 'success') {
//                         updateSubtotal(); // Update the subtotal if successful
//                     } else {
//                         alert('Failed to update quantity.');
//                         console.log(data.message);  // Log error message for debugging
//                     }
//                 }).catch(error => {
//                     console.error('Error:', error);
//                     alert('An error occurred while updating the quantity.');
//                 });
//           });

//           // Remove item from the cart
//           removeBtn.addEventListener('click', () => {
//               const cartId = removeBtn.getAttribute('data-cart-id');
//               fetch('remove_cart_item.php', {
//                   method: 'POST',
//                   body: new URLSearchParams({
//                       cart_id: cartId
//                   })
//               }).then(() => {
//                   item.remove(); // Remove the item from the DOM
//                   updateSubtotal(); // Update the subtotal after item removal
//               }).catch(error => {
//                   console.error('Error:', error);
//                   alert('An error occurred while removing the item.');
//               });
//           });
//       });
//   }

//   // Checkout button logic
//   checkoutBtn.addEventListener('click', () => {
//       window.location.href = 'payment.php'; // Redirect to payment page
//   });

//   // Initialize everything
//   setupCartItems();
//   updateSubtotal(); // Initial subtotal calculation on page load
// });

// // document.addEventListener('DOMContentLoaded', () => {
// //   const cartContainer = document.getElementById('cart-content');
// //   const emptyMessage = document.getElementById('empty-message');
// //   const totalAmountDisplay = document.getElementById('total-amount');
// //   const checkoutBtn = document.querySelector('.checkout-btn');

// //   // Update total price of all items in the cart
// //   function updateSubtotal() {
// //       let subtotal = 0;
// //       const items = document.querySelectorAll('.cart-item');

// //       items.forEach(item => {
// //           const price = parseFloat(item.querySelector('.item-price').textContent);
// //           const quantity = parseInt(item.querySelector('input[type="number"]').value);
// //           subtotal += price * quantity;
// //       });

// //       totalAmountDisplay.textContent = subtotal.toFixed(2);
// //   }

// //   // Add event listeners to quantity inputs and remove buttons
// //   function setupCartItems() {
// //       const items = document.querySelectorAll('.cart-item');

// //       items.forEach(item => {
// //           const quantityInput = item.querySelector('input[type="number"]');
// //           const removeBtn = item.querySelector('.remove-btn');

// //           // Update quantity in the database when the input changes
// //           quantityInput.addEventListener('input', () => {
// //               const cartId = quantityInput.getAttribute('data-cart-id');
// //               const newQuantity = quantityInput.value;

// //               // Send the updated quantity to the server
// //               fetch('update_cart_quantity.php', {
// //                   method: 'POST',
// //                   body: new URLSearchParams({
// //                       cart_id: cartId,
// //                       quantity: newQuantity
// //                   })
// //               }).then(response => response.json())
// //                 .then(data => {
// //                     if (data.status === 'success') {
// //                         updateSubtotal();
// //                     } else {
// //                         alert('Failed to update quantity.');
// //                     }
// //                 }).catch(error => {
// //                     console.error('Error:', error);
// //                     alert('An error occurred while updating the quantity.');
// //                 });
// //           });

// //           // Remove item from the cart
// //           removeBtn.addEventListener('click', () => {
// //               const cartId = removeBtn.getAttribute('data-cart-id');
// //               fetch('remove_cart_item.php', {
// //                   method: 'POST',
// //                   body: new URLSearchParams({
// //                       cart_id: cartId
// //                   })
// //               }).then(() => {
// //                   item.remove();
// //                   updateSubtotal();
// //               });
// //           });
// //       });
// //   }

// //   // Checkout button logic
// //   checkoutBtn.addEventListener('click', () => {
// //       window.location.href = 'payment.php'; // Redirect to payment page
// //   });

// //   // Initialize everything
// //   setupCartItems();
// //   updateSubtotal();
// // });

// // quantityInput.addEventListener('input', () => {
// //   const cartId = quantityInput.getAttribute('data-cart-id');
// //   const newQuantity = quantityInput.value;

// //   // Log the data being sent for debugging
// //   console.log('Updating cart item with cart_id:', cartId, 'and quantity:', newQuantity);

// //   // Send the updated quantity to the server
// //   fetch('update_cart_quantity.php', {
// //       method: 'POST',
// //       body: new URLSearchParams({
// //           cart_id: cartId,
// //           quantity: newQuantity
// //       })
// //   }).then(response => response.json())
// //     .then(data => {
// //         if (data.status === 'success') {
// //             updateSubtotal();
// //         } else {
// //             alert('Failed to update quantity.');
// //             console.log(data.message);  // Log error message for debugging
// //         }
// //     }).catch(error => {
// //         console.error('Error:', error);
// //         alert('An error occurred while updating the quantity.');
// //     });
// // });
