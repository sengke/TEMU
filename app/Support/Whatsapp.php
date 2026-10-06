<?php

namespace App\Support;

use App\Models\Setting;

class Whatsapp
{
    /** Ethiopian mobile: 09xxxxxxxx, 07xxxxxxxx, +2519xxxxxxxx, 2519xxxxxxxx */
    public const PHONE_REGEX = '/^(\+?251|0)?[79]\d{8}$/';

    /** Turn 0911223344 / +251911223344 / 911223344 into 251911223344 (what wa.me needs). */
    public static function international(?string $number): string
    {
        $digits = preg_replace('/\D/', '', (string) $number);

        if (str_starts_with($digits, '251')) {
            return $digits;
        }
        if (str_starts_with($digits, '0')) {
            return '251' . substr($digits, 1);
        }

        return '251' . $digits;
    }

    /** Click-to-chat link to Temu Estates with an optional pre-filled message. */
    public static function url(?string $message = null): string
    {
        $url = 'https://wa.me/' . self::international(Setting::get('whatsapp_number', '251911000000'));

        return $message ? $url . '?text=' . rawurlencode($message) : $url;
    }
}
