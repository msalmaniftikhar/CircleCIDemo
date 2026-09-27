<?php

declare(strict_types=1);

use App\HelloWorld;
use PHPUnit\Framework\TestCase;

class HelloWorldTest extends TestCase
{
    private HelloWorld $hw;

    protected function setUp(): void
    {
        $this->hw = new HelloWorld();
    }

    public function testGetMessage(): void
    {
        $this->assertSame('Hello World', $this->hw->getMessage());
    }

    public function testGetAppName(): void
    {
        $this->assertSame('CircleCI Demo', $this->hw->getAppName());
    }

    public function testRunOutput(): void
    {
        ob_start();
        $this->hw->run();
        $output = ob_get_clean();

        $this->assertStringContainsString('=== CircleCI Demo ===', $output);
        $this->assertStringContainsString('Hello World', $output);
        $this->assertStringContainsString('PHP Version:', $output);
    }
}
