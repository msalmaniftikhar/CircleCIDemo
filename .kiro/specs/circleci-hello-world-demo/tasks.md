# Implementation Plan: circleci-hello-world-demo

## Overview

This plan maps every coding task to one of the 11 pipeline phases that the project folder structure is designed to demonstrate. Phases 1–4 are already complete (requirements, user stories, acceptance criteria, and design docs exist). Tasks below cover Phases 5–11 only.

```
Phase 1  Requirement  →  .kiro/specs/requirements.md        ✅ done
Phase 2  Story        →  user stories in requirements.md    ✅ done
Phase 3  AC           →  acceptance criteria in req.md      ✅ done
Phase 4  Spec Kit     →  .kiro/specs/design.md              ✅ done
Phase 5  Context      →  .kiro/steering/project-context.md
Phase 6  Agent Harness→  .circleci/config.yml
Phase 7  Implementation→  src/, composer.json, index.php
Phase 8  AC/Test Verify→  tests/unit/, .kiro/hooks/
Phase 9  Convergence  →  tests/convergence/
Phase 10 Human Gate   →  scripts/human-gate.sh
Phase 11 Production   →  README.md, docs/pipeline-stages.md
```

---

## Tasks

- [x] 1. Phase 5 — Context/Skills: create steering file
  - [x] 1.1 Create `.kiro/steering/project-context.md`
    - Write a steering document that describes the project purpose, the 11-phase pipeline, folder layout, coding conventions (`declare(strict_types=1)`, PSR-4, PHPUnit 10), and the requirement IDs in scope
    - Include a section listing each phase, the files it owns, and the requirement IDs it satisfies
    - This file is read by the agent at task-execution time to ground all subsequent implementation decisions
    - _Requirements: none (meta-context file)_

- [x] 2. Phase 6 — Agent Harness: rewrite CircleCI pipeline
  - [x] 2.1 Rewrite `.circleci/config.yml` for CircleCI 2.1
    - Set `version: 2.1` at the top of the file
    - Define a `php-executor` executor using `docker: cimg/php:8.2`
    - Define a single `build` job using `php-executor` with steps in this order, each with a descriptive `name` field:
      1. `checkout`
      2. `composer install --no-interaction --prefer-dist`
      3. `find . -name "*.php" -not -path "./vendor/*" -exec php -l {} \;`
      4. `php index.php`
      5. `vendor/bin/phpunit --testdox tests/unit/`
      6. `vendor/bin/phpunit --testdox tests/convergence/`
      7. `bash scripts/human-gate.sh`
    - Define a `main` workflow that triggers `build` on every push to any branch with no branch filter
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 5.7, 5.8, 5.9, 5.10_

- [x] 3. Phase 7 — Implementation: Composer config, HelloWorld class, entry point
  - [x] 3.1 Create `composer.json` at the project root
    - Set package name `demo/circleci-hello-world`, type `project`
    - Declare `php: >=8.2` under `require`
    - Declare `phpunit/phpunit: ^10.0` under `require-dev`
    - Configure PSR-4 autoloading: `"App\\": "src/"` under `autoload`
    - Configure PSR-4 autoloading: `"Tests\\": "tests/"` under `autoload-dev`
    - _Requirements: 3.1, 3.2, 3.3_

  - [x] 3.2 Create `src/HelloWorld.php` with the `App\HelloWorld` class
    - Add `declare(strict_types=1)` and `namespace App;`
    - Implement `getMessage(): string` returning `'Hello World'`
    - Implement `getAppName(): string` returning `'CircleCI Demo'`
    - Implement `run(): void` that echoes `"=== CircleCI Demo ==="`, `"Hello World"`, and `"PHP Version: " . PHP_VERSION` each on its own line
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

  - [x] 3.3 Refactor `index.php` to be a thin entry point
    - Replace existing content with `require_once __DIR__ . '/vendor/autoload.php';`
    - Instantiate `App\HelloWorld` and call `->run()`
    - Ensure no output logic remains in `index.php` itself
    - _Requirements: 2.1, 2.2, 2.3_

- [x] 4. Phase 8 — AC/Test Verification: unit tests and hook
  - [x] 4.1 Create `tests/unit/HelloWorldTest.php` extending `PHPUnit\Framework\TestCase`
    - Add `declare(strict_types=1)` and import `App\HelloWorld`
    - Add `setUp()` creating a fresh `HelloWorld` instance in `$this->helloWorld`
    - Implement `testGetMessage()`: assert `getMessage()` equals `'Hello World'`
    - Implement `testGetAppName()`: assert `getAppName()` equals `'CircleCI Demo'`
    - Implement `testRun()`: capture output with `ob_start()`/`ob_get_clean()` and assert it contains `'Hello World'`, `'=== CircleCI Demo ==='`, and `'PHP Version:'`
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [x] 4.2 Write property test for `run()` output structure inside `testRun()` (Property 1)
    - **Property 1: run() output contains all required substrings**
    - Assert that for any `HelloWorld` instance the output of `run()` always contains all three required strings: `"=== CircleCI Demo ==="`, `"Hello World"`, and a substring beginning with `"PHP Version:"`
    - **Validates: Requirements 1.4, 1.5**

  - [x] 4.3 Create `.kiro/hooks/ac-verification.json`
    - Write a Kiro v1 hook with trigger `PostFileSave`, matcher `\\.php$`
    - Action type `command`: run `vendor/bin/phpunit --testdox tests/unit/ 2>&1` so that saving any `.php` file automatically re-runs the unit test suite and surfaces failures immediately
    - _Requirements: 4.5_

