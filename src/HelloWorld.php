<?php

declare(strict_types=1);

namespace App;

class HelloWorld
{
    public function getMessage(): string
    {
        return 'Hello World';
    }

    public function getAppName(): string
    {
        return 'CircleCI Demo';
    }

    public function run(): void
    {
        echo "=== {$this->getAppName()} ===" . PHP_EOL;
        echo $this->getMessage() . PHP_EOL;
        echo 'PHP Version: ' . PHP_VERSION . PHP_EOL;
    }
}
