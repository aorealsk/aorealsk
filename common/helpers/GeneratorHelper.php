<?php

namespace common\helpers;

final class GeneratorHelper
{
    public static function promoCodeGenerator(
        array $itemLength,
        string $postFix,
        string $delimiter = '-',
        string $prefix = '',
        int $order = 0
    ) {
        $promoCode = $prefix != '' ? [$prefix] : [];
        for ($i = 0; $i < count($itemLength); $i++) {
            $promoCode[] = substr(
                str_shuffle(
                    str_repeat(
                        $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                        ceil($itemLength[$i] / strlen($x))
                    )
                ),
                1,
                $itemLength[$i]
            );
        }
        $promoCode[] = $postFix . $order;
        return implode($delimiter, $promoCode);
    }
}
