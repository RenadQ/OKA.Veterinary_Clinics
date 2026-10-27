    function showHistory(button) {
        const userId = button.parentElement.parentElement.dataset.userId;

        // استعلام لجلب البيانات من الخادم
        fetch(`get_user_history.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                const orderList = document.getElementById("orderList");
                const serviceList = document.getElementById("serviceList");
                
                // مسح القوائم السابقة
                orderList.innerHTML = '';
                serviceList.innerHTML = '';

                // إضافة الطلبات
                data.orders.forEach(order => {
                    orderList.innerHTML += `<li>Order ID: ${order.order_id}, Total: ${order.total_amount}, Status: ${order.status}</li>`;
                });

                // إضافة حجوزات الخدمات
                data.services.forEach(service => {
                    serviceList.innerHTML += `<li>Booking ID: ${service.booking_id}, Appointment Date: ${service.appointment_date}, Status: ${service.status_id}</li>`;
                });

                // عرض النافذة المنبثقة
                document.getElementById("historyModal").style.display = "block";
            });
    }

    function closeHistoryModal() {
        document.getElementById("historyModal").style.display = "none";
    }