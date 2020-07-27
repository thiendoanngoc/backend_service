<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TaskReminderTypeEnum extends Enum
{
    const NoRemind =   0;
    const MinuteRemind =   1;
    const HourRemind = 2;
    const DateRemind = 3;
    const MonthRemind = 4;
    const DateTimeRemind = 5;

    public static $types = [self::NoRemind, self::MinuteRemind, self::HourRemind, self::DateRemind, self::MonthRemind, self::DateTimeRemind];
}
