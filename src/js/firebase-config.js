// Import Firebase SDK
import { initializeApp } from "firebase/app";
import { getAuth, GoogleAuthProvider, signInWithPopup, signOut } from "firebase/auth";

// Cấu hình Firebase (thay thế bằng thông tin của bạn)
const firebaseConfig = {
    apiKey: "AIzaSyD4twjlqWI-ea0GyNxYPG6yrNyOLspkUhg",
    authDomain: "do-an-1-6302c.firebaseapp.com",
    projectId: "do-an-1-6302c",
    storageBucket: "do-an-1-6302c.firebasestorage.app",
    messagingSenderId: "261152376192",
    appId: "1:261152376192:web:466b8b382e22d334e16d65",
    measurementId: "G-3JHLECRCC9"
  };

// Khởi tạo Firebase
const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const provider = new GoogleAuthProvider();

// Hàm đăng nhập bằng Google
export const loginWithGoogle = async () => {
    try {
        const result = await signInWithPopup(auth, provider);
        console.log("User:", result.user);
        alert(`Chào mừng, ${result.user.displayName}`);
    } catch (error) {
        console.error("Lỗi đăng nhập:", error);
    }
};

// Hàm đăng xuất
export const logout = async () => {
    try {
        await signOut(auth);
        alert("Đã đăng xuất!");
    } catch (error) {
        console.error("Lỗi đăng xuất:", error);
    }
};
