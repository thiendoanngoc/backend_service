<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaskStatusEnum extends Enum
{
	const New = 1;
	const InProgress = 2;
	const Done = 3;
	const Reject = 4;
	const Cancel = 5;
	const Review = 6;

	public static $types = [self::New, self::InProgress, self::Done, self::Reject, self::Cancel, self::Review];
}
