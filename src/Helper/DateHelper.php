<?php

namespace MagDv\Diadoc\Helper;

use DateTime;

class DateHelper
{
    public static function convertDateTimeToTicks(DateTime $dateTime = null): ?int
    {
        if (is_null($dateTime)) {
            return null;
        }
        return $dateTime->getTimestamp() * 10000000 + 621355968000000000;
    }

    public static function convertTicksToDateTime(?int $ticks): ?DateTime
    {
        if (is_null($ticks)) {
            return null;
        }
        $timestamp = floor(($ticks - 621355968000000000)/10000000);

        $dateTime = new DateTime();
        $dateTime->setTimestamp($timestamp);
        return $dateTime;
    }
}
