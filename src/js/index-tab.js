document.addEventListener("DOMContentLoaded", function () {
    const tabs = document.querySelectorAll(".tab");
    const contents = document.querySelectorAll(".tab-body .body");

    tabs.forEach(tab => {
        tab.addEventListener("click", function () {
            // Xóa class "active" khỏi tất cả các tab
            tabs.forEach(t => t.classList.remove("active"));
            this.classList.add("active");

            // Ẩn tất cả nội dung của tab-body
            contents.forEach(c => c.classList.remove("active"));

            // Hiển thị nội dung tương ứng
            const targetId = this.getAttribute("data-tab");
            const targetContent = document.getElementById(targetId);

            if (targetContent) {
                targetContent.classList.add("active");
            }
        });
    });
});
