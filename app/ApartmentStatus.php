<?php

namespace App;

enum ApartmentStatus: string
{
    case Available = 'available';
    case Unavailable = 'unavailable';
    case Reserved = 'reserved';
    case UnderMaintenance = 'under_maintenance';


}
