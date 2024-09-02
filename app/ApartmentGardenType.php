<?php

namespace App;

enum ApartmentGardenType: string
{
    case Private = 'private';
    case Shared = 'shared';
    case None = 'none';
}
