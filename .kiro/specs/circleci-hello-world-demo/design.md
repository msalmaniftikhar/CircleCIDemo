# Design Document

## Feature: circleci-hello-world-demo

---

## Overview

This document describes the architecture and implementation design for the CircleCI Hello World Demo — a PHP application that demonstrates Kiro spec-driven development with a full CircleCI 2.1 continuous integration pipeline.

The existing flat `index.php` script is refactored into a testable `HelloWorld` class (`src/HelloWorld.php`) under the `App` namespace. A PHPUnit test suite validates the class logic, Composer manages dependencies and PSR-4 autoloading, and a single CircleCI `build` job orchestrates checkout, install, lint, execution, and testing on every push.

---

## Architecture

The project follows a minimal, flat PHP structure with no framework dependencies:

```
CircleCIDemo/
├── .circleci/
│   └── config.yml          # CircleCI 2.1 pipeline configuration
├── src/
│   └── HelloWorld.php      # HelloWorld class (App namespace)
├── tests/
│   └── HelloWorldTest.php  # PHPUnit test suite
├── vendor/                 # Composer-managed dependencies (gitignored)
├── composer.json           # Dependency manifest + PSR-4 autoloading
├── composer.lock           # Locked dependency versions
├── index.php               # Thin entry point — delegates to HelloWorld::run()
└── README.md               # Project documentation with CircleCI badge
```

### Component Boundaries

```
┌─────────────────────────────────────────────────────────────┐
│                        index.php                            │
│   require vendor/autoload.php                               │
│   (new App\HelloWorld())->run()                             │
└───────────────────────────┬─────────────────────────────────┘
                            │ delegates all output
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                   App\HelloWorld                            │
│                   src/HelloWorld.php                        │
│                                                             │
│   getMessage() → "Hello World"                              │
│   getAppName() → "CircleCI Demo"                            │
│   run()        → echo formatted greeting to stdout          │
└─────────────────────────────────────────────────────────────┘
                            │ tested by
                            ▼
┌─────────────────────────────────────────────────────────────┐
│               tests/HelloWorldTest.php                      │
│               extends PHPUnit\Framework\TestCase            │
│                                                             │
│   testGetMessage()  → assertEquals("Hello World", ...)      │
│   testGetAppName()  → assertEquals("CircleCI Demo", ...)    │
│   testRun()         → assertStringContainsString(...)        │
└─────────────────────────────────────────────────────────────┘
```

---

## Components

### 1. `src/HelloWorld.php` — HelloWorld Class

**Namespace:** `App`

**Responsibility:** Encapsulates all greeting logic and formatted output.

```php
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
```

**Design notes:**
- `getMessage()` and `getAppName()` are pure methods — no side effects, no external dependencies.
- `run()` composes the two getters; this keeps individual accessors independently testable.
- `declare(strict_types=1)` enforces type safety across all method signatures.

---

### 2. `index.php` — Application Entry Point

**Responsibility:** Bootstrap Composer autoloading and delegate execution to `HelloWorld::run()`.

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\HelloWorld;

$app = new HelloWorld();
$app->run();
```

**Design notes:**
- No business logic lives in `index.php`; it is strictly a bootstrap file.
- All output is produced exclusively via `HelloWorld::run()`.

---

### 3. `composer.json` — Dependency and Autoloading Configuration

**Responsibility:** Declare PHPUnit as a dev dependency and configure PSR-4 autoloading.

```json
{
    "name": "demo/circleci-hello-world",
    "description": "CircleCI Hello World Demo",
    "type": "project",
    "require": {
        "php": ">=8.2"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    }
}
```

**Design notes:**
- `phpunit/phpunit: ^10.0` is pinned to a major version range, ensuring compatibility with PHP 8.2 while allowing patch updates.
- PSR-4 autoloading maps `App\` → `src/`, so `App\HelloWorld` resolves to `src/HelloWorld.php` automatically.

---

### 4. `tests/HelloWorldTest.php` — PHPUnit Test Suite

**Responsibility:** Verify the three public methods of `HelloWorld` behave as specified.

```php
<?php

declare(strict_types=1);

use App\HelloWorld;
use PHPUnit\Framework\TestCase;

class HelloWorldTest extends TestCase
{
    private HelloWorld $helloWorld;

    protected function setUp(): void
    {
        $this->helloWorld = new HelloWorld();
    }

    public function testGetMessage(): void
    {
        $this->assertEquals('Hello World', $this->helloWorld->getMessage());
    }

    public function testGetAppName(): void
    {
        $this->assertEquals('CircleCI Demo', $this->helloWorld->getAppName());
    }

