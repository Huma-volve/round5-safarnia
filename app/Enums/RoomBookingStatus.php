<?php

namespace App\Enums;

enum RoomBookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case CheckedIn = 'checked-in';
    case CheckedOut = 'checked-out';
}
