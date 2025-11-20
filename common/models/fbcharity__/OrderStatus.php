<?php

namespace common\models\fbcharity;

final class OrderStatus
{
    public const PENDING = 0;
    public const PAID = 1;
    public const DELETED = 2;
    public const FINALIZED = 3;

    public static function getCssOptions(int $value): string
    {
        $status = [
            static::PENDING => 'background-color:  #fad7a0;',
            static::PAID => 'background-color: #bdf0a2;',
            static::DELETED => 'background-color: #7f8c8d; text-decoration: line-through; color: white;',
            static::FINALIZED => 'info',
        ];

        return $status[$value];
    }

    public static function getLabel(int $value): string
    {
        $status = [
            static::PENDING => 'čaká',
            static::PAID => 'zaplatená',
            static::DELETED => 'zrušená',
            static::FINALIZED => 'sfinalizovaná',  // listky boli poslane
        ];

        return $status[$value];
    }

    public static function getStatuses(): array
    {
        return [
            static::PENDING => 'čaká',
            static::PAID => 'zaplatená',
            static::DELETED => 'zrušená',
            static::FINALIZED => 'sfinalizovaná',  // listky boli poslane
        ];
    }
}
