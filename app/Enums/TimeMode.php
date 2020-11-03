<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static NoLimit()
 * @method static static TimeLimit()
 * @method static static PerQuestion()
 */
final class TimeMode extends Enum implements LocalizedEnum
{
    const NoLimit = 1;
    const TimeLimit = 2;
    const PerSection = 3;
    const PerQuestion = 4;
}
