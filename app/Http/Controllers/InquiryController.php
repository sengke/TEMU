<?php

namespace App\Http\Controllers;

use App\Enums\InquiryType;
use App\Models\Inquiry;
use App\Support\AdminAlert;
use App\Support\Whatsapp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        // Remove spaces/dashes so "0911 22 33 44" passes validation.
        foreach (['phone', 'whatsapp'] as $field) {
            if ($request->filled($field)) {
                $request->merge([$field => preg_replace('/[\s\-]/', '', $request->input($field))]);
            }
        }

        $data = $request->validate([
            'type' => ['required', Rule::enum(InquiryType::class)],
            'property_id' => ['nullable', 'exists:properties,id'],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:' . Whatsapp::PHONE_REGEX],
            'whatsapp' => ['nullable', 'regex:' . Whatsapp::PHONE_REGEX],
            'email' => ['nullable', 'email', 'max:160'],
            'message' => ['nullable', 'string', 'max:2000'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'website' => ['prohibited'], // honeypot: real people leave this hidden field empty
        ], [
            'phone.regex' => 'Enter a valid Ethiopian phone number, for example 0911 22 33 44.',
            'whatsapp.regex' => 'Enter a valid Ethiopian WhatsApp number, for example 0911 22 33 44.',
        ]);

        $inquiry = Inquiry::create(collect($data)->except('website')->all());

        AdminAlert::send('New ' . strtolower($inquiry->type->label()) . ' - ' . $inquiry->name, [
            'Customer name' => $inquiry->name,
            'Phone number' => $inquiry->phone,
            'WhatsApp number' => $inquiry->whatsapp,
            'Property' => $inquiry->property?->title,
            'Message' => $inquiry->message,
            'Preferred viewing date' => $inquiry->preferred_date?->format('j M Y'),
            'Received' => $inquiry->created_at->format('j M Y, H:i'),
        ]);

        return back()->with('success', 'Thank you. Temu Estates will contact you shortly.');
    }
}
