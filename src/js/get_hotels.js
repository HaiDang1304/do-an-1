document.addEventListener("DOMContentLoaded", function () {
    fetch('../php/get_hotels_index.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("tickets-hotels");

            if (!data || data.length === 0) {
                container.innerHTML = "<p>Không có khách sạn nào.</p>";
                return;
            }
            container.innerHTML = '';
            data.slice(0, 4).forEach(hotel => {
                const ticket = document.createElement("div");
                ticket.className = "ticket";
            
                ticket.innerHTML = `
                <a class="ticket" href="../php/hotels-detail.php?id=${hotel.id}">
                    <div class="ticket-image">
                        <img src="../public/images/images-hotel/bg-tickets/${hotel.image}" alt="${hotel.name}">
                    </div>
                    <div class="ticket-content">
                        <h3>${hotel.name}</h3>
                        <p>⭐ ${hotel.rating}/10 (${hotel.reviews} đánh giá)</p>
                        <p>Chỉ từ ${Number(hotel.price).toLocaleString('vi-VN')}đ</p>
                    </div>
                    <div class="ticket-localtion-tag">
                        ${Array.isArray(hotel.tags) ? hotel.tags.map(tag => `
                            <div class="ticket-localtion-hatag">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-tag-fill" viewBox="0 0 16 16">
                                    <path d="M2 1a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l4.586-4.586a1 1 0 0 0 0-1.414l-7-7A1 1 0 0 0 6.586 1z" />
                                </svg>
                                <span>${tag}</span>
                            </div>
                        `).join('') : ''}
                    </div>
                </a>
                `;
            
                container.appendChild(ticket);
            });
            
        })
        .catch(error => {
            console.error("Lỗi khi fetch hotels:", error);
        });
});
