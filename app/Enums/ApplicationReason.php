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
    const SICK_LEAVE =   1;
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
      ]
    ];
}
