<?php

declare(strict_types=1);

namespace Test\Unit;

use MagDv\Diadoc\Auth\AuthenticateV3AuthMode;
use MagDv\Diadoc\Auth\OidcAuthFlowAuthMode;
use MagDv\Diadoc\Auth\OidcDeviceFlowAuthMode;
use MagDv\Diadoc\DiadocApi;
use MagDv\Diadoc\Entities\Http\HttpTimeouts;
use MagDv\Diadoc\Interfaces\HttpTimeoutsAwareInterface;
use Test\base\BaseTest;

class HttpTimeoutsTest extends BaseTest
{
    protected function setUp(): void
    {
        if (! \defined('CURLOPT_TIMEOUT')) {
            self::markTestSkipped('Для тестов таймаутов требуется ext-curl');
        }
    }

    public function testDefaultValues(): void
    {
        $timeouts = new HttpTimeouts();

        self::assertSame(30, $timeouts->timeout);
        self::assertSame(30, $timeouts->maxDuration);
    }

    public function testCurlOptions(): void
    {
        self::assertSame(
            [
                \CURLOPT_CONNECTTIMEOUT => 30,
                \CURLOPT_LOW_SPEED_LIMIT => 1,
                \CURLOPT_LOW_SPEED_TIME => 30,
                \CURLOPT_TIMEOUT => 30,
            ],
            (new HttpTimeouts(30, 30))->toCurlOptions()
        );
    }

    public function testIdleTimeoutIsClampedByMaxDuration(): void
    {
        $timeouts = new HttpTimeouts(30, 5);

        self::assertSame(
            [
                \CURLOPT_CONNECTTIMEOUT => 5,
                \CURLOPT_LOW_SPEED_LIMIT => 1,
                \CURLOPT_LOW_SPEED_TIME => 5,
                \CURLOPT_TIMEOUT => 5,
            ],
            $timeouts->toCurlOptions()
        );
    }

    public function testZeroMaxDurationDisablesHardLimit(): void
    {
        self::assertSame(
            [
                \CURLOPT_CONNECTTIMEOUT => 30,
                \CURLOPT_LOW_SPEED_LIMIT => 1,
                \CURLOPT_LOW_SPEED_TIME => 30,
                \CURLOPT_TIMEOUT => 0,
            ],
            (new HttpTimeouts(30, 0))->toCurlOptions()
        );
    }

    public function testTimeoutMustBePositive(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new HttpTimeouts(0, 30);
    }

    public function testMaxDurationMustNotBeNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new HttpTimeouts(30, -1);
    }

    public function testLegacyOptionsAreUsedWhenTimeoutsAreNotConfigured(): void
    {
        $api = new TestableDiadocApi();

        self::assertNull($api->getTimeouts());
        self::assertSame([\CURLOPT_TIMEOUT => 20], $api->exposeBuildTimeoutOptions());
    }

    public function testConfiguredTimeoutsAreUsed(): void
    {
        $timeouts = new HttpTimeouts(10, 20);
        $api = new TestableDiadocApi($timeouts);

        self::assertSame($timeouts, $api->getTimeouts());
        self::assertSame($timeouts->toCurlOptions(), $api->exposeBuildTimeoutOptions());
    }

    public function testResetToNullRestoresLegacyOptions(): void
    {
        $api = new TestableDiadocApi(new HttpTimeouts(10, 20));
        $api->setTimeouts(null);

        self::assertNull($api->getTimeouts());
        self::assertSame([\CURLOPT_TIMEOUT => 20], $api->exposeBuildTimeoutOptions());
    }

    public function testLegacyConstructorPropagatesTimeoutsToAuthMode(): void
    {
        $timeouts = new HttpTimeouts(10, 20);
        $api = new DiadocApi('dd-client', 'https://diadoc-api.kontur.ru/', false, null, $timeouts);

        $authMode = $this->extractAuthMode($api);

        self::assertInstanceOf(HttpTimeoutsAwareInterface::class, $authMode);
        self::assertSame($timeouts, $authMode->getHttpTimeouts());
    }

    public function testCreatePropagatesTimeoutsToAuthMode(): void
    {
        $timeouts = new HttpTimeouts(10, 20);
        $authMode = new OidcAuthFlowAuthMode(
            'client',
            'secret',
            'https://identity.kontur.ru',
            'https://diadoc-api.kontur.ru/'
        );

        $api = DiadocApi::create($authMode, 'https://diadoc-api.kontur.ru/', false, null, $timeouts);

        self::assertSame($timeouts, $api->getTimeouts());
        self::assertSame($timeouts, $authMode->getHttpTimeouts());
    }

    public function testAuthModesKeepLegacyTimeoutsByDefault(): void
    {
        self::assertNull((new AuthenticateV3AuthMode('dd-client'))->getHttpTimeouts());
        self::assertNull($this->createOidcFlowMode()->getHttpTimeouts());
        self::assertNull(
            (new OidcDeviceFlowAuthMode('client', 'secret', 'https://identity.kontur.ru', 'https://diadoc-api.kontur.ru/'))
                ->getHttpTimeouts()
        );
    }

    public function testAuthModesAcceptTimeoutsInConstructor(): void
    {
        $timeouts = new HttpTimeouts(10, 20);

        self::assertSame($timeouts, (new AuthenticateV3AuthMode('dd-client', timeouts: $timeouts))->getHttpTimeouts());
        self::assertSame($timeouts, $this->createOidcFlowMode($timeouts)->getHttpTimeouts());
    }

    public function testCreateDoesNotOverrideAuthModeTimeoutsWhenNonePassed(): void
    {
        $timeouts = new HttpTimeouts(10, 20);
        $authMode = $this->createOidcFlowMode($timeouts);

        $api = DiadocApi::create($authMode, 'https://diadoc-api.kontur.ru/');

        self::assertNull($api->getTimeouts());
        self::assertSame($timeouts, $authMode->getHttpTimeouts());
    }

    public function testSetTimeoutsPropagatesToAuthModeAndCanReset(): void
    {
        $authMode = new OidcAuthFlowAuthMode(
            'client',
            'secret',
            'https://identity.kontur.ru',
            'https://diadoc-api.kontur.ru/'
        );
        $api = DiadocApi::create($authMode, 'https://diadoc-api.kontur.ru/');

        $timeouts = new HttpTimeouts(5, 15);
        $api->setTimeouts($timeouts);
        self::assertSame($timeouts, $authMode->getHttpTimeouts());

        $api->setTimeouts(null);
        self::assertNull($authMode->getHttpTimeouts());
    }

    private function createOidcFlowMode(?HttpTimeouts $timeouts = null): OidcAuthFlowAuthMode
    {
        return new OidcAuthFlowAuthMode(
            'client',
            'secret',
            'https://identity.kontur.ru',
            'https://diadoc-api.kontur.ru/',
            null,
            null,
            $timeouts
        );
    }

    private function extractAuthMode(DiadocApi $api): object
    {
        $property = new \ReflectionProperty(DiadocApi::class, 'authMode');
        $property->setAccessible(true);

        $authMode = $property->getValue($api);
        self::assertIsObject($authMode);

        return $authMode;
    }
}
