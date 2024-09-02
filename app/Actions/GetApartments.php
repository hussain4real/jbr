<?php

namespace App\Actions;

use App\Models\Apartment;

class GetApartments
{
//    /**
//      * Get all apartments, optionally filtering by "featured".
//      */
//     public function __invoke($onlyFeatured = false)
//     {
//         // Fetch all apartments or only featured ones
//         if ($onlyFeatured) {
//             return Apartment::where('is_featured', true)->get();
//         }

//         return Apartment::all(); // Get all apartments
//     }

/**
     * Get all apartments.
     */
    public function __invoke()
    {
        return Apartment::all();
    }
}
