<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('listing_type')->index();           // sale | rent
            $table->string('status')->default('for_sale')->index();

            $table->decimal('price', 15, 2)->index();
            $table->string('currency', 3)->default('ETB');

            $table->foreignId('location_id')->constrained()->restrictOnDelete();
            $table->foreignId('property_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();

            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->decimal('size_sqm', 10, 2)->nullable();
            $table->string('floor')->nullable();
            $table->boolean('has_parking')->default(false);
            $table->string('furnished')->nullable();            // furnished | semi_furnished | unfurnished
            $table->string('completion_status')->nullable();    // e.g. Completed, Off-plan
            $table->date('available_from')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('video_url')->nullable();            // YouTube / Vimeo link

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true)->index();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('amenity_property', function (Blueprint $table) {
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->primary(['property_id', 'amenity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenity_property');
        Schema::dropIfExists('properties');
    }
};
