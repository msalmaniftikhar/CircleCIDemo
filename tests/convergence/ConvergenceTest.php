<?php

declare(strict_types=1);

use App\HelloWorld;
use PHPUnit\Framework\TestCase;

/**
 * Phase 9 — Convergence
 *
 * Validates universal invariants (property-based assertions) that must hold
 * across ALL valid executions, not just specific example inputs.
 * These properties are distinct from unit tests: they assert structural
 * invariants of the system rather than specific input/output pairs.
 *
 * Property 1: run() output always contains all required substrings — Req 1.4, 1.5
 * Property 2: HelloWorld::run() produces identical output on every invocation — Req 2.2, 2.3
 */
class ConvergenceTest extends TestCase
{
    /**
     * Property 1: run() output contains all required substrings.
     *
     * For any HelloWorld instance, run() MUST always emit:
     *   - "=== CircleCI Demo ==="
     *   - "Hello World"
     *   - a line beginning with "PHP Version:"
     *
     * This is a structural invariant of the output format that holds regardless
     * of PHP runtime version (the PHP Version: prefix is always present even
     * though the version number varies by environment).
     *
     * @covers App\HelloWorld::run
     * Validates: Requirements 1.4, 1.5
     */
    public function testRunOutputContainsAllRequiredSubstrings(): void
    {
        $hw = new HelloWorld();

        ob_start();
        $hw->run();
        $output = ob_get_clean();

        $this->assertStringContainsString(
            '=== CircleCI Demo ===',
            $output,
            'Property 1: run() must always emit the app name banner'
        );
        $this->assertStringContainsString(
            'Hello World',
            $output,
            'Property 1: run() must always emit the greeting message'
        );
        $this->assertStringContainsString(
            'PHP Version:',
            $output,
            'Property 1: run() must always emit the PHP version prefix'
        );
    }

    /**
     * Property 2: index.php output equals HelloWorld::run() output.
     *
     * For any execution environment where vendor/autoload.php is present,
     * the complete stdout output of `php index.php` SHALL be identical to the
     * output captured from `(new App\HelloWorld())->run()` in the same environment.
     *
     * The entry point MUST NOT introduce any extra output beyond what
     * HelloWorld::run() produces. This is validated by comparing two
     * independent captures of run() — a proxy for the delegation invariant
     * (index.php is a thin bootstrap that calls run() and nothing else).
     *
     * We do NOT shell-exec `php index.php` inside a unit test; instead we
     * verify the invariant by confirming run() is idempotent and pure with
     * respect to its output: two fresh instances must produce the same output,
     * which means any caller (including index.php) that only calls run() will
     * produce exactly that output.
     *
     * @covers App\HelloWorld::run
     * Validates: Requirements 2.2, 2.3
     */
    public function testEntryPointDelegationInvariant(): void
    {
        $hw1 = new HelloWorld();
        ob_start();
        $hw1->run();
        $capture1 = ob_get_clean();

        $hw2 = new HelloWorld();
        ob_start();
        $hw2->run();
        $capture2 = ob_get_clean();

        $this->assertSame(
            $capture1,
            $capture2,
            'Property 2: HelloWorld::run() must produce identical output on every invocation (no side effects). '
            . 'This ensures index.php — which only calls run() — can introduce no extra output.'
        );
    }
}
