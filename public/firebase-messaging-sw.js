importScripts("https://www.gstatic.com/firebasejs/10.13.2/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/10.13.2/firebase-messaging-compat.js");

firebase.initializeApp({
    apiKey: "AIzaSyAR54ki7dWOLDQaiLJNbC41CHJjcj44xQM",
    authDomain: "reliastat-tech.firebaseapp.com",
    projectId: "reliastat-tech",
    storageBucket: "reliastat-tech.firebasestorage.app",
    messagingSenderId: "185426245569",
    appId: "1:185426245569:web:800e5d1e69681668c9614"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    console.log("Background Message:", payload);

    self.registration.showNotification(
        payload.notification.title,
        {
            body: payload.notification.body,
            icon: "/favicon.ico"
        }
    )
});