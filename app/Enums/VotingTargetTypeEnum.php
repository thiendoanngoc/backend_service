<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class VotingTargetTypeEnum extends Enum
{
    const All = 0;
    const Department = 1;
    const Specific = 2;

    public static $types = [self::All, self::Department, self::Specific];
}
