#!/usr/bin/env bash
# Phase 10 — Human Gate
# Final check before production. Runs the full test suite and blocks if anything fails.

set -euo pipefail

PHPUNIT="vendor/bin/phpunit"

if [ ! -f "$PHPUNIT" ]; then
  echo "ERROR: vendor/bin/phpunit not found. Run 'composer install' first." >&2
  exit 1
fi

echo "Running full test suite..."

if ! XDEBUG_MODE=off "$PHPUNIT" --testdox tests/unit/ tests/convergence/; then
  echo "[HUMAN GATE] Tests failed — pipeline blocked." >&2
  exit 1
fi

echo "[HUMAN GATE] All checks passed — ready for production."
