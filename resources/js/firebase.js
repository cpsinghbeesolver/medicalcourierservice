import { initializeApp } from "firebase/app";
import { getMessaging, getToken } from "firebase/messaging";
import { onMessage } from "firebase/messaging";

const firebaseConfig = {
    apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
    authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
    projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
    storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
    messagingSenderId: import.meta.env.VITE_FIREBASE_SENDER_ID,
    appId: import.meta.env.VITE_FIREBASE_APP_ID,
};
const app = initializeApp(firebaseConfig);

const messaging = getMessaging(app);

export async function generateFCMToken() {

    const permission = await Notification.requestPermission();

    if (permission !== "granted") {
        console.log("Notification permission denied.");
        return;
    }

    const registration = await navigator.serviceWorker.register(
        '/firebase-messaging-sw.js'
    );

    const fcm_token = await getToken(messaging, {
        vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY,
        serviceWorkerRegistration: registration,
    });
    console.log("FCM Token:", fcm_token);

    //Save fcm token in db for company
    const token = document
    .querySelector('meta[name="api-token"]')
    .getAttribute('content');

    const savedToken = localStorage.getItem('fcm_token');
    if (savedToken !== fcm_token) {
        $.ajax({
                url: '/api/v1/save-fcm-token',
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                data: { device_token: fcm_token },
                success: function(response) {
                    localStorage.setItem('fcm_token', fcm_token);
                }
            });
        return token;
    }
}

onMessage(messaging, (payload) => {

    console.log("Foreground notification:", payload);

    if (Notification.permission === "granted") {
        new Notification(payload.notification.title, {
            body: payload.notification.body,
            icon: "/favicon.ico",
        });
    }
});