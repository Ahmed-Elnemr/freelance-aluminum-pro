<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class FirebaseMessagingServiceWorkerController extends Controller
{
    public function __invoke(): Response
    {
        $firebase = config('services.firebase_web');

        $script = <<<'JS'
importScripts('https://www.gstatic.com/firebasejs/10.14.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.14.1/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: __API_KEY__,
    authDomain: __AUTH_DOMAIN__,
    projectId: __PROJECT_ID__,
    storageBucket: __STORAGE_BUCKET__,
    messagingSenderId: __MESSAGING_SENDER_ID__,
    appId: __APP_ID__,
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    const title = payload.notification?.title || 'ALUMINUM PRO';
    const options = {
        body: payload.notification?.body || '',
        icon: '/images/pwa/icon-192.png',
        data: payload.data || {},
    };

    self.registration.showNotification(title, options);
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification?.data?.url || '/admin';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if ('focus' in client) {
                    client.navigate(targetUrl);

                    return client.focus();
                }
            }

            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
JS;

        $script = str_replace(
            [
                '__API_KEY__',
                '__AUTH_DOMAIN__',
                '__PROJECT_ID__',
                '__STORAGE_BUCKET__',
                '__MESSAGING_SENDER_ID__',
                '__APP_ID__',
            ],
            [
                json_encode($firebase['api_key'] ?? ''),
                json_encode($firebase['auth_domain'] ?? ''),
                json_encode($firebase['project_id'] ?? ''),
                json_encode($firebase['storage_bucket'] ?? ''),
                json_encode($firebase['messaging_sender_id'] ?? ''),
                json_encode($firebase['app_id'] ?? ''),
            ],
            $script
        );

        return response($script, 200, [
            'Content-Type' => 'application/javascript; charset=UTF-8',
            'Service-Worker-Allowed' => '/',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
