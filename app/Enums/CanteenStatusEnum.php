<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class CanteenStatusEnum extends Enum
{
    const New =   1;
    const Approve =   2;
    const Reject = 3;

    public static $status = [self::New, self::Approve, self::Reject];
}
