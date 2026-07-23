#!/usr/bin/env bash
set -euo pipefail

# Vytvori nasaditelny release ZIP pre CodeIgniter projekt v codei/.
# Archiv je urceny pre Hostinger fallback deployment bez zmeny web rootu,
# teda pre rozbalenie do public_html tak, aby vznikol adresar public_html/codei.
#
# Použitie:
#   ./release.sh
#   ./release.sh 3.1.5
#   ./release.sh patch|minor|major|mini
#   ./release.sh patch --auto-commit
#   ./release.sh patch --auto-commit --auto-push

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

VERSION_FILE="$ROOT_DIR/RELEASE_VERSION"
PROJECT_DIR="$ROOT_DIR/codei"
DEFAULT_VERSION="1.0.0"
AUTO_COMMIT=false
AUTO_PUSH=false
COMMIT_MESSAGE=""
TARGET_LAYOUT="codei"

VERSION=""
BUMP_MODE=""
for arg in "$@"; do
  case "$arg" in
    --auto-commit)
      AUTO_COMMIT=true
      ;;
    --auto-push)
      AUTO_PUSH=true
      ;;
    --commit-message=*)
      COMMIT_MESSAGE="${arg#*=}"
      ;;
    --target=codei)
      TARGET_LAYOUT="codei"
      ;;
    --target=*)
      echo "Error: unsupported target '${arg#*=}'. Supported target is: codei" >&2
      exit 1
      ;;
    patch|minor|major|mini)
      if [[ -n "$VERSION" || -n "$BUMP_MODE" ]]; then
        echo "Usage: $0 [--auto-commit] [--auto-push] [--commit-message=...] [--target=codei] [version|patch|minor|major|mini]" >&2
        exit 1
      fi
      BUMP_MODE="$arg"
      ;;
    -h|--help)
      cat <<'EOF'
Usage: ./release.sh [--auto-commit] [--auto-push] [--commit-message=...] [--target=codei] [version|patch|minor|major|mini]

Vytvori ZIP pre deployment CodeIgniter projektu codei/ na Hostinger shared hostingu.
ZIP je pripraveny na rozbalenie do public_html, kde vytvori public_html/codei.

Voľby:
  --auto-commit           Commitne RELEASE_VERSION a vytvoreny ZIP
  --auto-push             Po commite pushne aktuálnu pracovnú vetvu
  --commit-message=MSG    Vlastná správa auto-commitu
  --target=codei          Cielovy layout release (predvolene codei)

Verzia:
  version                 Explicitná verzia, napr. 3.1.5
  patch                   x.y.z -> x.y.(z+1)
  minor|mini              x.y.z -> x.(y+1).0
  major                   x.y.z -> (x+1).0.0
EOF
      exit 0
      ;;
    *)
      if [[ -n "$VERSION" ]]; then
        echo "Usage: $0 [--auto-commit] [--auto-push] [--commit-message=...] [--target=codei] [version|patch|minor|major|mini]" >&2
        exit 1
      fi
      VERSION="$arg"
      ;;
  esac
done

if [[ "$AUTO_PUSH" == true && "$AUTO_COMMIT" == false ]]; then
  AUTO_COMMIT=true
fi

if [[ -f "$VERSION_FILE" ]]; then
  CURRENT_VERSION="$(tr -d '[:space:]' < "$VERSION_FILE")"
else
  CURRENT_VERSION="$DEFAULT_VERSION"
fi

if [[ -z "$CURRENT_VERSION" ]]; then
  CURRENT_VERSION="$DEFAULT_VERSION"
fi

if [[ -n "$BUMP_MODE" ]]; then
  if ! [[ "$CURRENT_VERSION" =~ ^([0-9]+)\.([0-9]+)\.([0-9]+)$ ]]; then
    echo "Error: current version '$CURRENT_VERSION' is not x.y.z and cannot be bumped automatically." >&2
    echo "Use an explicit version, for example: ./release.sh 3.1.5" >&2
    exit 1
  fi

  major="${BASH_REMATCH[1]}"
  minor="${BASH_REMATCH[2]}"
  patch="${BASH_REMATCH[3]}"

  case "$BUMP_MODE" in
    patch)
      patch=$((patch + 1))
      ;;
    minor|mini)
      minor=$((minor + 1))
      patch=0
      ;;
    major)
      major=$((major + 1))
      minor=0
      patch=0
      ;;
  esac

  VERSION="${major}.${minor}.${patch}"
elif [[ -z "$VERSION" ]]; then
  VERSION="$CURRENT_VERSION"
fi

