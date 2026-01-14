@php
    $title = 'Lamavie Admin Login';
    $roleLabel = 'Lamavie Admin';
    $action = route('admin.login');
    $hiddenInputs = ['fcm_token' => ''];
    $includeFirebase = true;
    $description = 'Sign in to your admin dashboard';
@endphp

@include('auth.login-layout', compact('title','roleLabel','action','hiddenInputs','includeFirebase','description'))

@push('login-scripts')
<script>
    (function(){
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_API_KEY', '') }}",
            authDomain: "{{ env('FIREBASE_AUTH_DOMAIN', '') }}",
            projectId: "{{ env('FIREBASE_PROJECT_ID', '') }}",
            messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID', '') }}",
            appId: "{{ env('FIREBASE_APP_ID', '') }}",
            vapidKey: "{{ env('FIREBASE_VAPID_KEY', '') }}"
        };

        const form = document.querySelector('form');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!firebaseConfig.messagingSenderId || !firebaseConfig.vapidKey) { form.submit(); return; }
            try {
                if (!window.firebase || !firebase.apps.length) {
                    firebase.initializeApp(firebaseConfig);
                }
            } catch (initE) { console.error('Firebase init error', initE); }

            const messaging = (function(){ try { return firebase.messaging(); } catch(e){ return null; }})();
            let registration = null;
            if ('serviceWorker' in navigator) {
                try { registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js'); } catch(e){ console.warn('SW register failed', e); }
            }

            const permission = await Notification.requestPermission();
            if (permission === 'granted' && registration && messaging) {
                try {
                    const token = await messaging.getToken({ vapidKey: firebaseConfig.vapidKey, serviceWorkerRegistration: registration });
                    if (token) { const input = document.getElementById('fcm_token'); if (input) input.value = token; }
                } catch (tokenErr) { console.warn('getToken failed', tokenErr); }
            }
            form.submit();
        });
    })();
</script>
@endpush
