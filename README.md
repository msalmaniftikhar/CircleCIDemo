# CircleCI Hello World — Kiro Spec-Driven Demo

[![CircleCI](https://dl.circleci.com/status-badge/img/gh/msalmaniftikhar/CircleCIDemo/tree/main.svg?style=svg)](https://dl.circleci.com/status-badge/redirect/gh/msalmaniftikhar/CircleCIDemo/tree/main)

> **Note:** Replace `<org>` in the badge URLs above with your GitHub organization or username before the first push.

---

## Description

This project demonstrates the full **11-phase Kiro spec-driven development pipeline** using a PHP Hello World application and CircleCI for continuous integration. Every file in this repository is a traceable artifact of a specific pipeline phase — from requirements through production.

The application is intentionally minimal (a single `HelloWorld` PHP class) so all complexity lives in the pipeline and process, not in business logic.

---

## The 11-Phase Pipeline

| Phase | Name | Artifact |
|-------|------|----------|
| 1 | Requirement | `.kiro/specs/circleci-hello-world-demo/requirements.md` |
| 2 | Story | User stories in `requirements.md` |
| 3 | AC | Acceptance criteria in `requirements.md` |
| 4 | Spec Kit | `.kiro/specs/circleci-hello-world-demo/design.md` |
| 5 | Context/Skills | `.kiro/steering/project-context.md` |
| 6 | Agent Harness | `.circleci/config.yml` |
| 7 | Implementation | `src/HelloWorld.php`, `composer.json`, `index.php` |
| 8 | AC/Test Verification | `tests/unit/HelloWorldTest.php`, `.kiro/hooks/ac-verification.json` |
| 9 | Convergence | `tests/convergence/ConvergenceTest.php` |
| 10 | Human Gate | `scripts/human-gate.sh` |
| 11 | Production | `README.md`, `docs/pipeline-stages.md` |

For a full narrative of each phase, see [`docs/pipeline-stages.md`](docs/pipeline-stages.md).

---

## Prerequisites

- **PHP ≥ 8.2** — [php.net/downloads](https://www.php.net/downloads)
- **Composer** — [getcomposer.org](https://getcomposer.org/)

---

## Local Setup

### 1. Install dependencies

```bash
composer install
```

This installs PHPUnit and generates the Composer autoloader in `vendor/`.

### 2. Run the application

```bash
php index.php
```

Expected output:
```
=== CircleCI Demo ===
Hello World
PHP Version: 8.2.x
```

### 3. Run unit tests (Phase 8 — AC Verification)

```bash
vendor/bin/phpunit tests/unit/
```

### 4. Run convergence tests (Phase 9 — Invariant Verification)

```bash
vendor/bin/phpunit tests/convergence/
```

### 5. Run all tests with testdox output

```bash
vendor/bin/phpunit --testdox tests/unit/ tests/convergence/
```

### 6. Human gate check (Phase 10 — Pre-Production)

```bash
bash scripts/human-gate.sh
```

Runs the full suite and exits 0 if everything passes, 1 if anything fails.

---

## Project Structure

```
CircleCIDemo/
├── .circleci/config.yml              # CircleCI 2.1 pipeline
├── .kiro/
│   ├── hooks/ac-verification.json   # Auto-run tests on PHP file save
│   ├── specs/                        # Kiro spec artifacts (Phases 1–4)
│   └── steering/project-context.md  # Agent context (Phase 5)
├── docs/pipeline-stages.md           # Phase narrative documentation
├── scripts/human-gate.sh             # Pre-production gate
├── src/HelloWorld.php                # Application class
├── tests/
│   ├── convergence/ConvergenceTest.php
│   └── unit/HelloWorldTest.php
├── composer.json
└── index.php
```

---

## CI/CD

Every push to any branch triggers the CircleCI `build` job, which:

1. Checks out source code
2. Installs Composer dependencies
3. Validates PHP syntax on all `.php` files
4. Runs `php index.php` as a smoke test
5. Runs unit tests (`tests/unit/`)
6. Runs convergence tests (`tests/convergence/`)
7. Runs the human gate script

A non-zero exit from any step marks the build as failed.
