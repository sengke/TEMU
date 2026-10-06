<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Property;
use App\Models\PropertySubmission;
use App\Models\PropertyType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Notification::fake();
        $this->seed();
    }

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('Building trust')->assertSee('Featured properties');
    }

    public function test_buy_page_shows_only_properties_for_sale(): void
    {
        $this->get('/buy')->assertOk()
            ->assertSee('Luxury 3 Bedroom Apartment')
            ->assertDontSee('Studio Apartment');
    }

    public function test_rent_page_shows_only_rentals(): void
    {
        $this->get('/rent')->assertOk()
            ->assertSee('2 Bedroom Apartment')
            ->assertDontSee('Modern 4 Bedroom Villa');
    }

    public function test_commercial_page_shows_commercial_properties(): void
    {
        $this->get('/commercial')->assertOk()
            ->assertSee('Office Space')
            ->assertDontSee('Luxury 3 Bedroom Apartment');
    }

    public function test_property_page_shows_details_and_status(): void
    {
        $property = Property::where('title', 'Luxury 3 Bedroom Apartment')->firstOrFail();

        $this->get(route('properties.show', $property))->assertOk()
            ->assertSee('27,000,000 ETB')
            ->assertSee('For sale')
            ->assertSee('Request viewing');
    }

    public function test_unpublished_property_is_hidden(): void
    {
        $property = Property::first();
        $property->update(['is_published' => false]);

        $this->get(route('properties.show', $property))->assertNotFound();
    }

    public function test_sold_property_stays_visible_with_sold_label(): void
    {
        $property = Property::where('title', 'Luxury 3 Bedroom Apartment')->firstOrFail();
        $property->update(['status' => 'sold']);

        $this->get(route('properties.show', $property))->assertOk()->assertSee('Sold');
    }

    public function test_viewing_request_is_saved_as_a_lead(): void
    {
        $property = Property::first();

        $this->post(route('inquiry.store'), [
            'type' => 'viewing',
            'property_id' => $property->id,
            'name' => 'Test Visitor',
            'phone' => '0911 22 33 44',
            'message' => 'I would like to visit.',
        ])->assertSessionHasNoErrors()->assertRedirect();

        $this->assertDatabaseHas('inquiries', ['name' => 'Test Visitor', 'phone' => '0911223344', 'type' => 'viewing']);
        $this->assertSame(1, Inquiry::count());
    }

    public function test_inquiry_rejects_a_bad_phone_number(): void
    {
        $this->post(route('inquiry.store'), ['type' => 'general', 'name' => 'Test', 'phone' => '12345'])
            ->assertSessionHasErrors('phone');

        $this->assertSame(0, Inquiry::count());
    }

    public function test_spam_bots_filling_the_hidden_field_are_blocked(): void
    {
        $this->post(route('inquiry.store'), ['type' => 'general', 'name' => 'Bot', 'phone' => '0911223344', 'website' => 'http://spam'])
            ->assertSessionHasErrors('website');
    }

    public function test_property_submission_is_received_for_review(): void
    {
        $this->post(route('list.store'), [
            'name' => 'Owner One',
            'phone' => '0922334455',
            'location' => 'Bole, near the airport',
            'property_type_id' => PropertyType::first()->id,
            'listing_type' => 'sale',
            'expected_price' => 5000000,
        ])->assertSessionHasNoErrors()->assertRedirect(route('list.create'));

        $this->assertSame('submitted', PropertySubmission::first()->status->value);
    }

    public function test_sitemap_lists_published_properties(): void
    {
        $property = Property::first();

        $this->get('/sitemap.xml')->assertOk()->assertSee(route('properties.show', $property), false);
    }

    public function test_static_pages_load(): void
    {
        foreach (['/about', '/contact', '/list-your-property'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
