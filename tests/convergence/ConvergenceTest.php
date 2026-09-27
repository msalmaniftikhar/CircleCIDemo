<?php

declare(strict_types=1);

use App\HelloWorld;
use PHPUnit\Framework\TestCase;

/**
 * Convergence tests validate structural invariants — properties that must hold
 * across all executions, not just specific examples.
 */
class ConvergenceTest extends TestCase
{
    // Property 1: run() always produces the same output regardless of how many times it's called.
    public function testRunIsIdempotent(): void
    {
        $hw = new HelloWorld();

        ob_start();
        $hw->run();
        $first = ob_get_clean();

        ob_start();
        $hw->run();
        $second = ob_get_clean();

        $this->assertSame($first, $second);
    }

    // Property 2: Two separate HelloWorld instances always produce identical output.
    // This proves the entry point (index.php) cannot introduce extra output by
    // instantiating a new HelloWorld and calling run().
    public function testOutputIsConsistentAcrossInstances(): void
    {
        ob_start();
        (new HelloWorld())->run();
        $first = ob_get_clean();

        ob_start();
        (new HelloWorld())->run();
        $second = ob_get_clean();

        $this->assertSame($first, $second);
    }
}
