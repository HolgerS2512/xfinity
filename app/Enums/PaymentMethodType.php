<?php

namespace App\Enums;

enum PaymentMethodType: string
{
    case credit_card = 'credit_card';
    case paypal = 'paypal';
    case klarna = 'klarna';
    case instant_bank_transfer_klarna = 'instant_bank_transfer_klarna';
    case bank_transfer = 'bank_transfer';
    case sepa = 'sepa';
    case apple_pay = 'apple_pay';
    case google_pay = 'google_pay';
    case instant_bank_transfer = 'instant_bank_transfer';
    case direct_debit = 'direct_debit';
    case invoice_payment = 'invoice_payment';
    case nfc = 'nfc';
    case crypto = 'crypto';
    case cash_on_delivery = 'cash_on_delivery';
    case cash = 'cash';
    case coupon = 'coupon';
}
