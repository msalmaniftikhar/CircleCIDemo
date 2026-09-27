# Project Context

## Purpose

PHP Hello World demo showing the full 11-phase Kiro spec-driven pipeline with CircleCI. The app is intentionally minimal — all the interest is in the process and folder structure, not the business logic.

## Coding conventions

- Every PHP file opens with `declare(strict_types=1)`
- Application classes use the `App\` namespace, rooted at `src/`
- PSR-4 autoloading: `App\` → `src/`, `Tests\` → `tests/`
- Unit tests go in `tests/unit/`, convergence/invariant tests in `tests/convergence/`
- Test framework: PHPUnit 10 (`phpunit/phpunit: ^10.0`)
- `index.php` is a bootstrap only — no output logic

## Folder → phase map

| Folder / File | Phase |
|---------------|-------|
| `.kiro/specs/` | 1–4 Requirement, Story, AC, Spec Kit |
| `.kiro/steering/` | 5 Context/Skills |
| `.circleci/config.yml` | 6 Agent Harness |
| `src/`, `composer.json`, `index.php` | 7 Implementation |
| `tests/unit/`, `.kiro/hooks/` | 8 AC/Test Verification |
| `tests/convergence/` | 9 Convergence |
| `scripts/human-gate.sh` | 10 Human Gate |
| `README.md`, `docs/` | 11 Production |
