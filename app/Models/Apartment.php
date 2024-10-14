<?php

namespace App\Models;

use App\ApartmentGardenType;
use App\ApartmentParking;
use App\ApartmentStatus;
use App\ApartmentSwimmingPoolType;
use App\ApartmentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'type',
        'bedrooms',
        'bathrooms',
        'area',
        'parking',
        'swimming_pool_type',
        'garden_type',
        'amenities',
        'is_featured',
        'status',
    ];
     /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'float',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
            'area' => 'integer',
            'type'=>ApartmentType::class,
            'parking'=>ApartmentParking::class,
            'swimming_pool_type'=>ApartmentSwimmingPoolType::class,
            'gardent_type'=>ApartmentGardenType::class,
            'amenities' => 'array',
            'is_featured' => 'boolean',
            'status' => ApartmentStatus::class,
        ];
    }
  
}
