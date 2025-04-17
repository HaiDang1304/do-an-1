document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById("carousel-container");
    
    if (!container) {
        console.error('Không tìm thấy #carousel-container');
        return;
    }

    fetch('../php/get_carousel-cell.php')
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            const fragment = document.createDocumentFragment();

            data.data.forEach(tour => {
                const cell = document.createElement("div");
                cell.className = "carousel-cell";

                const img = document.createElement("img");
                img.src = tour.image;
                img.alt = tour.alt;
                img.setAttribute("data-pswp-src", tour.image);
                img.setAttribute("data-pswp-width", tour.width.toString());
                img.setAttribute("data-pswp-height", tour.height.toString());

                cell.appendChild(img);
                fragment.appendChild(cell);
            });

            container.appendChild(fragment);

            // Khởi tạo Flickity nếu đã load library
            if (typeof Flickity !== 'undefined') {
                new Flickity(container, {
                    cellAlign: 'left',
                    contain: true,
                    wrapAround: true,
                    autoPlay: 5000 // Tự động chuyển slide mỗi 5s
                });
            }
        })
        .catch(error => {
            console.error('Lỗi:', error);
            container.innerHTML = `<div class="error">Không thể tải carousel: ${error.message}</div>`;
        });
});