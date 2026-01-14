<?php

namespace App\Channels;

use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseChannel
{
    public function send($notifiable, Notification $notification)
    {
        Log::info('📲 FirebaseChannel::send - Method called', [
            'notification_class' => get_class($notification),
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'notifiable_email' => $notifiable->email ?? 'N/A',
            'has_fcm_token' => !empty($notifiable->fcm_token),
            'fcm_token_preview' => $notifiable->fcm_token ? substr($notifiable->fcm_token, 0, 20) . '...' : null,
            'fcm_token_length' => $notifiable->fcm_token ? strlen($notifiable->fcm_token) : 0,
        ]);
        
        if (!method_exists($notification, 'toFirebase')) {
            Log::error('❌ FirebaseChannel::send - toFirebase method missing', [
                'notification_class' => get_class($notification),
            ]);
            return;
        }

        try {
            Log::info('🔄 FirebaseChannel::send - Calling toFirebase()', [
                'notifiable_id' => $notifiable->id,
            ]);
            
            $message = $notification->toFirebase($notifiable);
            
            Log::info('📦 FirebaseChannel::send - toFirebase() returned', [
                'message_is_null' => $message === null,
                'message_type' => gettype($message),
                'message_class' => is_object($message) ? get_class($message) : null,
            ]);

            if (!$message) {
                Log::warning('⚠️ FirebaseChannel::send - No message returned', [
                    'notifiable_id' => $notifiable->id,
                    'has_fcm_token' => !empty($notifiable->fcm_token),
                ]);
                return;
            }
            
            Log::info('🚀 FirebaseChannel::send - Sending via Firebase::messaging()', [
                'notifiable_id' => $notifiable->id,
                'notifiable_email' => $notifiable->email ?? 'N/A',
            ]);

            try {
                try {
                    $payload = method_exists($message, 'jsonSerialize') ? $message->jsonSerialize() : (method_exists($message, 'toArray') ? $message->toArray() : (array) $message);
                    Log::debug('🔧 FirebaseChannel::send - Message payload', ['payload' => $payload, 'notifiable_id' => $notifiable->id]);
                } catch (\Throwable $t) {
                    Log::warning('⚠️ FirebaseChannel::send - Failed to serialize message', ['err' => $t->getMessage(), 'notifiable_id' => $notifiable->id]);
                }

                $result = Firebase::messaging()->send($message);

                Log::info('✅ FirebaseChannel::send - Firebase send completed', [
                    'notifiable_id' => $notifiable->id,
                    'notifiable_email' => $notifiable->email ?? 'N/A',
                    'result_type' => gettype($result),
                    'result' => is_scalar($result) ? $result : (is_array($result) ? $result : json_encode($result)),
                ]);

                Log::debug('🔎 FirebaseChannel::send - Raw send result', [
                    'notifiable_id' => $notifiable->id,
                    'raw_result' => is_scalar($result) ? $result : (is_array($result) ? $result : json_encode($result)),
                ]);
            } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                // Let the outer catch block handle this, rethrow to be caught below
                throw $e;
            }
            
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error('❌ FirebaseChannel::send - MessagingException', [
                'error' => $e->getMessage(),
                'previous' => $e->getPrevious()?->getMessage(),
                'notifiable_id' => $notifiable->id,
                'notifiable_email' => $notifiable->email ?? 'N/A',
                'errors' => method_exists($e, 'errors') ? $e->errors() : null,
                'trace' => $e->getTraceAsString(),
            ]);

            // Attempt safe cleanup for common token-related errors
            try {
                $msg = strtolower($e->getMessage());
                if (str_contains($msg, 'notregistered') || str_contains($msg, 'unregistered') || str_contains($msg, 'invalid') || str_contains($msg, 'registration token')) {
                    try {
                        $notifiable->fcm_token = null;
                        $notifiable->save();
                        Log::warning('⚠️ FirebaseChannel::send - Cleared invalid fcm_token', ['notifiable_id' => $notifiable->id]);
                    } catch (\Throwable $t) {
                        Log::error('❌ FirebaseChannel::send - Failed clearing token', ['err' => $t->getMessage(), 'notifiable_id' => $notifiable->id]);
                    }
                }
            } catch (\Throwable $_) {
                // swallow to avoid masking the original logging
            }
        } catch (\Exception $e) {
            Log::error('❌ FirebaseChannel::send - General exception', [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'notifiable_id' => $notifiable->id,
                'notifiable_email' => $notifiable->email ?? 'N/A',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
        
        Log::info('🏁 FirebaseChannel::send - Method completed', [
            'notifiable_id' => $notifiable->id,
        ]);
    }
}
