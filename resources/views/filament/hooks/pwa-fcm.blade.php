@auth
    @php
        $firebaseWeb = config('services.firebase_web');
        $firebaseConfigured = filled($firebaseWeb['api_key'] ?? null)
            && filled($firebaseWeb['project_id'] ?? null)
            && filled($firebaseWeb['messaging_sender_id'] ?? null)
            && filled($firebaseWeb['app_id'] ?? null)
            && filled($firebaseWeb['vapid_key'] ?? null);
        $firebaseClientConfig = [
            'apiKey' => $firebaseWeb['api_key'] ?? null,
            'authDomain' => $firebaseWeb['auth_domain'] ?? null,
            'projectId' => $firebaseWeb['project_id'] ?? null,
            'storageBucket' => $firebaseWeb['storage_bucket'] ?? null,
            'messagingSenderId' => $firebaseWeb['messaging_sender_id'] ?? null,
            'appId' => $firebaseWeb['app_id'] ?? null,
            'vapidKey' => $firebaseWeb['vapid_key'] ?? null,
        ];
    @endphp

    <script>
        window.AluminumProPwa = {
            deviceTokenUrl: @json(route('admin.device-token')),
            csrfToken: @json(csrf_token()),
            firebaseConfigured: @json($firebaseConfigured),
            firebase: @json($firebaseClientConfig),
        };
    </script>

    @if ($firebaseConfigured)
        <script type="module" src="{{ asset('js/admin-pwa.js') }}"></script>
    @else
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            }
        </script>
    @endif
@endauth
