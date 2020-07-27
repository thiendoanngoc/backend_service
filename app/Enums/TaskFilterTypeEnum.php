<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TaskFilterTypeEnum extends Enum
{
    const Assign = 1;
	const Assigned = 2;
	const Done = 3;
	const Outdate = 4;

	public static $types = [self::Assign, self::Assigned, self::Done, self::Outdate];
}
