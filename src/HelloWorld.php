<?php

declare(strict_types=1);

namespace App;

/**
 * HelloWorld — Phase 7 Implementation
 *
 * Encapsulates all greeting logic and formatted output for the CircleCI
 * Hello World demo. All three methods are independently testable.
 *
 * Requirements satisfied: 1.1, 1.2, 1.3, 1.4, 1.5
 */
class HelloWorld
{
    /**
     * Returns the greeting message.
     *
     * @return string Always "Hello World"
     */
    public function getMessage(): string
    {
        return 'Hello World';
    }

    /**
     * Returns the application name.
     *
     * @return string Always "CircleCI Demo"
     */
    public function getAppName(): string
    {
        return 'CircleCI Demo';
    }

    /**
     * Outputs a formatted greeting to standard output.
     *
     * Output format (three lines):
     *   === CircleCI Demo ===
     *   Hello World
     *   PHP Version: <version>
     */
    public function run(): void
    {
        echo "=== {$this->getAppName()} ===" . PHP_EOL;
        echo $this->getMessage() . PHP_EOL;
        echo 'PHP Version: ' . PHP_VERSION . PHP_EOL;
    }
}
