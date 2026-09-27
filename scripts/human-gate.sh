#!/usr/bin/env bash
# Phase 10 — Human Gate
#
# Final local and CI gate. Runs the full test suite (unit + convergence)
# before production deployment.
#
# Exit 0 = all checks passed, ready for Phase 11 — Production.
# Exit 1 = tests failed, pipeline blocked.
#
# Usage:
#   bash scripts/human-gate.sh          # from project root
#   ./scripts/human-gate.sh             # if executable bit is set

set -euo pipefail

echo "╔══════════════════════════════════════════╗"
echo "║         KIRO SPEC — HUMAN GATE           ║"
echo "║  Phase 10 of 11: Pre-Production Check    ║"
echo "╚══════════════════════════════════════════╝"
echo ""

PHPUNIT="vendor/bin/phpunit"

if [ ! -f "$PHPUNIT" ]; then
  echo "[HUMAN GATE] ERROR: PHPUnit not found. Run 'composer install' first." >&2
  exit 1
fi

echo "Running Phase 8 — Unit Tests (AC Verification)..."
echo "─────────────────────────────────────────────────"
if ! "$PHPUNIT" --testdox tests/unit/; then
  echo "" >&2
  echo "[HUMAN GATE] Tests failed — pipeline blocked." >&2
  exit 1
fi

echo ""
echo "Running Phase 9 — Convergence Tests (Invariant Verification)..."
echo "────────────────────────────────────────────────────────────────"
if ! "$PHPUNIT" --testdox tests/convergence/; then
  echo "" >&2
  echo "[HUMAN GATE] Convergence tests failed — pipeline blocked." >&2
  exit 1
fi

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║  [HUMAN GATE] All checks passed.         ║"
echo "║  Ready for Phase 11 — Production.        ║"
echo "╚══════════════════════════════════════════╝"
exit 0
