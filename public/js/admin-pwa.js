import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.14.1/firebase-app.js';
import { deleteToken, getMessaging, getToken, onMessage, isSupported } from 'https://www.gstatic.com/firebasejs/10.14.1/firebase-messaging.js';

const config = window.AluminumProPwa;
const PROJECT_CACHE_KEY = 'aluminum_pro_firebase_project';
const TOKEN_CACHE_KEY = 'aluminum_pro_fcm_token';

async function ensureServiceWorkers() {
    if (!('serviceWorker' in navigator)) {
        return null;
    }

    const previousProject = localStorage.getItem(PROJECT_CACHE_KEY);
    const currentProject = config?.firebase?.projectId ?? null;

    if (previousProject && currentProject && previousProject !== currentProject) {
        const registrations = await navigator.serviceWorker.getRegistrations();
        await Promise.all(registrations.map((registration) => registration.unregister()));
        localStorage.removeItem(TOKEN_CACHE_KEY);
    }

    await navigator.serviceWorker.register('/sw.js').catch(() => null);

    return navigator.serviceWorker.register('/firebase-messaging-sw.js');
}

function browserUuid() {
    const key = 'aluminum_pro_web_uuid';
    const existing = localStorage.getItem(key);

    if (existing) {
        return existing;
    }

    const uuid = crypto.randomUUID();
    localStorage.setItem(key, uuid);

    return uuid;
}

async function registerDeviceToken(token) {
    const response = await fetch(config.deviceTokenUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            device_token: token,
            device_type: 'web',
            uuid: browserUuid(),
        }),
    });

    if (!response.ok) {
        console.error('[PWA] Failed to save device token', response.status);
    }
}

async function boot() {
    if (!config?.firebaseConfigured) {
        if ('serviceWorker' in navigator) {
            await navigator.serviceWorker.register('/sw.js').catch(() => {});
        }

        return;
    }

    const supported = await isSupported().catch(() => false);

    if (!supported) {
        await ensureServiceWorkers();

        return;
    }

    const registration = await ensureServiceWorkers();

    if (!registration) {
        return;
    }

    const permission = await Notification.requestPermission();

    if (permission !== 'granted') {
        console.warn('[PWA] Notification permission not granted');

        return;
    }

    const app = initializeApp({
        apiKey: config.firebase.apiKey,
        authDomain: config.firebase.authDomain,
        projectId: config.firebase.projectId,
        storageBucket: config.firebase.storageBucket,
        messagingSenderId: config.firebase.messagingSenderId,
        appId: config.firebase.appId,
    });

    const messaging = getMessaging(app);

    try {
        await deleteToken(messaging);
    } catch (_) {
        // Ignore when there is no existing token.
    }

    const token = await getToken(messaging, {
        vapidKey: config.firebase.vapidKey,
        serviceWorkerRegistration: registration,
    });

    if (token) {
        localStorage.setItem(PROJECT_CACHE_KEY, config.firebase.projectId);
        localStorage.setItem(TOKEN_CACHE_KEY, token);
        console.log('[PWA] Firebase project:', config.firebase.projectId);
        console.log('[PWA] Sender ID:', config.firebase.messagingSenderId);
        console.log('[PWA] Device token (use this in /pwa-test/...):', token);
        await registerDeviceToken(token);
    }

    onMessage(messaging, (payload) => {
        const title = payload.notification?.title ?? 'ALUMINUM PRO';
        const options = {
            body: payload.notification?.body ?? '',
            icon: '/images/pwa/icon-192.png',
            data: payload.data ?? {},
        };

        if (Notification.permission === 'granted') {
            new Notification(title, options);
        }
    });
}

boot().catch((error) => {
    console.error('[PWA] boot failed', error);
});
