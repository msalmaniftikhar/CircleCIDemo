# Implementation Plan: circleci-hello-world-demo

## Pipeline phase map

| Phase | Name | Artifact |
|-------|------|----------|
| 1–3 | Requirement → Story → AC | `requirements.md` ✅ |
| 4 | Spec Kit | `design.md` ✅ |
| 5 | Context/Skills | `.kiro/steering/project-context.md` ✅ |
| 6 | Agent Harness | `.circleci/config.yml` ✅ |
| 7 | Implementation | `src/HelloWorld.php`, `composer.json`, `index.php` ✅ |
| 8 | AC/Test Verification | `tests/unit/HelloWorldTest.php`, `.kiro/hooks/ac-verification.json` ✅ |
| 9 | Convergence | `tests/convergence/ConvergenceTest.php` ✅ |
| 10 | Human Gate | `scripts/human-gate.sh` ✅ |
| 11 | Production | `README.md`, `docs/pipeline-stages.md` ✅ |

---

## Tasks

- [x] 1. Create the agent steering context file
  - Create `.kiro/steering/project-context.md` describing the project purpose, 11-phase pipeline map, folder layout, and coding conventions
  - _Requirements: none (meta-context)_

- [x] 2. Set up the CircleCI pipeline
  - Rewrite `.circleci/config.yml` with CircleCI 2.1, `cimg/php:8.2` executor, and a single `build` job
  - Steps: checkout → composer install → PHP lint → run app → unit tests → convergence tests → human gate
  - _Requirements: 5.1–5.5_

- [x] 3. Implement the application
  - [x] 3.1 Create `composer.json` with `phpunit/phpunit:^10.0` dev dependency and PSR-4 autoloading (`App\` → `src/`)
    - _Requirements: 3.1–3.3_
  - [x] 3.2 Create `src/HelloWorld.php` with `getMessage()`, `getAppName()`, and `run()` methods
    - _Requirements: 1.1–1.6_
  - [x] 3.3 Simplify `index.php` to load the autoloader and call `HelloWorld::run()`
    - _Requirements: 2.1–2.2_

- [x] 4. Write the test suite
  - [x] 4.1 Create `tests/unit/HelloWorldTest.php` — example-based tests for all three methods
    - _Requirements: 4.1–4.4_
  - [x] 4.2 Create `tests/convergence/ConvergenceTest.php` — invariant tests (idempotency, instance consistency)
    - _Requirements: 4.5–4.6_
  - [x] 4.3 Create `.kiro/hooks/ac-verification.json` — re-run unit tests on every PHP file save
    - _Requirements: 4.1_

- [x] 5. Add the human gate script
  - Create `scripts/human-gate.sh` — runs the full test suite and blocks the pipeline if anything fails
  - _Requirements: 5.3–5.4_

- [x] 6. Finalize production docs
  - [x] 6.1 Update `README.md` with CircleCI badge, pipeline table, and local setup instructions
    - _Requirements: 6.1–6.4_
  - [x] 6.2 Create `docs/pipeline-stages.md` with a Mermaid flowchart and per-phase narrative
    - _Requirements: none (documentation)_

---

## Task dependency graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1", "3.1"] },
    { "id": 1, "tasks": ["2", "3.2"] },
    { "id": 2, "tasks": ["3.3"] },
    { "id": 3, "tasks": ["4.1", "4.2", "4.3"] },
    { "id": 4, "tasks": ["5"] },
    { "id": 5, "tasks": ["6.1", "6.2"] }
  ]
}
```
