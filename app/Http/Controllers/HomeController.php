<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'featured' => Property::published()->featured()->with(['location', 'propertyType', 'media'])->latest()->take(6)->get(),
            'locations' => Location::orderBy('name')->get(),
            'types' => PropertyType::orderBy('name')->get(),
        ]);
    }
}
