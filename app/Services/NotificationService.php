<?php

namespace App\Services;

use App\Models\Notification;
use GPBMetadata\Google\Api\Auth;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationService
{

    public function index()
    {
        return auth()->user()->notifications;
    }

    public function send($user, $title, $message, $notificationType, $type = 'basic')
    {

        // Initialize the Firebase Factory with the service account
        $factory = (new Factory)->withServiceAccount(env('FIREBASE_CREDENTIALS'));

        // Create the Messaging instance
        $messaging = $factory->createMessaging();

        // Prepare the notification array
        $notification = [
            'title' => $title,
            'body' => $message,
            'sound' => 'default',
        ];

        // Additional data payload
        $data = [
            'type' => $type,
            'id' => $user['id'],
            'message' => $message,
        ];
        // $user['fcm_token'] = 'ADFDADFSfDsfdslj29fkdjf';
        // Create the CloudMessage instance
        $cloudMessage = CloudMessage::withTarget('token', $user['fcm_token'])
            ->withNotification($notification)
            ->withData($data);
        try {
            // Send the notification
            $messaging->send($cloudMessage);
            // Save the notification to the database
            Notification::query()->create([
                'type' => 'App\Notifications' . $notificationType,
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $user['id'],
                'data' => json_encode([
                    'user' => $user['name'],
                    'message' => $message,
                    'title' => $title,
                ]), // The data of the notification
            ]);
            return 1;
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error($e->getMessage());
            return 0;
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            Log::error($e->getMessage());
            return 0;
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->findOrFail($notificationId);
        if (isset($notification)) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function destroy($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        if (isset($notification)) {
            $notification->delete();
        }
    }
}
