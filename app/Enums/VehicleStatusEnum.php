<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class VehicleStatusEnum extends Enum
{
    const Available = 1;
	const Booked = 2;
	const NotAvailable = 3;
}
