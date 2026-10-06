<?php

namespace App\Support;

use App\Models\Setting;
use App\Notifications\NewLead;
use Illuminate\Support\Facades\Notification;

class AdminAlert
{
    /** Email the Temu Estates admin. Never breaks the visitor's form if email is not set up. */
    public static function send(string $subject, array $details): void
    {
        $to = Setting::get('email');

        if (! $to) {
            return;
        }

        try {
            Notification::route('mail', $to)->notify(new NewLead($subject, $details));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
