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

    const TRANSFER = 2;
    const CASH = 1;

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

    const PAYMENT = [
       self::CASH =>  [
           'value' => self::CASH,
           'name' => 'Thanh toán tiền mặt'
       ],
        self::TRANSFER => [
            'value' => self::TRANSFER,
            'name' => 'Thanh toán tiền chuyển khoản'
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

    /**
     * @param $status
     * @return array|mixed
     */

    static function getPayment($status)
    {
        return data_get(self::PAYMENT , $status ?? []);
    }
}
