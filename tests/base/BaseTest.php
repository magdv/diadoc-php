<?php

declare(strict_types=1);

namespace Test\base;

use PHPUnit\Framework\TestCase;
use Test\helpers\ApiClient;

class BaseTest extends TestCase
{
    /**
     * @var \Test\helpers\ApiClient
     */
    private $api;

    public static function setUpBeforeClass(): void
    {
        $params = parse_ini_file(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env');

        foreach ($params as $name => $value) {
            putenv($name . '=' . $value);
        }
    }

    protected function auth(): ApiClient
    {
        if (null === $this->api) {
            $this->api = new ApiClient();
        }
        return $this->api;
    }
}