    public function testRun(): void
    {
        ob_start();
        $this->helloWorld->run();
        $output = ob_get_clean();

        $this->assertStringContainsString('Hello World', $output);
        $this->assertStringContainsString('=== CircleCI Demo ===', $output);
        $this->assertStringContainsString('PHP Version:', $output);
    }
}
```

**Design notes:**
- `setUp()` creates a fresh `HelloWorld` instance before each test, avoiding shared state.
- `testRun()` uses output buffering (`ob_start` / `ob_get_clean`) to capture stdout without polluting test output.
- Three assertions in `testRun()` directly map to the three required output lines from Requirement 1.5.

---

### 5. `.circleci/config.yml` — CircleCI Pipeline

**Responsibility:** Define the CI/CD pipeline that validates the project on every push.

```yaml
version: 2.1

executors:
  php-executor:
    docker:
      - image: cimg/php:8.2

jobs:
  build:
    executor: php-executor
    steps:
      - checkout

      - run:
          name: Install Composer dependencies
          command: composer install --no-interaction --prefer-dist

      - run:
          name: Validate PHP syntax
          command: find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;

      - run:
          name: Run application
          command: php index.php

      - run:
          name: Run PHPUnit test suite
          command: vendor/bin/phpunit --testdox

workflows:
  main:
    jobs:
      - build
```

**Pipeline step ordering and rationale:**

| Step | Command | Purpose |
|------|---------|---------|
| 1. Checkout | `checkout` | Retrieve source code from VCS |
| 2. Install | `composer install --no-interaction --prefer-dist` | Install PHPUnit and generate autoloader |
| 3. Lint | `find ... -exec php -l {} \;` | Fast syntax check — catches parse errors before running |
| 4. Run | `php index.php` | Smoke-test the entry point executes cleanly |
| 5. Test | `vendor/bin/phpunit --testdox` | Execute full test suite with readable output |

**Design notes:**
- Lint runs before the application and tests so syntax errors surface immediately with clear output.
- The `main` workflow has no branch filter, so every push to any branch triggers the build job.
- A non-zero exit from any step causes CircleCI to fail the job and halt subsequent steps (default behavior).

---

### 6. `README.md` — Project Documentation

**Responsibility:** Document the project with a CircleCI badge and local usage instructions.

**Structure:**
- CircleCI build badge (links to the project pipeline)
- Project description
- Prerequisites (PHP ≥ 8.2, Composer)
- Local setup: `composer install`
- Run locally: `php index.php`
- Run tests: `vendor/bin/phpunit`
- No Travis CI references

---

## Data Models

This project contains no persistent data models. The only data flows are:

- **String constants** returned by `getMessage()` and `getAppName()` — hardcoded, no serialization needed.
- **stdout output** produced by `run()` — formatted string assembled from getters and `PHP_VERSION`.

---

## Error Handling

| Scenario | Handling |
|----------|----------|
| Missing `vendor/autoload.php` | PHP fatal error with path info; user must run `composer install` |
| PHP syntax error in source | Caught by lint step in CI; `php -l` exits non-zero and halts pipeline |
| PHPUnit test failure | `vendor/bin/phpunit` exits non-zero; CI job marked failed |
| PHP version < 8.2 | `composer install` fails on platform requirement check |

No exceptions are thrown by `HelloWorld` methods — all three are pure output operations with no failure modes.

---

## Interfaces

### Public API of `App\HelloWorld`

| Method | Signature | Returns | Side Effects |
|--------|-----------|---------|--------------|
| `getMessage` | `getMessage(): string` | `"Hello World"` | None |
| `getAppName` | `getAppName(): string` | `"CircleCI Demo"` | None |
| `run` | `run(): void` | — | Writes to stdout |

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

**Property Reflection:**
Most acceptance criteria in this project are deterministic (zero-argument methods, fixed return values) and are best validated with example-based unit tests. Two meaningful universal properties emerge:

1. The output of `run()` must always contain all three required substrings — this is a structural invariant of the output format that holds regardless of PHP runtime version (the `PHP Version:` prefix is always present even though the version number varies).
2. The output of `index.php` must always equal the output of `HelloWorld::run()` — this is a delegation invariant (no extra output may be introduced by the entry point).

These two properties are distinct and non-redundant: Property 1 validates the class in isolation; Property 2 validates the wiring between entry point and class.

---

### Property 1: run() output contains all required substrings

*For any* instance of `HelloWorld`, calling `run()` SHALL produce output that contains all three required strings: `"=== CircleCI Demo ==="`, `"Hello World"`, and a substring beginning with `"PHP Version:"`.

**Validates: Requirements 1.4, 1.5**

---

### Property 2: index.php output equals HelloWorld::run() output

*For any* execution environment where `vendor/autoload.php` is present, the complete stdout output of `php index.php` SHALL be identical to the output captured from `(new App\HelloWorld())->run()` in the same environment.

**Validates: Requirements 2.2, 2.3**
