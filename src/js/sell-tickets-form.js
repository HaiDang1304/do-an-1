const data = [
    {
      id: 1,
      title: "Sheraton Phú Quốc Long Beach Resort",
      location: "Bãi Dài,  X. Gành Dầu ",
      point: "9.5",
      total_previews: "100",
      image: "../public/images hotel/image-book-hotel/vinpearl-phu-quoc-resort--374x280.webp",
      description: " Còn 81 ngày | Combo 3N2Đ | VMB + Đưa đón + Buffet sáng + Miễn phí trẻ từ 4.099.000 VND/Khách ",
      tag: [
        { id: 1, name: "Gần gũi với thiên nhiên" },
        { id: 2, name: "Châu Âu" },
        { id: 3, name: "Chụp ảnh đẹp" },
        { id: 4, name: "Thương hiệu quốc tế" },
        { id: 5, name: "Nghỉ dưỡng biển" },
        { id: 6, name: "Bể bơi giáp biển duy nhất" },
      ]
    },
    {
      id: 2,
      title: "Khu Nghỉ Dưỡng Premier Residences Phú Quốc Emerald Bay",
      location: "Thị Trấn An Thới,  Huyện Phú Quốc",
      point: "8.5",
      total_previews: "200",
      image: "../public/images hotel/image-book-hotel/khu-nghi-duong-premier-residences-phu-quoc-emerald-bay-374x280.webp",
      description: " Còn 81 ngày | Combo 3N2Đ | Vé máy bay + Đưa Đón Sân Bay + Ăn sáng từ 4.299.000 VND/Khách ",
      tag: [
        { id: 1, name: "Thương hiệu quốc tế" },
        { id: 2, name: "Hiện đại" },
        { id: 3, name: "Hồ bơi vô cực" },
        { id: 4, name: "Hồ bơi riêng trên căn hộ" },
      ]
    },
    {
      id: 3,
      title: "Title 3",
      location: "Location 3",
      point: "7.5",
      total_previews: "300",
      image: "image3.jpg",
      description: "Description for Title 3",
      tag: [
        { id: 1, name: "Tag 1" },
        { id: 2, name: "Tag 2" },
        { id: 3, name: "Tag 3" },
        { id: 4, name: "Tag 4" },
        { id: 5, name: "Tag 5" },
        { id: 6, name: "Tag 6" },
      ]
    },
  ];

  const container = document.getElementById('hotel-list');

  data.forEach(item => {
    const tagsHTML = item.tag.map(tag => `<span class="badge bg-light border">✔️ ${tag.name}</span>`).join(' ');

    const html = `
      <div class="combo-banner">
      ${item.description}
    
      <div class="deal-box position-relative card overflow-hidden">
        <a href="chi-tiet-${item.title.toLowerCase().replace(/\s+/g, '-')}.html" class="stretched-link"></a>

        <div class="row g-0">
          <div class="col-md-3 position-relative">
            <div class="ribbon bg-danger text-white px-2 py-1 small position-absolute top-0 start-0 rounded-end">
              3N2Đ | VMB+Ăn sáng | 4tr099
            </div>
            <img src="${item.image}" class="img-fluid w-100 h-100 object-fit-cover" alt="Khách sạn">
          </div>

          <div class="col-md-6 p-3 position-relative">
            <h5 class="fw-bold text-primary mb-2">${item.title}</h5>
            <div class="mb-2">
              <span class="text-warning">★ ★ ★ ★ ★</span>
              <span class="badge bg-success ms-2">${item.point} Tuyệt vời</span>
              <small class="text-muted">| ${item.total_previews} đánh giá</small>
            </div>
            <div class="mb-2">
              <i class="bi bi-geo-alt-fill text-danger"></i> ${item.location} -
              <a href="https://www.google.com/maps?q=${encodeURIComponent(item.title)}" 
                 class="text-decoration-underline text-primary map-link" 
                 target="_blank">Xem bản đồ</a>
            </div>
            <div class="hotel-tags">
              ${tagsHTML}
            </div>
          </div>
          <div class="col-md-3 d-flex flex-column justify-content-center align-items-start p-3 bg-light">
            <h6 class="text-info fw-bold mb-2">🎁 Ưu đãi bí mật</h6>
            <p class="mb-1">${item.description}</p>
            <small class="text-muted">📍 Gồm ăn sáng</small>
          </div>
        </div>
      </div>
      </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
  });