#!/usr/bin/env python3
"""
Technicky cleanup nazvov ciest bez diakritiky.

Pouzitie v koreni repozitara:
  python3 tools/rename_paths_without_diacritics.py --dry-run
  python3 tools/rename_paths_without_diacritics.py --apply

Skript:
- prejde gitom sledovane subory,
- premenuje iba cesty, v ktorych je diakritika,
- medzery a velkost pismen ponecha,
- po premenovani aplikuje nove nazvy v textovych suboroch.
"""

from __future__ import annotations

import argparse
import subprocess
import sys
import unicodedata
from pathlib import Path

TEXT_SUFFIXES = {
    ".md", ".txt", ".php", ".js", ".css", ".json", ".xml", ".yml", ".yaml",
    ".env", ".example", ".sh", ".sql", ".ini", ".neon", ".lock", ".dist",
}

SKIP_DIRS = {".git", "vendor", "node_modules"}


def run_git(args: list[str], check: bool = True) -> subprocess.CompletedProcess[str]:
    return subprocess.run(["git", *args], text=True, stdout=subprocess.PIPE, stderr=subprocess.PIPE, check=check)


def strip_diacritics(text: str) -> str:
    return unicodedata.normalize("NFKD", text).encode("ascii", "ignore").decode("ascii")


def tracked_paths() -> list[str]:
    result = run_git(["ls-files", "-z"])
    return [p for p in result.stdout.split("\0") if p]


def is_text_file(path: Path) -> bool:
    if not path.is_file():
        return False
    if path.suffix in TEXT_SUFFIXES:
        return True
    name = path.name.lower()
    return name in {"readme", "changelog", "license", ".gitignore", ".env.example"}


def safe_read(path: Path) -> str | None:
    try:
        return path.read_text(encoding="utf-8")
    except UnicodeDecodeError:
        return None


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--apply", action="store_true", help="vykonat zmeny")
    parser.add_argument("--dry-run", action="store_true", help="iba vypisat plan")
    args = parser.parse_args()

    if args.apply == args.dry_run:
        print("Pouzi presne jeden prepinac: --dry-run alebo --apply", file=sys.stderr)
        return 2

    status = run_git(["status", "--short"]).stdout.strip()
    if status:
        print("STOP: working tree nie je cisty. Najprv commit/stash/rollback.", file=sys.stderr)
        print(status, file=sys.stderr)
        return 1

    paths = tracked_paths()
    rename_map: dict[str, str] = {}
    for path in paths:
        new_path = strip_diacritics(path)
        if new_path != path:
            rename_map[path] = new_path

    if not rename_map:
        print("OK: v sledovanych cestach nie je diakritika.")
        return 0

    reverse: dict[str, list[str]] = {}
    for old, new in rename_map.items():
        reverse.setdefault(new, []).append(old)
    collisions = {new: olds for new, olds in reverse.items() if len(olds) > 1}
    existing_collisions = [new for new in reverse if new not in rename_map and Path(new).exists()]
    if collisions or existing_collisions:
        print("STOP: kolizia cielovych nazvov.", file=sys.stderr)
        for new, olds in collisions.items():
            print(f"KOLIZIA {new}: {olds}", file=sys.stderr)
        for new in existing_collisions:
            print(f"EXISTUJE {new}", file=sys.stderr)
        return 1

    print("Mapa premenovania:")
    for old, new in sorted(rename_map.items()):
        print(f"  {old} -> {new}")

    if args.dry_run:
        print("\nDRY-RUN: ziadne zmeny neboli vykonane.")
        return 0

    # Premenovanie robime od najhlbsich ciest, aby sa nerozpadli rodicovske presuny.
    for old, new in sorted(rename_map.items(), key=lambda item: item[0].count("/"), reverse=True):
        old_path = Path(old)
        new_path = Path(new)
        new_path.parent.mkdir(parents=True, exist_ok=True)
        if old_path.exists():
            run_git(["mv", old, new])

    # Aktualizacia textovych odkazov po premenovani.
    replacements = sorted(rename_map.items(), key=lambda item: len(item[0]), reverse=True)
    changed_files: list[str] = []
    for path in Path(".").rglob("*"):
        if any(part in SKIP_DIRS for part in path.parts):
            continue
        if not is_text_file(path):
            continue
        content = safe_read(path)
        if content is None:
            continue
        new_content = content
        for old, new in replacements:
            new_content = new_content.replace(old, new)
        if new_content != content:
            path.write_text(new_content, encoding="utf-8")
            changed_files.append(str(path))

    remaining = [p for p in tracked_paths() if strip_diacritics(p) != p]
    if remaining:
        print("STOP: po premenovani stale existuju sledovane cesty s diakritikou:", file=sys.stderr)
        for path in remaining:
            print(path, file=sys.stderr)
        return 1

    print("\nZmenene textove subory:")
    for path in changed_files:
        print(f"  {path}")
    print("\nHOTOVO: spusti git status a skontroluj diff.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
