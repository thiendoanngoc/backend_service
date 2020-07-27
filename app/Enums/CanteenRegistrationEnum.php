<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class CanteenRegistrationEnum extends Enum
{
    const Breakfast = 1;
    const Lunch = 2;

    public static $types = [self::Breakfast, self::Lunch];
}
