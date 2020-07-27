<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationTypeEnum extends Enum
{
    const TaskAssign =   0;
    const TaskAssigned =   1;
    const Announcement =   2;
    const Contract = 3;
    const Other = 4;
    const Voting = 5;
}
