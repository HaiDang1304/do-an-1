document.addEventListener('DOMContentLoaded', function () {
    generateCaptcha();
});

function generateCaptcha() {
    const captchaDisplay = document.getElementById('captchaDisplay');
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let captcha = '';
    for (let i = 0; i < 6; i++) {
        captcha += characters.charAt(Math.floor(Math.random() * characters.length));
    }
    captchaDisplay.textContent = captcha;
}

document.getElementById('loginForm').addEventListener('submit', function (event) {
    event.preventDefault();
    const captchaInput = document.getElementById('captchaInput').value;
    const captchaDisplay = document.getElementById('captchaDisplay').textContent;

    if (captchaInput === captchaDisplay) {
        alert('Đăng ký thành công!');
        // Thực hiện các thao tác đăng ký ở đây
    } else {
        alert('CAPTCHA không đúng. Vui lòng thử lại.');
        generateCaptcha();
    }
});