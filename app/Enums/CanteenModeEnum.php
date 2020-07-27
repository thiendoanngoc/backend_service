<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class CanteenModeEnum extends Enum
{
    const Personal = 0;
    const Department = 1;
    const Guest = 2;

    public static $modes = [self::Personal, self::Department, self::Guest];
}
