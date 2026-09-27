# Requirements

## Introduction

This project is a PHP Hello World demo that shows how Kiro spec-driven development works end-to-end with CircleCI. The goal is a clean, testable PHP application that automatically builds and tests itself on every push — no manual steps required.

---

## Requirement 1 — The Hello World message

**As a developer, I want the app to print a clear Hello World greeting, so I can confirm it's running correctly.**

1. Running the app prints `=== CircleCI Demo ===` on the first line.
2. Running the app prints `Hello World` on the second line.
3. Running the app prints the current PHP version on the third line.
4. The greeting logic lives in a `HelloWorld` class, not in `index.php`, so it can be tested independently.
5. `getMessage()` returns the string `Hello World`.
6. `getAppName()` returns the string `CircleCI Demo`.

---

## Requirement 2 — The entry point stays simple

**As a developer, I want `index.php` to only start the app, so the file stays easy to read and maintain.**

1. `index.php` loads the Composer autoloader and calls `HelloWorld::run()`.
2. All output comes from `HelloWorld::run()` — `index.php` itself prints nothing.

---

## Requirement 3 — Dependencies are managed with Composer

**As a developer, I want Composer to handle dependencies, so I can install everything with one command.**

1. `composer install` installs all dependencies without errors.
2. PHPUnit is available as a dev dependency after install.
3. The `HelloWorld` class is autoloaded automatically — no manual `require` statements needed.

---

## Requirement 4 — The app has automated tests

**As a developer, I want automated tests for the HelloWorld class, so I know immediately if something breaks.**

1. Running `vendor/bin/phpunit tests/unit/` passes with zero failures.
2. `getMessage()` is tested to return `Hello World`.
3. `getAppName()` is tested to return `CircleCI Demo`.
4. `run()` is tested to produce all three required output lines.
5. Running `vendor/bin/phpunit tests/convergence/` passes with zero failures.
6. The convergence tests confirm that `run()` always produces the same output, no matter how many times or how it's called.

---

## Requirement 5 — Every push is automatically validated by CircleCI

**As a developer, I want CircleCI to check my code on every push, so I never accidentally break the build.**

1. Pushing to any branch triggers a CircleCI build automatically.
2. The build uses PHP 8.2 (`cimg/php:8.2`).
3. The build installs dependencies, checks PHP syntax, runs the app, runs all tests, and runs the human gate — in that order.
4. If any step fails, the build stops and reports the failure.
5. A passing build means the code is safe to ship.

---

## Requirement 6 — The README explains the project clearly

**As a developer or reviewer, I want a README that tells me exactly what this project does and how to run it, so I don't have to dig through the code.**

1. The README shows a live CircleCI build status badge.
2. The README explains the 11-phase pipeline in a table.
3. The README includes step-by-step local setup instructions.
4. The README does not mention Travis CI.
