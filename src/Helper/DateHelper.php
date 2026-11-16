<?php

namespace MagDv\Diadoc\Helper;

use DateTime;

final class DateHelper
{
    public static function convertDateTimeToTicks(?DateTime $dateTime = null): ?int
    {
        if (!$dateTime instanceof \DateTime) {
            return null;
        }

        return $dateTime->getTimestamp() * 10_000_000 + 621_355_968_000_000_000;
    }

    public static function convertTicksToDateTime(?int $ticks): ?DateTime
    {
        if ($ticks === null) {
            return null;
        }

        $timestamp = floor(($ticks - 621_355_968_000_000_000) / 10_000_000);

        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        return $dateTime;
    }
}
