# Project Context: CircleCI Hello World Demo

## Project Purpose

This is a PHP Hello World application designed to demonstrate Kiro spec-driven development with a full 11-phase pipeline. Each phase maps to a concrete artifact or code deliverable, and the CircleCI 2.1 pipeline automates validation from syntax check through test execution on every push.

The project is intentionally minimal — no framework, no database, no external services — so the pipeline structure itself is the primary demonstration artifact.

---

## The 11-Phase Pipeline

| Phase | Name             | Folder / File                                        | Requirement IDs Satisfied       |
|-------|------------------|------------------------------------------------------|---------------------------------|
| 1     | Requirement      | `.kiro/specs/circleci-hello-world-demo/requirements.md` | (meta — defines all req IDs) |
| 2     | Story            | User stories embedded in `requirements.md`           | (meta)                          |
| 3     | AC               | Acceptance criteria in `requirements.md`             | (meta)                          |
| 4     | Spec Kit         | `.kiro/specs/circleci-hello-world-demo/design.md`    | (meta)                          |
| 5     | Context / Skills | `.kiro/steering/project-context.md`                  | (meta — this file)              |
| 6     | Agent Harness    | `.circleci/config.yml`                               | 5.1–5.10                        |
| 7     | Implementation   | `src/HelloWorld.php`, `composer.json`, `index.php`   | 1.1–1.5, 2.1–2.3, 3.1–3.3      |
| 8     | AC/Test Verify   | `tests/unit/HelloWorldTest.php`, `.kiro/hooks/`      | 4.1–4.5                         |
| 9     | Convergence      | `tests/convergence/ConvergenceTest.php`              | 1.4, 1.5, 2.2, 2.3             |
| 10    | Human Gate       | `scripts/human-gate.sh`                              | 5.8                             |
| 11    | Production       | `README.md`, `docs/pipeline-stages.md`               | 6.1–6.3                         |

Phases 1–4 are complete (artifacts exist). Phases 5–11 are active code phases implemented by tasks in the spec.

---

## Folder Layout

```
CircleCIDemo/
├── .circleci/
│   └── config.yml                          # Phase 6 — CircleCI 2.1 pipeline
├── .kiro/
│   ├── hooks/
│   │   └── ac-verification.json            # Phase 8 — PostFileSave hook for unit tests
│   ├── specs/
│   │   └── circleci-hello-world-demo/
│   │       ├── requirements.md             # Phase 1–3 — requirements and ACs
│   │       ├── design.md                   # Phase 4 — architecture and properties
│   │       └── tasks.md                    # Implementation plan
│   └── steering/
│       └── project-context.md             # Phase 5 — this file
├── docs/
│   └── pipeline-stages.md                 # Phase 11 — narrative pipeline doc
├── scripts/
│   └── human-gate.sh                      # Phase 10 — pre-push gate script
├── src/
│   └── HelloWorld.php                     # Phase 7 — HelloWorld class (App namespace)
├── tests/
│   ├── convergence/
│   │   └── ConvergenceTest.php            # Phase 9 — invariant/property tests
│   └── unit/
│       └── HelloWorldTest.php             # Phase 8 — PHPUnit unit tests
├── vendor/                                # Composer-managed (gitignored)
├── composer.json                          # Phase 7 — dependency manifest + PSR-4
├── composer.lock                          # Locked dependency versions
├── index.php                              # Phase 7 — thin entry point
└── README.md                              # Phase 11 — documentation with CI badge
```

---

## Coding Conventions

- **Strict types**: Every PHP file opens with `declare(strict_types=1);` immediately after `<?php`.
- **Namespace**: Application classes live under the `App\` namespace, rooted at `src/`. Example: `App\HelloWorld` → `src/HelloWorld.php`.
- **PSR-4 autoloading**: Configured in `composer.json`. `App\` maps to `src/`, `Tests\` maps to `tests/`.
- **Test location**: Unit tests go in `tests/unit/`, convergence/property tests go in `tests/convergence/`.
- **Test framework**: PHPUnit 10 (`phpunit/phpunit: ^10.0`). Test classes extend `PHPUnit\Framework\TestCase`.
- **Test naming**: Test methods are prefixed with `test` (e.g., `testGetMessage()`). Each test class corresponds to one source class.
- **Output capture**: Tests that verify stdout output use `ob_start()` / `ob_get_clean()` — never `expectOutputString()` for multi-line assertions.
- **No framework dependencies**: `composer.json` requires only `php: >=8.2` at runtime and `phpunit/phpunit: ^10.0` at dev time.
- **Entry point rule**: `index.php` contains only bootstrap logic (`require_once vendor/autoload.php`) and a single `->run()` call. No output logic lives in `index.php`.

---

## Requirement IDs in Scope

| Group | IDs       | Subject                          |
|-------|-----------|----------------------------------|
| 1     | 1.1–1.5   | HelloWorld class and `run()` output |
| 2     | 2.1–2.3   | `index.php` entry point delegation |
| 3     | 3.1–3.3   | `composer.json` and Composer setup |
| 4     | 4.1–4.5   | PHPUnit test suite               |
| 5     | 5.1–5.10  | CircleCI pipeline configuration  |
| 6     | 6.1–6.3   | README documentation             |

Detailed acceptance criteria for each ID are in `.kiro/specs/circleci-hello-world-demo/requirements.md`.

---

## Key Design Decisions

- **Single CI job**: All pipeline steps run in one `build` job. No parallelism needed for a project this size.
- **Lint before run**: PHP syntax validation (`php -l`) runs before `php index.php` so parse errors surface with clear output rather than a cryptic runtime failure.
- **Two test suites in CI**: The CircleCI config runs `tests/unit/` and `tests/convergence/` as separate steps so failures are immediately locatable by suite.
- **Human gate as final step**: `scripts/human-gate.sh` is the last CI step and re-runs the full test suite, acting as a final checkpoint before any production promotion.
- **Properties over examples where meaningful**: Two correctness properties are defined in `design.md` (Property 1: `run()` output structure; Property 2: entry-point delegation). Both appear in `tests/unit/` (inline) and `tests/convergence/` (dedicated) to demonstrate Phase 9 as a distinct pipeline stage.
