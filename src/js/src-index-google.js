 import { initializeApp } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-app.js";
        import { getAuth, GoogleAuthProvider, signInWithPopup, signOut, onAuthStateChanged } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-auth.js";

        // Cấu hình Firebase
        const firebaseConfig = {
            apiKey: "AIzaSyD4twjlqWI-ea0GyNxYPG6yrNyOLspkUhg",
            authDomain: "do-an-1-6302c.firebaseapp.com",
            projectId: "do-an-1-6302c",
            storageBucket: "do-an-1-6302c.appspot.com",
            messagingSenderId: "261152376192",
            appId: "1:261152376192:web:466b8b382e22d334e16d65",
            measurementId: "G-3JHLECRCC9"
        };

        // Khởi tạo Firebase
        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);
        const provider = new GoogleAuthProvider();

        // Xử lý đăng nhập bằng Google



        // Khởi tạo Flickity
        const elem = document.querySelector('.carousel');
        new Flickity(elem, {
            cellAlign: 'left',
            contain: true,
            wrapAround: true, // Tạo vòng lặp liên tục
            autoPlay: 2000,   // Tự động chuyển slide (tính bằng ms)
            pauseAutoPlayOnHover: true, // Tạm dừng khi rê chuột vào
            prevNextButtons: true, // Hiển thị nút điều hướng
            pageDots: true // Hiển thị chấm tròn điều hướng
        });



        document.getElementById("logout").addEventListener("click", () => {
            signOut(auth).then(() => {
                localStorage.removeItem("user")
                document.getElementById("user-info").innerHTML = "";
                document.getElementById("logout").style.display = "none";
                document.getElementById("user-info").style.display = "none";
                document.querySelector(".login-button").style.display = "block";
            }).catch((error) => {
                console.error("Lỗi đăng xuất:", error);
            });
        });

        onAuthStateChanged(auth, (user) => {
            if (user) {
                console.log("Người dùng đã đăng nhập:", user);
                console.log("Email xác thực:", user.emailVerified);
                document.getElementById("user-info").innerHTML = `Xin chào: ${user.email}`;//(${user.email})
                document.getElementById("logout").style.display = "block";
                document.getElementById("user-info").style.display = "block";
                document.querySelector(".login-button").style.display = "none";
            } else {
                console.log("Người dùng chưa đăng nhập.");
            }
        });


