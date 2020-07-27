<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SellingStuffStatusEnum extends Enum
{
    const Open = 1;
	const Sold = 2;
	const Closed = 3;

	public static $types = [self::Open, self::Sold, self::Closed];
}
