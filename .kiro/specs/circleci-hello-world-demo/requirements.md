# Requirements Document

## Introduction

This feature covers the end-to-end delivery of a PHP Hello World application with a fully integrated CircleCI 2.1 continuous integration pipeline. The existing flat `index.php` script is refactored into a testable `HelloWorld` PHP class, a PHPUnit test suite is added, and the `.circleci/config.yml` is rewritten to run a single CI job that installs dependencies, validates PHP syntax, lints the codebase, executes the application, and runs the PHPUnit test suite. The feature is intended as a Kiro spec-driven demonstration of both the PHP application structure and the CI pipeline configuration.

## Glossary

- **HelloWorld**: The PHP class defined in `src/HelloWorld.php` that encapsulates the greeting and output logic.
- **Application**: The PHP project consisting of `src/HelloWorld.php`, `index.php`, `tests/HelloWorldTest.php`, and `composer.json`.
- **CI Pipeline**: The CircleCI 2.1 workflow defined in `.circleci/config.yml` that automates validation and testing of the Application.
- **Test Suite**: The PHPUnit test file `tests/HelloWorldTest.php` that exercises the HelloWorld class.
- **Composer**: The PHP dependency manager used to install PHPUnit and manage autoloading.
- **Executor**: The CircleCI Docker image (`cimg/php`) that provides the PHP runtime environment for the CI Pipeline.
- **build Job**: The single CircleCI job named `build` that contains all CI Pipeline steps.
- **Workflow**: The CircleCI `workflows` block that schedules and triggers the build Job.

---

## Requirements

### Requirement 1 — HelloWorld Class

**User Story:** As a developer, I want a `HelloWorld` PHP class with a testable `getMessage()` method, so that the application logic can be exercised by an automated test suite.

#### Acceptance Criteria

1. THE Application SHALL provide a `HelloWorld` class in `src/HelloWorld.php` under the `App` namespace.
2. THE `HelloWorld` class SHALL expose a public method `getMessage()` that returns the string `"Hello World"`.
3. THE `HelloWorld` class SHALL expose a public method `getAppName()` that returns the string `"CircleCI Demo"`.
4. THE `HelloWorld` class SHALL expose a public method `run()` that outputs a formatted greeting string containing the app name, the message, and the current PHP version to standard output.
5. WHEN `run()` is called, THE `HelloWorld` class SHALL output lines that include `"=== CircleCI Demo ==="`, `"Hello World"`, and a line beginning with `"PHP Version:"`.

---

### Requirement 2 — Application Entry Point

**User Story:** As a developer, I want `index.php` to delegate all output logic to the `HelloWorld` class, so that the entry point remains thin and testable logic is contained in the class.

#### Acceptance Criteria

1. THE Application SHALL provide an `index.php` at the project root that autoloads dependencies via Composer and instantiates `HelloWorld`.
2. WHEN `index.php` is executed, THE Application SHALL call `HelloWorld::run()` to produce all output.
3. THE Application SHALL produce no output from `index.php` other than that delegated to `HelloWorld::run()`.

---

### Requirement 3 — Composer Configuration

**User Story:** As a developer, I want a `composer.json` that declares PHPUnit as a dev dependency and configures PSR-4 autoloading for the `App` namespace, so that dependencies can be installed deterministically and classes resolved automatically.

#### Acceptance Criteria

1. THE Application SHALL provide a `composer.json` at the project root declaring `phpunit/phpunit` as a `require-dev` dependency pinned to a specific version range (e.g., `^10.0`).
2. THE Application SHALL configure PSR-4 autoloading in `composer.json` mapping the `App\\` namespace to the `src/` directory.
3. WHEN `composer install` is run, THE Application SHALL produce a `vendor/` directory containing PHPUnit and the Composer autoloader.

---

### Requirement 4 — PHPUnit Test Suite

**User Story:** As a developer, I want a PHPUnit test class for `HelloWorld`, so that the CI Pipeline can verify the application logic automatically on every commit.

#### Acceptance Criteria

1. THE Application SHALL provide a test file at `tests/HelloWorldTest.php` that extends `PHPUnit\Framework\TestCase`.
2. THE Test Suite SHALL contain a test method `testGetMessage()` that asserts `HelloWorld::getMessage()` returns `"Hello World"`.
3. THE Test Suite SHALL contain a test method `testGetAppName()` that asserts `HelloWorld::getAppName()` returns `"CircleCI Demo"`.
4. THE Test Suite SHALL contain a test method `testRun()` that captures output buffering and asserts the output of `HelloWorld::run()` contains `"Hello World"`.
5. WHEN `vendor/bin/phpunit` is executed from the project root, THE Test Suite SHALL report all tests as passed with zero failures and zero errors.

---

### Requirement 5 — CircleCI Pipeline Configuration

**User Story:** As a developer, I want a complete CircleCI 2.1 `config.yml` with a single `build` job covering install, lint, run, and test steps, so that every push is automatically validated.

#### Acceptance Criteria

1. THE CI Pipeline SHALL use CircleCI configuration version `2.1`.
2. THE CI Pipeline SHALL define an Executor using the `cimg/php:8.2` Docker image.
3. THE CI Pipeline SHALL define a single job named `build` that uses the defined Executor.
4. WHEN the build Job is triggered, THE CI Pipeline SHALL check out the source code using the `checkout` step.
5. WHEN the build Job is triggered, THE CI Pipeline SHALL install Composer dependencies by running `composer install --no-interaction --prefer-dist`.
6. WHEN the build Job is triggered, THE CI Pipeline SHALL validate PHP syntax on all `.php` files under `src/` and `tests/` by running `find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;`.
7. WHEN the build Job is triggered, THE CI Pipeline SHALL execute the application by running `php index.php`.
8. WHEN the build Job is triggered, THE CI Pipeline SHALL execute the Test Suite by running `vendor/bin/phpunit --testdox`.
9. THE CI Pipeline SHALL define a Workflow named `main` that triggers the build Job on every push to any branch.
10. IF any step in the build Job exits with a non-zero status, THEN THE CI Pipeline SHALL mark the build Job as failed and halt subsequent steps.

---

### Requirement 6 — README Update

**User Story:** As a developer, I want an updated `README.md` that references CircleCI instead of Travis CI and documents how to run the application and tests locally, so that the repository accurately reflects the current CI setup.

#### Acceptance Criteria

1. THE Application SHALL provide a `README.md` that includes a CircleCI build status badge linking to the project's CircleCI pipeline.
2. THE `README.md` SHALL include instructions for running `composer install`, `php index.php`, and `vendor/bin/phpunit` locally.
3. THE `README.md` SHALL not reference Travis CI.
