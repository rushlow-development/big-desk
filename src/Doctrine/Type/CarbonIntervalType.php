<?php

namespace App\Doctrine\Type;

use Carbon\CarbonInterval;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateIntervalType;
use Doctrine\DBAL\Types\Exception\InvalidFormat;

class CarbonIntervalType extends DateIntervalType
{
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?CarbonInterval
    {
        if (null === $value || $value instanceof CarbonInterval) {
            return $value;
        }

        $negative = false;

        if (isset($value[0]) && ('+' === $value[0] || '-' === $value[0])) {
            $negative = '-' === $value[0];
            $value = substr($value, 1);
        }

        try {
            $interval = new CarbonInterval($value);

            if ($negative) {
                $interval->invert = 1;
            }

            return $interval;
        } catch (\Throwable $exception) {
            throw InvalidFormat::new($value, static::class, self::FORMAT, $exception);
        }
    }
}
