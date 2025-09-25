<?php

namespace App\Http\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class TransactionRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(['deposit', 'withdraw'])]
        public string $type = '',
        #[Assert\NotBlank]
        // #[Assert\Regex(pattern: '//')] to do : Add regex for amount validation
        public string $amount = ''
    ) {
    }
}
