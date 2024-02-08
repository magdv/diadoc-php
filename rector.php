<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\JMSSetList;

return static function (RectorConfig $rectorConfig): void
{
    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    // register a single rule
    $rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    // define sets of rules
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::CODING_STYLE,
        SetList::PSR_4,
        SetList::NAMING,
        SetList::TYPE_DECLARATION_STRICT,
        JMSSetList::ANNOTATIONS_TO_ATTRIBUTES,
    ]);

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

};