if ! [[ "$VERSION" =~ ^[0-9]+\.[0-9]+\.[0-9]+([-+][0-9A-Za-z.-]+)?$ ]]; then
  echo "Error: invalid version '$VERSION'. Expected x.y.z, for example 3.1.5 or 3.1.5-rc1." >&2
  exit 1
fi

if [[ ! -d "$PROJECT_DIR" ]]; then
  echo "Error: missing project directory: $PROJECT_DIR" >&2
  exit 1
fi

if [[ "$TARGET_LAYOUT" != "codei" ]]; then
  echo "Error: unsupported release target layout: $TARGET_LAYOUT" >&2
  exit 1
fi

REQUIRED_PROJECT_PATHS=(
  "codei/public/index.php"
  "codei/public/.htaccess"
  "codei/app/Config/App.php"
  "codei/system/CodeIgniter.php"
  "codei/index.php"
  "codei/.htaccess"
)
for required_path in "${REQUIRED_PROJECT_PATHS[@]}"; do
  if [[ ! -e "$ROOT_DIR/$required_path" ]]; then
    echo "Error: missing required runtime path: $required_path" >&2
    exit 1
  fi
done

# Pred release musi byt pracovny strom cisty. Povoli sa iba release marker
# a predosle release ZIPy.
DIRTY_STATUS="$(git status --porcelain | grep -vE '^[ MARC?DU]{1,2} (RELEASE_VERSION|releases/[^ ]+\.zip)$' || true)"
if [[ -n "$DIRTY_STATUS" ]]; then
  echo "Error: git working tree is not clean. Commit or stash your changes before releasing." >&2
  echo "$DIRTY_STATUS" >&2
  exit 1
fi

printf '%s\n' "$VERSION" > "$VERSION_FILE"

OUT_DIR="$ROOT_DIR/releases"
OUT_FILE="$OUT_DIR/metodika-codei-hostinger-${VERSION}.zip"
TMP_DIR="$(mktemp -d)"
SOURCE_LIST="$TMP_DIR/codei-source-files.txt"
STAGE_DIR="$TMP_DIR/release-root"
ARCHIVE_LIST="$TMP_DIR/archive-files.txt"
trap 'rm -rf "$TMP_DIR"' EXIT

mkdir -p "$OUT_DIR" "$STAGE_DIR"

# Zdrojom je vyhradne Gitom sledovany obsah codei/.
git -C "$ROOT_DIR" ls-files "codei/" > "$SOURCE_LIST"

if [[ ! -s "$SOURCE_LIST" ]]; then
  echo "Error: no tracked files found in codei/." >&2
  exit 1
fi

