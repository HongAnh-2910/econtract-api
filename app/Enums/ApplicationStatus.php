<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ApplicationStatus extends Enum
{
    const PENDING =  'Chờ duyệt';
    const CREATE_APPLICATION = 1;
    const CREATE_SUGGESTION = 2;

}
