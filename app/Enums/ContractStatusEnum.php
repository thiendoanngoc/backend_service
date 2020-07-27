<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ContractStatusEnum extends Enum
{
	const InProgress = 1;
	const Completed = 2;
	const Cancelled = 3;

	public static $types = [self::InProgress, self::Completed, self::Cancelled];
}
