<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class DeviceStatusEnum extends Enum
{
	const InWareHouse = 1;
	const InUse = 2;

	public static $types = [self::InWareHouse, self::InUse];
}
