// Thêm vào sau code Flickity
document.querySelectorAll('.carousel-cell img').forEach(img => {
    // Tạo overlay
    const overlay = document.createElement('div');
    overlay.className = 'zoom-overlay';
    
    // Clone hình ảnh
    const clonedImg = img.cloneNode();
    clonedImg.className = 'zoomed-img';
    
    // Thêm vào DOM
    document.body.appendChild(overlay);
    document.body.appendChild(clonedImg);

    // Xử lý click
    img.addEventListener('click', (e) => {
        e.stopPropagation();
        overlay.style.display = 'block';
        clonedImg.classList.add('active');
        clonedImg.src = img.src; // Cập nhật src cho hình clone
    });

    // Tắt zoom
    [overlay, clonedImg].forEach(element => {
        element.addEventListener('click', () => {
            overlay.style.display = 'none';
            clonedImg.classList.remove('active');
        });
    });
});
const lightbox = new PhotoSwipeLightbox({
    gallery: '.carousel',
    children: 'a',
    pswpModule: PhotoSwipe,
    initialZoomLevel: 'fit',
    secondaryZoomLevel: 2,
    maxZoomLevel: 4
});

lightbox.init();
// Cập nhật options Flickity
new Flickity(elem, {
    // ...các options cũ
    fullscreen: true,
    imagesLoaded: true
});

// Thêm sự kiện double click
elem.addEventListener('dblclick', function(event) {
    if (event.target.tagName === 'IMG') {
        this.flickity.viewFullscreen();
    }
});