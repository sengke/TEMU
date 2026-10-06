<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\City;
use App\Models\Location;
use App\Models\PropertyType;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $city = City::firstOrCreate(['slug' => 'addis-ababa'], ['name' => 'Addis Ababa']);

        foreach (['Bole', 'Kazanchis', 'CMC', 'Ayat', 'Megenagna', 'Piassa', 'Sarbet', 'Gerji', 'Summit', 'Lebu'] as $name) {
            Location::firstOrCreate(['city_id' => $city->id, 'name' => $name]);
        }

        $types = [
            'residential' => ['Apartment', 'House', 'Villa', 'Land'],
            'commercial' => ['Shop', 'Office', 'Building', 'Warehouse', 'Commercial Land', 'Other Commercial Unit'],
        ];
        foreach ($types as $category => $names) {
            foreach ($names as $name) {
                PropertyType::firstOrCreate(['name' => $name], ['category' => $category]);
            }
        }

        foreach (['Gym', 'Swimming Pool', 'Parking', 'Security', 'Elevator', 'Generator', 'Garden', "Children's Area"] as $name) {
            Amenity::firstOrCreate(['name' => $name]);
        }

        // PLACEHOLDER contact details - change these in the admin or here, then re-seed.
        foreach ([
            'whatsapp_number' => '251911000000',
            'phone' => '+251911000000',
            'email' => 'info@temuestates.com',
            'address' => 'Bole, Addis Ababa, Ethiopia',
            'facebook' => '',
            'instagram' => '',
            'telegram' => '',
        ] as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
