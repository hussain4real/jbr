<?php

namespace App;

enum ApartmentParking: string
{
    case Garage = 'garage';
    case Available = 'available';
    case Unavailable = 'unavailable';
}
