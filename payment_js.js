document.addEventListener("DOMContentLoaded", () => {
  const openModalBtn = document.querySelector(".open-modal");
  const paymentModal = document.getElementById("paymentModal");
  const modalContent = document.querySelector(".modal-content");
  const closeBtn = paymentModal.querySelector(".close");
  const paymentForm = document.getElementById("payment-form");
  const feedbackMessage = document.getElementById("feedback-message");

  // Show modal
  openModalBtn?.addEventListener("click", () => {
    paymentModal.classList.remove("hidden");
    feedbackMessage.textContent = "";
  });

  // Close by X
  closeBtn?.addEventListener("click", closeModal);

  // Close by outside click
  paymentModal.addEventListener("click", (e) => {
    if (!modalContent.contains(e.target)) {
      closeModal();
    }
  });

  // ESC to close
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal();
    }
  });

  // Card submission
  paymentForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const cardNumber = document.getElementById("card-number").value.trim();
    const expiry = document.getElementById("expiry").value.trim();
    const cvc = document.getElementById("cvc").value.trim();
    const cardHolder = document.getElementById("card-holder").value.trim();

    if (!cardNumber || !expiry || !cvc || !cardHolder) {
      return showMessage("Please fill all fields!", "error");
    }

    const isCardNumberValid = /^\d{16}$/.test(cardNumber);
    const isExpiryValid = /^\d{2}\/\d{2}$/.test(expiry);
    const isCvcValid = /^\d{3}$/.test(cvc);

    if (!isCardNumberValid) return showMessage("Card number must be 16 digits", "error");
    if (!isExpiryValid) return showMessage("Expiry must be in MM/YY format", "error");
    if (!isCvcValid) return showMessage("CVC must be 3 digits", "error");

    const contextType = paymentForm.dataset.context || "cart";
    const contextId = paymentForm.dataset.contextId || null;
    const amount = paymentForm.dataset.amount || 0;

    const data = {
      method: "card",
      type: contextType,
      amount,
      card_holder: cardHolder,
      card_number: cardNumber,
      expiry,
      cvc,
    };

    if (contextType === "appointment") {
      data.appointment_id = contextId;
    } else if (contextType === "service") {
      data.service_booking_id = contextId;
    }

    try {
      const res = await fetch("handle_payment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(data),
      });
      const result = await res.json();
      if (result.status === "success") {
        showMessage(result.message, "success");
        setTimeout(closeModal, 2000);
      } else {
        showMessage(result.message, "error");
      }
    } catch (err) {
      console.error(err);
      showMessage("An unexpected error occurred", "error");
    }
  });

  // Tabby / Store / Apple buttons
  window.payOption = async function (method) {
    const contextType = paymentForm.dataset.context || "cart";
    const contextId = paymentForm.dataset.contextId || null;
    const amount = paymentForm.dataset.amount || 0;

    if (method === "card") {
      paymentForm.classList.remove("hidden");
      return;
    }

    const data = {
      method,
      type: contextType,
      amount,
    };

    if (contextType === "appointment") {
      data.appointment_id = contextId;
    } else if (contextType === "service") {
      data.service_booking_id = contextId;
    }

    try {
      const res = await fetch("handle_payment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(data),
      });
      const result = await res.json();
      if (result.status === "success") {
        showMessage(result.message, "success");
        setTimeout(closeModal, 2000);
      } else {
        showMessage(result.message, "error");
      }
    } catch (err) {
      console.error(err);
      showMessage("Payment failed", "error");
    }
  };

  function closeModal() {
    paymentModal.classList.add("hidden");
    paymentForm.classList.add("hidden");
    paymentForm.reset();
    feedbackMessage.textContent = "";
  }

  function showMessage(msg, type = "error") {
    feedbackMessage.innerText = msg;
    feedbackMessage.className = type === "error" ? "error-message" : "success-message";
  }
});
// <!-- ===============================
//    Authors:
//    Team member  
//    Renad Alkahtani
//    ===============================  -->