<?php

declare(strict_types=1);

/**
 * Application entry point — Phase 7 Implementation
 *
 * Thin bootstrap: loads Composer autoloader and delegates all output
 * to HelloWorld::run(). No business logic lives here.
 *
 * Requirements satisfied: 2.1, 2.2, 2.3
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\HelloWorld;

$app = new HelloWorld();
$app->run();
