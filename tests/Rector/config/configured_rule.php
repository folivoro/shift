<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Folivoro\Shift\Rector\NormalizeSlothRegistrationPropertiesRector;

return RectorConfig::configure()
    ->withRules([
        NormalizeSlothRegistrationPropertiesRector::class,
    ]);