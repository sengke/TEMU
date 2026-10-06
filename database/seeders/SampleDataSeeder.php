<?php

namespace Database\Seeders;

use App\Enums\Furnished;
use App\Enums\ListingType;
use App\Enums\PropertyStatus;
use App\Models\Amenity;
use App\Models\Location;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Database\Seeder;

/** SAMPLE listings so you can see the site working. Delete them from the admin before launch. */
class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'title' => 'Luxury 3 Bedroom Apartment', 'location' => 'Bole', 'type' => 'Apartment',
                'listing_type' => ListingType::Sale, 'price' => 27000000, 'bedrooms' => 3, 'bathrooms' => 3, 'size_sqm' => 142,
                'floor' => '7th', 'has_parking' => true, 'is_featured' => true, 'completion_status' => 'Completed',
                'latitude' => 8.9960, 'longitude' => 38.7870,
                'description' => "A bright, well-finished apartment in the heart of Bole, close to restaurants, schools and the airport road.\n\nOpen-plan living area, fitted kitchen, a master suite and a private balcony.",
                'amenities' => ['Parking', 'Security', 'Elevator', 'Generator'],
            ],
            [
                'title' => '2 Bedroom Apartment', 'location' => 'Bole', 'type' => 'Apartment',
                'listing_type' => ListingType::Rent, 'price' => 45000, 'bedrooms' => 2, 'bathrooms' => 2, 'size_sqm' => 82,
                'floor' => '3rd', 'has_parking' => true, 'furnished' => Furnished::Furnished, 'available_from' => now()->addWeeks(2),
                'latitude' => 8.9990, 'longitude' => 38.7900,
                'description' => 'A comfortable furnished two-bedroom apartment, ready to move in.',
                'amenities' => ['Parking', 'Security', 'Generator'],
            ],
            [
                'title' => 'Modern 4 Bedroom Villa', 'location' => 'CMC', 'type' => 'Villa',
                'listing_type' => ListingType::Sale, 'price' => 65000000, 'bedrooms' => 4, 'bathrooms' => 4, 'size_sqm' => 320,
                'has_parking' => true, 'is_featured' => true, 'completion_status' => 'Completed',
                'description' => 'A spacious villa with a private garden and room for the whole family.',
                'amenities' => ['Garden', 'Security', 'Generator', "Children's Area"],
            ],
            [
                'title' => 'Studio Apartment', 'location' => 'Kazanchis', 'type' => 'Apartment',
                'listing_type' => ListingType::Rent, 'price' => 28000, 'bedrooms' => 1, 'bathrooms' => 1, 'size_sqm' => 45,
                'furnished' => Furnished::Semi, 'status' => PropertyStatus::Reserved,
                'description' => 'A compact studio in a central location, ideal for a single professional.',
                'amenities' => ['Security', 'Elevator'],
            ],
            [
                'title' => 'Office Space', 'location' => 'Kazanchis', 'type' => 'Office',
                'listing_type' => ListingType::Rent, 'price' => 120000, 'size_sqm' => 200, 'has_parking' => true,
                'description' => 'Open-plan office floor with meeting rooms and reception area.',
                'amenities' => ['Parking', 'Security', 'Elevator', 'Generator'],
            ],
            [
                'title' => 'Residential Plot', 'location' => 'Ayat', 'type' => 'Land',
                'listing_type' => ListingType::Sale, 'price' => 18500000, 'size_sqm' => 500,
                'description' => 'A well-located residential plot with clear access to the main road.',
                'amenities' => [],
            ],
        ];

        foreach ($samples as $s) {
            $amenities = $s['amenities'];
            $location = $s['location'];
            $type = $s['type'];
            unset($s['amenities'], $s['location'], $s['type']);

            $property = Property::firstOrCreate(
                ['title' => $s['title']],
                $s + [
                    'location_id' => Location::where('name', $location)->value('id'),
                    'property_type_id' => PropertyType::where('name', $type)->value('id'),
                ]
            );

            $property->amenities()->sync(Amenity::whereIn('name', $amenities)->pluck('id'));
        }
    }
}
