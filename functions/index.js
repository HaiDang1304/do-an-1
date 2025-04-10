/**
 * Import function triggers from their respective submodules:
 *
 * const {onCall} = require("firebase-functions/v2/https");
 * const {onDocumentWritten} = require("firebase-functions/v2/firestore");
 *
 * See a full list of supported triggers at https://firebase.google.com/docs/functions
 */

const functions = require("firebase-functions");
const admin = require("firebase-admin");
const sgMail = require("@sendgrid/mail");

admin.initializeApp();

// Lấy API Key từ biến môi trường Firebase Config
sgMail.setApiKey(functions.config().sendgrid.key);

exports.sendPromoEmails = 
functions.pubsub.schedule("every 24 hours").onRun(async (context) => {
  const db = admin.firestore();
  const snapshot = await db.collection("subscribers").get();

  const emails = snapshot.docs.map((doc) => doc.data().email);
  if (emails.length === 0) return null;

  const msg = {
    to: emails,
    from: "haidanglu2004@gmail.com",
    subject: "Khuyến mãi HOT! Đừng bỏ lỡ!",
    html: "<h1>Giảm giá 50% toàn bộ tour du lịch!</h1><p>Đặt ngay để nhận ưu đãi!</p>",
  };

  try {
    await sgMail.sendMultiple(msg);
    console.log("Emails sent successfully!");
  } catch (error) {
    console.error("Error sending emails:", error);
  }

  return null;
});


// Create and deploy your first functions
// https://firebase.google.com/docs/functions/get-started

// exports.helloWorld = onRequest((request, response) => {
//   logger.info("Hello logs!", {structuredData: true});
//   response.send("Hello from Firebase!");
// });  kiểm tra lỗi
