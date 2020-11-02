<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Private()
 * @method static static Public()
 */
final class VisibilityStatus extends Enum
{
    const Private = 1;
    const Public = 2;
}
