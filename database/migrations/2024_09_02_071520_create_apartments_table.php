<?php

use App\ApartmentGardenType;
use App\ApartmentParking;
use App\ApartmentStatus;
use App\ApartmentSwimmingPoolType;
use App\ApartmentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->float('price')->nullable();
            $table->string('type')->default(ApartmentType::Villa);
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            //size
            $table->integer('area')->nullable();
            $table->string('parking')->default(ApartmentParking::Available);
            $table->string('swimming_pool_type')->default(ApartmentSwimmingPoolType::Shared);
            $table->string('garden_type')->default(ApartmentGardenType::Shared);
            $table->json('amenities')->nullable();
            $table->integer('is_featured')->default(0);
            $table->string('status')->default(ApartmentStatus::Available);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
