// Ensure minimal event handlers are present at initial evaluation to satisfy
// browsers / Firebase SDK checks that require handlers to exist immediately.
try {
    self.addEventListener('push', function(e) {
        // placeholder: actual handling will be done later when initialized
        console.debug('firebase-messaging-sw: early push event');
    });
    self.addEventListener('notificationclick', function(e) {
        console.debug('firebase-messaging-sw: early notificationclick');
        try { e.notification && e.notification.close(); } catch (er) {}
    });
    self.addEventListener('pushsubscriptionchange', function(e) {
        console.debug('firebase-messaging-sw: early pushsubscriptionchange');
    });
} catch (e) {
    // ignore in environments where self is not available
}

importScripts('https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js');

// Basic startup log for debugging
try {
    console.debug('firebase-messaging-sw: loaded', { href: self.location && self.location.href, scope: self.registration && self.registration.scope });
} catch (e) {
    // ignore in non-browser contexts
}

// This service worker accepts Firebase client config from the page via postMessage.
// Register critical event handlers immediately (at initial evaluation) so the
// worker can respond to push/notification events even before any runtime init.

// Take control immediately on install/activate so the updated worker's
// top-level event handlers are registered without waiting for page reload.
self.addEventListener('install', (event) => {
    console.debug('firebase-messaging-sw: install event, calling skipWaiting()');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.debug('firebase-messaging-sw: activate event, claiming clients');
    event.waitUntil((async () => {
        try {
            await self.clients.claim();
        } catch (e) {
            console.warn('clients.claim() failed', e);
        }
    })());
});

let messaging = null;
let firebaseInitialized = false;

function safeShowNotification(title, options) {
    try {
        return self.registration.showNotification(title, options || {});
    } catch (e) {
        console.error('showNotification failed', e);
    }
}

function payloadToNotification(payload) {
    const notification = (payload && payload.notification) || {};
    const data = payload && payload.data ? payload.data : {};
    const title = notification.title || data.title || 'Notification';
    const body = notification.body || data.body || '';
    const options = Object.assign({}, notification, {
        body: body,
        data: Object.assign({}, data),
        icon: notification.icon || '/favicon.ico'
    });
    return { title, options };
}

// Handle push events even if firebase isn't initialized yet
self.addEventListener('push', function(event) {
    try {
        if (!event) return;
        let payload = null;
        if (event.data) {
            try {
                payload = event.data.json();
            } catch (e) {
                // Not JSON, use text
                payload = { notification: { body: event.data.text() } };
            }
        }

        if (payload) {
            const { title, options } = payloadToNotification(payload);
            event.waitUntil(safeShowNotification(title, options));
            return;
        }

        // If no payload, simply wake up clients
        event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(() => {}));
    } catch (err) {
        console.error('Error in push handler', err);
    }
});

// Handle notification click to focus/open client
self.addEventListener('notificationclick', function(event) {
    try {
        event.notification.close();
        const data = event.notification.data || {};
        const clickAction = data.click_action || data.url || '/admin';
        event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            for (let i = 0; i < windowClients.length; i++) {
                const client = windowClients[i];
                if (client.url && client.url.includes(clickAction)) {
                    return client.focus();
                }
            }
            return clients.openWindow(clickAction);
        }));
    } catch (err) {
        console.error('Error in notificationclick handler', err);
    }
});

// Handle subscription changes (try to re-subscribe if possible)
self.addEventListener('pushsubscriptionchange', function(event) {
    console.warn('pushsubscriptionchange event fired');
    event.waitUntil((async () => {
        try {
            // If we have a VAPID key from init, attempt to resubscribe
            const vapidKey = (self.__FIREBASE_CONFIG && self.__FIREBASE_CONFIG.vapidKey) || null;
            if (!vapidKey) return;
            const newSub = await self.registration.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: urlBase64ToUint8Array(vapidKey) });
            // Could postMessage to clients to notify about new subscription
            const all = await clients.matchAll({ includeUncontrolled: true });
            for (const c of all) {
                c.postMessage({ type: 'NEW_PUSH_SUBSCRIPTION', subscription: newSub.toJSON() });
            }
        } catch (e) {
            console.error('Failed to handle pushsubscriptionchange', e);
        }
    })());
});

// Helper to convert VAPID base64 string to Uint8Array
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

// Initialize Firebase runtime when client posts config
function initFirebase(config) {
    try {
        console.debug('initFirebase called in SW', { configProvided: !!config, firebaseInitialized });
        if (!config || firebaseInitialized) {
            console.debug('initFirebase: skipping because config missing or already initialized', { config, firebaseInitialized });
            return;
        }
        // store config for subscriptionchange handler
        self.__FIREBASE_CONFIG = Object.assign({}, config);
        try {
            firebase.initializeApp(config);
            console.debug('firebase.initializeApp succeeded in SW');
        } catch (e) {
            console.error('firebase.initializeApp failed in SW', e, { config });
            throw e;
        }
        messaging = firebase.messaging();
        firebaseInitialized = true;

        // Register background message handler (Firebase SDK)
        messaging.onBackgroundMessage(function(payload) {
            try {
                console.debug('onBackgroundMessage payload in SW', payload);
                const { title, options } = payloadToNotification(payload);
                safeShowNotification(title, options);
            } catch (e) {
                console.error('Error showing background notification', e, payload);
            }
        });
    } catch (e) {
        console.error('Failed to initialize firebase in SW', e);
    }
}

// If page included a global config before registration (rare), try to init it
if (typeof self.__FIREBASE_CONFIG !== 'undefined') {
    initFirebase(self.__FIREBASE_CONFIG);
}

// Listen for messages from the client page (used to send runtime config)
self.addEventListener('message', (event) => {
    if (!event || !event.data) return;
    const data = event.data;
    if (data.type === 'INIT_FIREBASE' && data.config) {
        initFirebase(data.config);
    }
});

