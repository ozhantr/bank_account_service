<?php

namespace App\Type;

enum TransactionType: string
{
    case deposit = 'deposit';
    case withdraw = 'withdraw';
}
