<?php

namespace common\helpers;

class LanguageHelper
{
    public static function calculatei18nCode(string $lang): string
    {
        return substr($lang, 0, 2);
    }
}
