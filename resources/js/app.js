import './bootstrap';
import { generateFCMToken } from './firebase';

const fcmToken = localStorage.getItem('fcm_token');
if (!fcmToken || fcmToken.trim() === '') {
    generateFCMToken();
}
