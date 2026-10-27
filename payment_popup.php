<!-- payment_popup.php -->
<div id="paymentModal" class="modal hidden">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2><i class="fas fa-credit-card"></i> Select Payment Method</h2>

    <input type="hidden" id="payment-type" value="cart"> <!-- dynamic: cart, service, appointment -->

    <div class="payment-options">
      <button type="button" onclick="payOption('card')">💳 Pay by Card</button>
      <button type="button" onclick="payOption('store')">🏪 Pay In Store</button>
      <button type="button" onclick="payOption('tabby')">🧾 Pay with Tabby</button>
      <button type="button" onclick="payOption('apple')">🍎 Pay with Apple Pay</button>
    </div>

    <form id="payment-form" class="hidden">
      <input type="text" id="card-holder" placeholder="👤 Card Holder Name" required />
      <input type="text" id="card-number" placeholder="💳 Card Number" required />
      <input type="text" id="expiry" placeholder="📅 MM/YY" required />
      <input type="text" id="cvc" placeholder="🔐 CVC" required />
      <button type="submit" class="submit">💵 Pay Now</button>
    </form>

    <div id="feedback-message" class="error-message"></div>
  </div>
</div>
