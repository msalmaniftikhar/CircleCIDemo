<?php

declare(strict_types=1);

use App\HelloWorld;
use PHPUnit\Framework\TestCase;

/**
 * Phase 8 — AC/Test Verification
 *
 * Verifies acceptance criteria for the HelloWorld class.
 * Each test maps directly to a numbered acceptance criterion.
 *
 * Requirements verified: 1.2, 1.3, 1.4, 1.5, 4.1, 4.2, 4.3, 4.4, 4.5
 */
class HelloWorldTest extends TestCase
{
    private HelloWorld $helloWorld;

    protected function setUp(): void
    {
        $this->helloWorld = new HelloWorld();
    }

    /**
     * AC 1.2 / Req 4.2: getMessage() returns "Hello World".
     *
     * @covers App\HelloWorld::getMessage
     */
    public function testGetMessage(): void
    {
        $this->assertEquals('Hello World', $this->helloWorld->getMessage());
    }

    /**
     * AC 1.3 / Req 4.3: getAppName() returns "CircleCI Demo".
     *
     * @covers App\HelloWorld::getAppName
     */
    public function testGetAppName(): void
    {
        $this->assertEquals('CircleCI Demo', $this->helloWorld->getAppName());
    }

    /**
     * AC 1.4, 1.5 / Req 4.4: run() produces all required output substrings.
     *
     * **Property 1: run() output contains all required substrings**
     *
     * For any HelloWorld instance, run() MUST always emit output that contains:
     *   - "=== CircleCI Demo ===" (app name banner)
     *   - "Hello World" (greeting message)
     *   - a substring beginning with "PHP Version:" (runtime info)
     *
     * This invariant holds regardless of the PHP runtime version, because the
     * "PHP Version:" prefix is always present even though the version number varies.
     *
     * **Validates: Requirements 1.4, 1.5**
     *
     * @covers App\HelloWorld::run
     */
    public function testRun(): void
    {
        ob_start();
        $this->helloWorld->run();
        $output = ob_get_clean();

        $this->assertStringContainsString(
            'Hello World',
            $output,
            'run() must contain the greeting message'
        );
        $this->assertStringContainsString(
            '=== CircleCI Demo ===',
            $output,
            'run() must contain the app name banner'
        );
        $this->assertStringContainsString(
            'PHP Version:',
            $output,
            'run() must contain the PHP version prefix'
        );
    }
}
