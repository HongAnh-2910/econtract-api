<?php

    namespace App\Enums;

    use BenSampo\Enum\Enum;

    /**
     * @method static static OptionOne()
     * @method static static OptionTwo()
     * @method static static OptionThree()
     */
    final class DocumentStatus extends Enum
    {
        const ALL = 'all';
        const TRASH = 'trash';
        const ALL_PRIVATE = 'all_private';
        const  SHARE = 'share';
    }
