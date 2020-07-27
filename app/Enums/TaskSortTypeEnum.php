<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TaskSortTypeEnum extends Enum
{
    const Newest =   1;
    const Oldest =   2;
    const DeadlineAsc = 3;
    const DeadlineDesc = 4;

    public static $types = [self::Newest, self::Oldest, self::DeadlineAsc, self::DeadlineDesc];
}
