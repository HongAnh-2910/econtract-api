<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Arr;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class StatusIsActive extends Enum
{
    const NOT_ACTIVE =   0;
    const ACTIVE =   1;

    const IS_ACTIVE = [
        self::ACTIVE => [
            'name' => 'Đã được kích hoạt',
            'value' => self::ACTIVE
        ],
        self::NOT_ACTIVE => [
            'name' => 'Chưa được kích hoạt',
            'value' => self::NOT_ACTIVE
        ],

    ];
    static function getIsActiveKeyValue($value)
    {
        return Arr::get(self::IS_ACTIVE ,$value);
    }

}
