<?php

namespace App\Interfaces;

use App\Models\Transaction;

interface PaymentProcessorInterface
{
    public function disburseMomo(Transaction $transaction): Transaction;

    public function disburseAirtime(Transaction $transaction): Transaction;

    public function disburseDataBundle(Transaction $transaction): Transaction;

    public function collect(Transaction $transaction): Transaction;

    public function checkStatus(Transaction $transaction): ?Transaction;
}
