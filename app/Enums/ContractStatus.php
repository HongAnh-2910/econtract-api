<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ContractStatus extends Enum
{
    const WAIT_APPROVAL = 1;
    const CLOSE_APPROVAL = 2;
    const SUCCESS = 3;
    const CANCELED = 4;

    const PERSONAL =  'personal';
    const COMPANY = 'company';

    const STATUSES = [
        self::WAIT_APPROVAL => [
            'value' => self::WAIT_APPROVAL,
            'name'  => 'Chờ phê duyệt'
        ],
        self::CLOSE_APPROVAL => [
            'value' => self::CLOSE_APPROVAL,
            'name'  => 'Phê duyệt chặt chẽ'
        ],
        self::SUCCESS => [
            'value' => self::SUCCESS,
            'name'  => 'Thành công'
        ],
        self::CANCELED => [
            'value' => self::CANCELED,
            'name'  => 'Hủy'
        ],
    ];

    /**
     * @param $status
     * @return array|mixed
     */

    static function getStatus($status)
    {
        return data_get(self::STATUSES , $status ?? []);
    }
}
