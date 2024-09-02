<?php

namespace App;

enum ApartmentSwimmingPoolType: string
{
    case Private = 'private';
    case Shared = 'shared';
    case None = 'none';
}