- [x] 5. Checkpoint — Ensure all unit tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Phase 9 — Convergence: property-invariant test suite
  - [x] 6.1 Create `tests/convergence/ConvergenceTest.php`
    - Add `declare(strict_types=1)` and import `App\HelloWorld`
    - Implement `testRunOutputContainsAllRequiredSubstrings()`: Property 1 — assert all three required substrings are present in `run()` output for a fresh instance
    - Implement `testIndexPhpOutputMatchesRunOutput()`: Property 2 — capture `(new HelloWorld())->run()` output via output buffering, then assert it is identical to what `index.php` would produce, verifying the entry point adds no extra output (mock or re-invoke `run()` in isolation; do NOT shell-exec `php index.php` inside a unit test)
    - Annotate each test with a docblock referencing the property number and requirement IDs
    - _Requirements: 1.4, 1.5, 2.2, 2.3_

  - [x] 6.2 Write property test for entry point delegation (Property 2)
    - **Property 2: index.php output equals HelloWorld::run() output**
    - **Validates: Requirements 2.2, 2.3**

- [x] 7. Phase 10 — Human Gate: pre-push check script
  - [x] 7.1 Create `scripts/human-gate.sh`
    - Make the file executable (`chmod +x` via the shebang line; note: the file itself must have `#!/usr/bin/env bash` as the first line and the CI step runs it with `bash scripts/human-gate.sh` so no separate chmod is needed in CI)
    - Run `vendor/bin/phpunit --testdox tests/unit/ tests/convergence/` and capture exit code
    - If exit code is non-zero, print `"[HUMAN GATE] Tests failed — pipeline blocked."` to stderr and exit 1
    - If exit code is zero, print `"[HUMAN GATE] All checks passed — ready for production."` to stdout and exit 0
    - This script acts as the final local and CI gate before the Production phase
    - _Requirements: 5.8 (maps to the final pipeline step that must pass)_

- [x] 8. Phase 11 — Production: README and narrative doc
  - [x] 8.1 Update `README.md` with CircleCI badge and local instructions
    - Add a CircleCI build status badge at the top using the format: `[![CircleCI](https://dl.circleci.com/status-badge/img/gh/<org>/<repo>/tree/main.svg?style=svg)](https://dl.circleci.com/status-badge/redirect/gh/<org>/<repo>/tree/main)`
    - Add a project description identifying this as a Kiro spec-driven CircleCI demo illustrating the 11-phase pipeline
    - Add a Prerequisites section listing PHP ≥ 8.2 and Composer
    - Add local setup instructions: `composer install`
    - Add run instructions: `php index.php`
    - Add test instructions: `vendor/bin/phpunit tests/unit/` and `vendor/bin/phpunit tests/convergence/`
    - Add human gate instructions: `bash scripts/human-gate.sh`
    - Remove any Travis CI references
    - _Requirements: 6.1, 6.2, 6.3_

  - [x] 8.2 Create `docs/pipeline-stages.md`
    - Write a narrative document explaining all 11 pipeline phases
    - For each phase include: phase number, phase name, folder/file it owns, a one-paragraph description of what happens in that phase, and the requirement IDs it satisfies (if any)
    - Phases 1–4 should be described as already-complete artifact phases; Phases 5–11 as active code phases
    - Include a Mermaid flowchart at the top showing the linear phase progression
    - _Requirements: none (documentation artifact)_

- [x] 9. Final checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP
- The `vendor/` directory is Composer-managed and must be listed in `.gitignore`
- The CircleCI badge placeholder `<org>/<repo>` must be replaced with the actual GitHub organization and repository name before the first push
- Property 1 and Property 2 tests appear in both `tests/unit/HelloWorldTest.php` (as inline assertions) and `tests/convergence/ConvergenceTest.php` (as dedicated convergence tests) — this is intentional: the convergence suite exists specifically to demonstrate Phase 9 as a distinct pipeline stage
- The `.kiro/hooks/ac-verification.json` hook fires on every PHP file save during local development, giving immediate AC feedback without running the full pipeline

---

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1", "3.1"] },
    { "id": 1, "tasks": ["2.1", "3.2"] },
    { "id": 2, "tasks": ["3.3"] },
    { "id": 3, "tasks": ["4.1", "4.3"] },
    { "id": 4, "tasks": ["4.2", "6.1"] },
    { "id": 5, "tasks": ["6.2", "7.1"] },
    { "id": 6, "tasks": ["8.1", "8.2"] }
  ]
}
```
