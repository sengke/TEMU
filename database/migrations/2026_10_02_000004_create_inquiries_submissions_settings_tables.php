<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('inquiry');   // inquiry | viewing | general
            $table->string('name');
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->text('message')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('status')->default('new');     // new | contacted | closed
            $table->timestamps();
        });

        Schema::create('property_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('location');
            $table->foreignId('property_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('listing_type');                // sale | rent
            $table->decimal('expected_price', 15, 2)->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->decimal('size_sqm', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('additional_info')->nullable();
            $table->string('status')->default('submitted');
            $table->text('admin_notes')->nullable();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete(); // created listing
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('property_submissions');
        Schema::dropIfExists('inquiries');
    }
};
