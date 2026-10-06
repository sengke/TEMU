<?php

namespace App\Http\Controllers;

use App\Models\Property;

class PropertyController extends Controller
{
    public function buy()
    {
        return view('properties.index', ['heading' => 'Properties for sale', 'listing' => 'sale', 'category' => null]);
    }

    public function rent()
    {
        return view('properties.index', ['heading' => 'Properties for rent', 'listing' => 'rent', 'category' => null]);
    }

    public function commercial()
    {
        return view('properties.index', ['heading' => 'Commercial properties', 'listing' => null, 'category' => 'commercial']);
    }

    public function show(Property $property)
    {
        abort_unless($property->is_published, 404);

        $property->load(['location', 'propertyType', 'amenities', 'media']);

        $similar = Property::published()
            ->where('id', '!=', $property->id)
            ->where('listing_type', $property->listing_type)
            ->where('location_id', $property->location_id)
            ->with(['location', 'propertyType', 'media'])
            ->take(3)->get();

        return view('properties.show', compact('property', 'similar'));
    }
}
