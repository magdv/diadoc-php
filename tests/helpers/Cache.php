<?php

declare(strict_types=1);

namespace Test\helpers;

use DivineOmega\DOFileCache\DOFileCache;

class Cache
{
    private static ?DOFileCache $cache = null;

    public static function getCache(): DOFileCache
    {
        if (self::$cache === null) {
            $cache = new DOFileCache();
            $cache->changeConfig(
                [
                    "cacheDirectory" => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR,
                    'gzipCompression' => false
                ]
            );

            self::$cache = $cache;
        }

        return self::$cache;
    }
}
