<?php
namespace common\models\accounting\invoice;
class InvoiceType
{
    public const INVOICE = 0;
    public const DEPOSIT_INVOICE = 1;
    public const CREDIT_ADVICE = 2;
    public const PRICE_OFFER = 3;


    public static function getInvoiceTypeCode(int $type): string
    {
        $code = [
            static::INVOICE => 'FAK',
            static::DEPOSIT_INVOICE => 'ZAL',
            static::CREDIT_ADVICE => 'DOB',
            static::PRICE_OFFER => 'CEN',
        ];

        return $code[$type];
    }
}