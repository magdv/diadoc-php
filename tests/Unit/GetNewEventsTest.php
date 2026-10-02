<?php

declare(strict_types=1);

namespace Test\Unit;

use DateTime;
use Diadoc\Proto\Events\BoxEvent;
use Diadoc\Proto\Events\BoxEventList;
use MagDv\Diadoc\Helper\DateHelper;
use Test\base\BaseTest;
use Test\enums\ConfigNames;

class GetNewEventsTest extends BaseTest
{
    public function testGetNewEventsV8(): void
    {
        $api = $this->auth()->getApi();

        $events = $api->getNewEventsV8(getenv(ConfigNames::FROM_BOX_ID));

        self::assertInstanceOf(BoxEventList::class, $events);
        self::assertGreaterThanOrEqual(0, $events->getTotalCount());
    }

    public function testGetNewEventsV8WithLimit(): void
    {
        $api = $this->auth()->getApi();

        $events = $api->getNewEventsV8(getenv(ConfigNames::FROM_BOX_ID), limit: 1);

        self::assertInstanceOf(BoxEventList::class, $events);
        self::assertLessThanOrEqual(1, $events->getEvents()->count());
    }

    public function testGetNewEventsV8Pagination(): void
    {
        $api = $this->auth()->getApi();
        $boxId = getenv(ConfigNames::FROM_BOX_ID);

        $firstPage = $api->getNewEventsV8($boxId, limit: 5);
        if ($firstPage->getEvents()->count() === 0) {
            self::markTestSkipped('В ящике нет событий для проверки постраничности.');
        }

        $firstPageKeys = [];
        $lastIndexKey = null;
        /** @var BoxEvent $event */
        foreach ($firstPage->getEvents() as $event) {
            self::assertNotEmpty($event->getIndexKey());
            $firstPageKeys[] = $event->getIndexKey();
            $lastIndexKey = $event->getIndexKey();
        }
        self::assertNotNull($lastIndexKey);

        $secondPage = $api->getNewEventsV8($boxId, afterIndexKey: $lastIndexKey, limit: 5);

        self::assertInstanceOf(BoxEventList::class, $secondPage);

        /** @var BoxEvent $nextEvent */
        foreach ($secondPage->getEvents() as $nextEvent) {
            self::assertNotEmpty($nextEvent->getIndexKey());
            self::assertNotContains(
                $nextEvent->getIndexKey(),
                $firstPageKeys,
                'Фильтр afterIndexKey вернул событие, которое уже было на предыдущей странице.'
            );
        }
    }

    public function testGetNewEventsV8ByTime(): void
    {
        $api = $this->auth()->getApi();
        $boxId = getenv(ConfigNames::FROM_BOX_ID);

        $fromTicks = DateHelper::convertDateTimeToTicks(new DateTime('-1 day'));
        $toTicks = DateHelper::convertDateTimeToTicks(new DateTime('+1 day'));
        self::assertNotNull($fromTicks);
        self::assertNotNull($toTicks);

        $events = $api->getNewEventsV8(
            $boxId,
            timestampFromTicks: $fromTicks,
            timestampToTicks: $toTicks
        );

        self::assertInstanceOf(BoxEventList::class, $events);

        /** @var BoxEvent $event */
        foreach ($events->getEvents() as $event) {
            if (!$event->hasMessage()) {
                continue;
            }
            $ticks = $event->getMessage()->getTimestampTicks();
            self::assertGreaterThanOrEqual($fromTicks, $ticks);
            self::assertLessThanOrEqual($toTicks, $ticks);
        }
    }
}
