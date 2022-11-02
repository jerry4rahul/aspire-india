<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class LoanStatus extends Enum
{
    const PENDING  = 1;
    const APPROVED = 2;
    const PAID     = 3;
}
