<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaskPriorityEnum extends Enum
{
	const Low = 1;
	const Normal = 2;
	const High = 3;

	public static $types = [self::Low, self::Normal, self::High];
}
