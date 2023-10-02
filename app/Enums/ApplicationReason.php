<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ApplicationReason extends Enum
{
    const SICK_LEAVE = 1;
    const MATERNITY_LEAVE = 2;
    const LEAVE_WITHOUT_PAY = 3;
    const ANNUAL_LEAVE = 4;
    const ANOTHER_BREAK = 5;
    const TAKE_SICK_CHILD_LEAVE = 6;
    const REST_AFTER_ILLNESS = 7;

    const DATA = [
      self::SICK_LEAVE => [
          'value' => self::SICK_LEAVE,
          'name' => 'Nghỉ ốm',
          'note' => 'Tối đa: 30 Ngày/Năm'
      ],
        self::MATERNITY_LEAVE => [
            'value' => self::MATERNITY_LEAVE,
            'name' => 'Nghỉ thai sản',
            'note' => 'Tối đa: 180 Ngày/ Năm'
        ],
        self::LEAVE_WITHOUT_PAY => [
            'value' => self::LEAVE_WITHOUT_PAY,
            'name' => 'Nghỉ không lương',
            'note' => 'Tối đa: 20 Ngày/ Năm'
        ],
        self::ANNUAL_LEAVE => [
            'value' => self::ANNUAL_LEAVE,
            'name' => 'Nghỉ phép năm',
            'note' => 'Tối đa: 20 Ngày/ Năm'
        ],
        self::ANOTHER_BREAK => [
            'value' => self::ANOTHER_BREAK,
            'name' => 'Nghỉ khác',
            'note' => 'Tối đa: 3 Ngày/ Năm'
        ],
        self::TAKE_SICK_CHILD_LEAVE => [
            'value' => self::TAKE_SICK_CHILD_LEAVE,
            'name' => 'Nghỉ con ốm',
            'note' => 'Tối đa: 20 Ngày/ Năm'
        ],
        self::REST_AFTER_ILLNESS => [
            'value' => self::TAKE_SICK_CHILD_LEAVE,
            'name' => 'ghỉ dưỡng sức sau ốm đau',
            'note' => 'Tối đa: 10 Ngày/ Năm'
        ]
    ];
}
