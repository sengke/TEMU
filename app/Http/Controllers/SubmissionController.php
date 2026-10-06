<?php

namespace App\Http\Controllers;

use App\Enums\ListingType;
use App\Models\PropertySubmission;
use App\Models\PropertyType;
use App\Support\AdminAlert;
use App\Support\Whatsapp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubmissionController extends Controller
{
    public function create()
    {
        return view('list-your-property', ['types' => PropertyType::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        foreach (['phone', 'whatsapp'] as $field) {
            if ($request->filled($field)) {
                $request->merge([$field => preg_replace('/[\s\-]/', '', $request->input($field))]);
            }
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:' . Whatsapp::PHONE_REGEX],
            'whatsapp' => ['nullable', 'regex:' . Whatsapp::PHONE_REGEX],
            'location' => ['required', 'string', 'max:160'],
            'property_type_id' => ['required', 'exists:property_types,id'],
            'listing_type' => ['required', Rule::enum(ListingType::class)],
            'expected_price' => ['nullable', 'numeric', 'min:0'],
            'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
            'size_sqm' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:3000'],
            'additional_info' => ['nullable', 'string', 'max:3000'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5 MB each
            'website' => ['prohibited'], // honeypot
        ], [
            'phone.regex' => 'Enter a valid Ethiopian phone number, for example 0911 22 33 44.',
            'whatsapp.regex' => 'Enter a valid Ethiopian WhatsApp number, for example 0911 22 33 44.',
            'photos.*.max' => 'Each photo must be 5 MB or smaller.',
        ]);

        $submission = PropertySubmission::create(collect($data)->except(['photos', 'website'])->all());

        foreach ($request->file('photos', []) as $photo) {
            $submission->addMedia($photo)->toMediaCollection('photos');
        }

        AdminAlert::send('New property submitted - ' . $submission->name, [
            'Owner' => $submission->name,
            'Phone number' => $submission->phone,
            'WhatsApp number' => $submission->whatsapp,
            'Location' => $submission->location,
            'Sale or rent' => $submission->listing_type->label(),
            'Expected price' => $submission->expected_price ? number_format($submission->expected_price) . ' ETB' : null,
            'Description' => $submission->description,
            'Photos uploaded' => $submission->getMedia('photos')->count(),
        ]);

        return redirect()->route('list.create')
            ->with('success', 'Thank you! Your property has been submitted. Our team will review it and contact you.');
    }
}
