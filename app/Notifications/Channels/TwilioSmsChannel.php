<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioSmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toTwilioSms($notifiable);
        $phone = $notifiable->phone ?? $notifiable->routeNotificationForTwilioSms();

        if (! $phone) {
            return;
        }

        $sid = config('boarding-house.twilio.sid');
        $token = config('boarding-house.twilio.token');
        $from = config('boarding-house.twilio.from');

        if (! $sid || ! $token || ! $from) {
            Log::info('[SMS mock] To: '.$phone.' — '.$message);

            return;
        }

        Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $phone,
                'Body' => $message,
            ]);
    }
}
