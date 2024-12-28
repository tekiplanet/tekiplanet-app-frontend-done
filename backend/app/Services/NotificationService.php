<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Events\NewNotification;
use Illuminate\Support\Str;

class NotificationService
{
    public function send($data, $users)
    {
        // Create the notification
        $notification = Notification::create([
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'icon' => $data['icon'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'data' => $data['extra_data'] ?? null,
        ]);

        // If users is a single user, convert to array
        $users = is_array($users) ? $users : [$users];

        // Attach notification to users and queue broadcasts
        foreach ($users as $user) {
            $notification->users()->attach($user->id, [
                'id' => Str::uuid(),
                'read' => false
            ]);

            // Queue the broadcast event
            dispatch(function () use ($notification, $user) {
                event(new NewNotification($notification, $user));
            })->onQueue('default');
        }

        return $notification;
    }
} 