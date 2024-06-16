<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->import(__DIR__ . '/.lando/ecs-voeller.php');
    $ecsConfig->paths([
        __DIR__ . '/.scripts',
        __DIR__ . '/assets',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/templates',
        __DIR__ . '/translations',
    ]);
    $ecsConfig->skip([
        __DIR__ . '/node_modules/*',
        __DIR__ . '/var/*',
        __DIR__ . '/vendor/*',
    ]);
};
