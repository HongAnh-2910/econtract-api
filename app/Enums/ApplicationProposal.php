<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ApplicationProposal extends Enum
{
    const TRADING_ASSETS_TABLE = 1;
    const PROPOSED_PURCHASE = 2;
    const PAYMENT_ORDER = 3;
    const REQUESTS_FOR_ADVANCES = 4;

    const DATA = [
        self::TRADING_ASSETS_TABLE  => [
            'value' => self::TRADING_ASSETS_TABLE,
            'name'  => 'Bàn giao tài sản',
            'note'  => 'Tên đề xuất "Tên thành viên - Phòng ban - BBBG + tên hàng/DV'
        ],
        self::PROPOSED_PURCHASE     => [
            'value' => self::PROPOSED_PURCHASE,
            'name'  => 'Đề nghị mua hàng',
            'note'  => 'Tên đề xuất "Tên thành viên - Phòng ban - Đề nghị mua + tên hàng'
        ],
        self::PAYMENT_ORDER         => [
            'value' => self::PAYMENT_ORDER,
            'name'  => 'Đề nghị thanh toán',
            'note'  => 'Tên đề xuất "Tên thành viên - Phòng ban - Đề nghị thanh toán + tên hàng/DV'
        ],
        self::REQUESTS_FOR_ADVANCES => [
            'value' => self::REQUESTS_FOR_ADVANCES,
            'name'  => 'Đề nghị tạm ứng',
            'note'  => 'Tên đề xuất "Tên thành viên - Phòng ban - thanh toán tạm ứng + ngày tạm ứng'
        ],
    ];

    /**
     * @param $key
     * @return array|\ArrayAccess|mixed
     */

    static function getApplicationProposalReason($key)
    {
        return data_get(self::DATA, $key);
    }
}