while IFS= read -r source_path; do
  [[ -n "$source_path" ]] || continue

  if [[ "$source_path" != codei/* ]]; then
    echo "Error: release source escaped codei/: $source_path" >&2
    exit 1
  fi

  relative_path="${source_path#codei/}"
  if [[ -z "$relative_path" ]]; then
    continue
  fi

  case "$relative_path" in
    .env|.env.*|local-config.php|.local-config.php|*/local-config.php|*/.local-config.php)
      echo "Error: secret configuration must not be packaged: $source_path" >&2
      exit 1
      ;;
    writable/cache/*|writable/debugbar/*|writable/logs/*|writable/session/*|writable/uploads/*)
      if [[ "$relative_path" != */index.html ]]; then
        continue
      fi
      ;;
    tests/*|phpunit.dist.xml)
      continue
      ;;
    deploy/apache/*)
      continue
      ;;
    vendor/*)
      echo "Error: vendor directory is tracked in git, but must not be released from repository artifacts: $source_path" >&2
      exit 1
      ;;
  esac

  if [[ ! -f "$ROOT_DIR/$source_path" ]]; then
    echo "Error: tracked app file is missing from working tree: $source_path" >&2
    exit 1
  fi

  if [[ "$source_path" == *.php ]]; then
    if ! php -l "$ROOT_DIR/$source_path" >/dev/null 2>&1; then
      echo "Error: PHP syntax error in $source_path" >&2
      exit 1
    fi
  fi

  mkdir -p "$STAGE_DIR/codei/$(dirname "$relative_path")"
  cp -p "$ROOT_DIR/$source_path" "$STAGE_DIR/codei/$relative_path"
done < "$SOURCE_LIST"

cp -p "$VERSION_FILE" "$STAGE_DIR/codei/RELEASE_VERSION"

mkdir -p "$STAGE_DIR/codei/deploy"
printf '%s\n' "$VERSION" > "$STAGE_DIR/codei/deploy/RELEASE_VERSION.txt"

rm -f "$OUT_FILE"
(
  cd "$STAGE_DIR"
  find . -type f -print \
    | sed 's#^\./##' \
    | LC_ALL=C sort \
    | zip -q -9 "$OUT_FILE" -@
)

# Výpis archívu sa uloží raz. Tým sa vyhneme chybným výsledkom pipeline
# unzip|grep pri zapnutom pipefail.
unzip -Z1 "$OUT_FILE" > "$ARCHIVE_LIST"

if [[ ! -s "$ARCHIVE_LIST" ]]; then
  rm -f "$OUT_FILE"
  echo "Error: created archive is empty." >&2
  exit 1
fi

if grep -Eq '^(app|public|system|writable|deploy)/' "$ARCHIVE_LIST"; then
  rm -f "$OUT_FILE"
  echo "Error: archive root is invalid. Expected top-level directory codei/." >&2
  exit 1
fi

if grep -Ev '^codei/' "$ARCHIVE_LIST" >/dev/null; then
  rm -f "$OUT_FILE"
  echo "Error: archive contains entries outside codei/ root." >&2
  exit 1
fi

if grep -Eq '(^|/)(\.env(\..+)?)$|(^|/)(\.local-config\.php|local-config\.php)$' "$ARCHIVE_LIST"; then
  rm -f "$OUT_FILE"
  echo "Error: archive contains secret configuration files (.env/local-config)." >&2
  exit 1
fi

REQUIRED_ARCHIVE_FILES=(
  "codei/index.php"
  "codei/.htaccess"
  "codei/RELEASE_VERSION"
  "codei/public/index.php"
  "codei/public/.htaccess"
  "codei/app/Config/App.php"
  "codei/deploy/RELEASE_VERSION.txt"
)
for required_file in "${REQUIRED_ARCHIVE_FILES[@]}"; do
  if ! grep -Fxq "$required_file" "$ARCHIVE_LIST"; then
    rm -f "$OUT_FILE"
    echo "Error: created archive does not contain $required_file." >&2
    exit 1
  fi
done

for required_prefix in "codei/app/" "codei/system/" "codei/writable/" "codei/public/"; do
  if ! grep -q "^${required_prefix}" "$ARCHIVE_LIST"; then
    rm -f "$OUT_FILE"
    echo "Error: created archive does not contain runtime tree $required_prefix" >&2
    exit 1
  fi
done

ARCHIVED_VERSION="$(unzip -p "$OUT_FILE" codei/deploy/RELEASE_VERSION.txt | tr -d '[:space:]')"
if [[ "$ARCHIVED_VERSION" != "$VERSION" ]]; then
  rm -f "$OUT_FILE"
  echo "Error: archived release marker '$ARCHIVED_VERSION' does not match requested version '$VERSION'." >&2
  exit 1
fi

ARCHIVED_ROOT_VERSION="$(unzip -p "$OUT_FILE" codei/RELEASE_VERSION | tr -d '[:space:]')"
if [[ "$ARCHIVED_ROOT_VERSION" != "$VERSION" ]]; then
  rm -f "$OUT_FILE"
  echo "Error: archived root version '$ARCHIVED_ROOT_VERSION' does not match requested version '$VERSION'." >&2
  exit 1
fi

echo "Release created: $OUT_FILE"
echo "Contents: $(wc -l < "$ARCHIVE_LIST") files from codei/"
echo "Deployment: extract ZIP into public_html to create/overwrite public_html/codei"
echo "Verified: archive root is codei/ and includes index.php shim + public front controller"
echo "Verified: archive excludes .env/local-config and runtime writable payload files"
echo "Verified release markers: codei/RELEASE_VERSION=$ARCHIVED_ROOT_VERSION, codei/deploy/RELEASE_VERSION.txt=$ARCHIVED_VERSION"

if [[ "$AUTO_COMMIT" == true ]]; then
  if [[ -z "$COMMIT_MESSAGE" ]]; then
    COMMIT_MESSAGE="Release ${VERSION}"
  fi

  git add "$VERSION_FILE"
  git add -f "$OUT_FILE"

  if git diff --cached --quiet; then
    echo "Auto-commit skipped: no staged changes."
  else
    git commit -m "$COMMIT_MESSAGE"
    echo "Auto-commit created: $COMMIT_MESSAGE"
  fi
fi

if [[ "$AUTO_PUSH" == true ]]; then
  CURRENT_BRANCH="$(git rev-parse --abbrev-ref HEAD)"
  if [[ "$CURRENT_BRANCH" == "HEAD" || -z "$CURRENT_BRANCH" ]]; then
    echo "Error: cannot auto-push from detached HEAD." >&2
    exit 1
  fi

  git push origin "$CURRENT_BRANCH"
  echo "Auto-push completed: origin/$CURRENT_BRANCH"
fi