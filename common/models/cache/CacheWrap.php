<?php

namespace common\models\cache;

interface CacheWrap
{
    public static function isLoaded(): bool;
    public static function load(): void;
    public static function clear(): void;
}
