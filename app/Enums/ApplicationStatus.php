<?php

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Arr;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ApplicationStatus extends Enum
{
    const PENDING = 1;
    const SUCCESS = 2;
    const CANCEL = 3;

    const PENDING_STR = 'pending';
    const SUCCESS_STR = 'success';
    const CANCEL_STR = 'cancel';
    const DELETE_STR = 'delete';
    const APPLICATION_STR = 'application';
    const PROPOSAL_STR = 'proposal';

    const CREATE_APPLICATION = 1;
    const CREATE_SUGGESTION = 2;


    CONST STATUS_APPLICATION = [
        self::PENDING => [
            'value' => self::PENDING,
            'name'  => 'Chờ duyệt'
        ],
        self::SUCCESS => [
            'value' => self::SUCCESS,
            'name'  => 'Đã duyệt'
        ],
        self::CANCEL => [
            'value' => self::CANCEL,
            'name'  => 'Hủy'
        ]
    ];

    const IS_APPLICATION = [
        self::CREATE_APPLICATION => [
            'value' => self::CREATE_APPLICATION,
            'name'  => 'Tạo đơn từ'
        ],
        self::CREATE_SUGGESTION => [
            'value' => self::CREATE_SUGGESTION,
            'name'  => 'Tạo đơn đề nghị'
        ]
    ];

    /**
     * @param $key
     * @return array|\ArrayAccess|mixed
     */

    static function getApplicationByKey($key)
    {
        return data_get(self::IS_APPLICATION ,$key);
    }

    /**
     * @param $status
     * @return array|\ArrayAccess|mixed
     */

    static function getStatusApplication($status)
    {
        return data_get(self::STATUS_APPLICATION ,$status);
    }

}
