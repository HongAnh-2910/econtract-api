<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TypeDelete extends Enum
{
    const SOFT_DELETE =   1;
    const DELETE =   2;
}
