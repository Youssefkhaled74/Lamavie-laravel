<?php

namespace App\Http\Controllers\Dashboard\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FcmTokenController extends Controller
{
    /**
     * Store or update the FCM token for the authenticated admin.
     */
    public function store(Request $request)
    {
        // Log the full incoming request
        \Illuminate\Support\Facades\Log::debug('FcmTokenController::store - Request received', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'content_type' => $request->header('Content-Type'),
            'all_headers' => $request->headers->all(),
            'all_input' => $request->all(),
            'has_fcm_token' => $request->has('fcm_token'),
            'fcm_token_length' => $request->has('fcm_token') ? strlen($request->input('fcm_token')) : 0,
        ]);

        try {
            $request->validate([
                'fcm_token' => 'required|string|max:1024',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::error('FcmTokenController::store - Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            throw $e;
        }

        $admin = auth('admin')->user() ?? auth()->user();
        if (! $admin) {
            \Illuminate\Support\Facades\Log::warning('FcmTokenController::store - No authenticated admin', [
                'guards_checked' => ['admin', 'default'],
                'session_id' => session()->getId(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $oldToken = $admin->fcm_token;
        $newToken = $request->input('fcm_token');
        
        $admin->fcm_token = $newToken;
        
        \Illuminate\Support\Facades\Log::info('FcmTokenController::store - Saving admin fcm_token', [
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'old_token' => $oldToken ? substr($oldToken, 0, 20) . '...' : null,
            'new_token' => substr($newToken, 0, 20) . '...',
            'new_token_full_length' => strlen($newToken),
            'token_changed' => $oldToken !== $newToken,
            'ip' => $request->ip(),
        ]);
        
        try {
            $saved = $admin->save();
            \Illuminate\Support\Facades\Log::info('FcmTokenController::store - Save result', [
                'saved' => $saved,
                'admin_id' => $admin->id,
                'fcm_token_in_db' => $admin->fresh()->fcm_token ? substr($admin->fresh()->fcm_token, 0, 20) . '...' : null,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('FcmTokenController::store - Save failed', [
                'admin_id' => $admin->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to save token', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'message' => 'FCM token saved',
            'admin_id' => $admin->id,
            'token_length' => strlen($newToken),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Debug route to display current admin FCM token status.
     */
    public function debug(Request $request)
    {
        $admin = auth('admin')->user() ?? auth()->user();
        
        if (! $admin) {
            return response()->json([
                'authenticated' => false,
                'message' => 'No admin authenticated',
            ], 401);
        }

        // Gather debugging info
        $tokenPresent = !empty($admin->fcm_token);
        $tokenPreview = $tokenPresent ? substr($admin->fcm_token, 0, 30) . '...' : null;

        $debugInfo = [
            'authenticated' => true,
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'has_fcm_token' => $tokenPresent,
            'token_preview' => $tokenPreview,
            'token_length' => $tokenPresent ? strlen($admin->fcm_token) : 0,
            'updated_at' => $admin->updated_at ? $admin->updated_at->toIso8601String() : null,
            'session_id' => session()->getId(),
            'guards' => [
                'admin_authenticated' => auth('admin')->check(),
                'default_authenticated' => auth()->check(),
            ],
            'notification_permission_instructions' => [
                'chrome' => 'Click lock icon in address bar → Site settings → Notifications',
                'firefox' => 'Click lock icon → Connection Secure → More Information → Permissions tab',
                'edge' => 'Click lock icon → Permissions for this site → Notifications',
            ],
            'service_worker_check' => [
                'instruction' => 'Open DevTools → Application → Service Workers',
                'expected_sw' => '/firebase-messaging-sw.js',
                'unregister_command' => "navigator.serviceWorker.getRegistrations().then(rs => rs.forEach(r => r.unregister()))",
            ],
            'test_token_post' => [
                'endpoint' => route('admin.fcm-token.store'),
                'method' => 'POST',
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-CSRF-TOKEN' => csrf_token(),
                ],
                'body_example' => json_encode(['fcm_token' => 'YOUR_TOKEN_HERE']),
            ],
        ];

        // Return as JSON for API calls or HTML for browser
        if ($request->wantsJson() || $request->is('*/json')) {
            return response()->json($debugInfo);
        }

        // Simple HTML view
        $html = '<!DOCTYPE html><html><head><title>Admin FCM Debug</title><style>
            body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
            h1 { color: #4ec9b0; }
            pre { background: #252526; padding: 15px; border-radius: 5px; overflow-x: auto; }
            .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
            .success { background: #28a745; color: white; }
            .warning { background: #ffc107; color: black; }
            .error { background: #dc3545; color: white; }
            button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin: 5px; }
            button:hover { background: #0056b3; }
        </style></head><body>';
        
        $html .= '<h1>🔍 Admin FCM Token Debug</h1>';
        
        if ($tokenPresent) {
            $html .= '<div class="status success">✅ FCM Token is present in database</div>';
        } else {
            $html .= '<div class="status error">❌ No FCM Token stored for this admin</div>';
        }
        
        $html .= '<h2>Admin Info</h2><pre>' . json_encode([
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'has_fcm_token' => $tokenPresent,
            'token_preview' => $tokenPreview,
            'token_length' => $debugInfo['token_length'],
        ], JSON_PRETTY_PRINT) . '</pre>';
        
        $html .= '<h2>Quick Actions</h2>';
        $html .= '<button onclick="checkPermission()">Check Notification Permission</button>';
        $html .= '<button onclick="checkServiceWorker()">Check Service Worker</button>';
        $html .= '<button onclick="testTokenRetrieval()">Test Token Retrieval</button>';
        $html .= '<button onclick="unregisterSW()">Unregister All Service Workers</button>';
        $html .= '<div id="output"></div>';
        
        $html .= '<h2>Full Debug Info</h2><pre>' . json_encode($debugInfo, JSON_PRETTY_PRINT) . '</pre>';
        
        $html .= '<script>
            const output = document.getElementById("output");
            function log(msg, type = "info") {
                const div = document.createElement("div");
                div.className = "status " + type;
                div.textContent = msg;
                output.appendChild(div);
                console.log(msg);
            }
            
            async function checkPermission() {
                output.innerHTML = "";
                const perm = Notification.permission;
                log("Notification permission: " + perm, perm === "granted" ? "success" : perm === "denied" ? "error" : "warning");
                if (perm === "default") {
                    log("Click to request permission...", "info");
                    const result = await Notification.requestPermission();
                    log("New permission: " + result, result === "granted" ? "success" : "error");
                }
            }
            
            async function checkServiceWorker() {
                output.innerHTML = "";
                if (!("serviceWorker" in navigator)) {
                    log("Service Workers not supported", "error");
                    return;
                }
                const regs = await navigator.serviceWorker.getRegistrations();
                log("Found " + regs.length + " service worker(s)", regs.length > 0 ? "success" : "warning");
                regs.forEach((r, i) => {
                    log(`SW ${i+1}: ${r.scope} - ${r.active ? r.active.scriptURL : "not active"}`, "info");
                });
            }
            
            async function testTokenRetrieval() {
                output.innerHTML = "";
                try {
                    const config = {
                        apiKey: "' . env('FIREBASE_API_KEY', '') . '",
                        authDomain: "' . env('FIREBASE_AUTH_DOMAIN', '') . '",
                        projectId: "' . env('FIREBASE_PROJECT_ID', '') . '",
                        messagingSenderId: "' . env('FIREBASE_MESSAGING_SENDER_ID', '') . '",
                        appId: "' . env('FIREBASE_APP_ID', '') . '",
                    };
                    const vapidKey = "' . env('FIREBASE_VAPID_KEY', '') . '";
                    
                    if (!config.messagingSenderId || !vapidKey) {
                        log("Firebase config missing! Check your .env file", "error");
                        return;
                    }
                    
                    log("Loading Firebase...", "info");
                    if (!window.firebase || !firebase.apps.length) {
                        firebase.initializeApp(config);
                    }
                    const messaging = firebase.messaging();
                    
                    const reg = await navigator.serviceWorker.register("/firebase-messaging-sw.js");
                    log("SW registered: " + reg.scope, "success");
                    
                    const token = await messaging.getToken({ vapidKey: vapidKey, serviceWorkerRegistration: reg });
                    log("Token obtained: " + token.substring(0, 30) + "...", "success");
                    
                    // POST to server
                    const response = await fetch("' . route('admin.fcm-token.store') . '", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "' . csrf_token() . '",
                        },
                        body: JSON.stringify({ fcm_token: token })
                    });
                    const data = await response.json();
                    log("Server response: " + JSON.stringify(data), response.ok ? "success" : "error");
                } catch (e) {
                    log("Error: " + e.message, "error");
                    console.error(e);
                }
            }
            
            async function unregisterSW() {
                output.innerHTML = "";
                const regs = await navigator.serviceWorker.getRegistrations();
                for (let r of regs) {
                    await r.unregister();
                    log("Unregistered: " + r.scope, "success");
                }
                log("All service workers unregistered. Reload the page.", "success");
            }
        </script>';
        
        $html .= '<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-app-compat.js"></script>';
        $html .= '<script src="https://www.gstatic.com/firebasejs/9.22.2/firebase-messaging-compat.js"></script>';
        $html .= '</body></html>';
        
        return response($html);
    }
}
