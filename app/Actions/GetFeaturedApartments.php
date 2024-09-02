<?php

namespace App\Actions;

use App\Models\Apartment;

class GetFeaturedApartments
{
    /**
     * Get only featured apartments.
     */
    public function __invoke()
    {
        return Apartment::where('is_featured', true)->get();
    }
  
}
