<?php

declare(strict_types=1);

namespace Test\helpers;

class Cache
{
    private static ?SimpleFileCache $cache = null;

    public static function getCache(): SimpleFileCache
    {
        if (!self::$cache instanceof SimpleFileCache) {
            $cache = new SimpleFileCache();
            $cache->changeConfig(
                [
                    'cacheDirectory' => dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR,
                    'gzipCompression' => false,
                ]
            );
            self::$cache = $cache;
        }

        return self::$cache;
    }
}
