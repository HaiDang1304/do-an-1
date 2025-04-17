document.addEventListener("DOMContentLoaded", function () {
  const ticketList = document.getElementById("tickets-list");
  if (!ticketList) {
    console.error("Element with ID 'ticket-list' not found");
    return;
  }

  fetch("../php/get_tickets.php")
    .then((response) => response.json())
    .then((data) => {
      ticketList.innerHTML = ""; // Clear the old content
      data.forEach((ticket) => {
        const ticketHTML = `
              <a class="ticket" href="combo.php?id=${ticket.id}">
            <div ">
              <div class="ticket-image">
                <img src="${ticket.image_url}" alt="${ticket.title}">
              </div>
              <div class="ticket-content">
                <h3>${ticket.title}</h3>
                <p>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-star-fill" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <span>${ticket.rating}</span>/10
                  <span>(${ticket.reviews})</span>
                </p>
                <p>Chỉ từ ${ticket.price.toLocaleString("vi-VN")}đ</p>
              </div>
              <div class="ticket-localtion">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                  <path
                    d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                </svg>
                <span>${ticket.location}</span>
              </div>
            </div>
            </a>
          `;
        ticketList.innerHTML += ticketHTML;
      });
    })
    .catch((error) => {
      console.error("Lỗi khi tải vé:", error);
    });
});
