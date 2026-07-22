#!/usr/bin/env bash
set -euo pipefail

# Spustí CodeIgniter lokálny server a otvorí aplikáciu v prehliadači.
#
# Použitie:
#   ./startApp.sh
#   ./startApp.sh --host=127.0.0.1 --port=8080
#   ./startApp.sh --no-browser
#   ./startApp.sh --skip-ext-check

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$ROOT_DIR/codei"

HOST="127.0.0.1"
PORT="8080"
NO_BROWSER=false
SKIP_EXT_CHECK=false

for arg in "$@"; do
  case "$arg" in
    --host=*)
      HOST="${arg#*=}"
      ;;
    --port=*)
      PORT="${arg#*=}"
      ;;
    --no-browser)
      NO_BROWSER=true
      ;;
    --skip-ext-check)
      SKIP_EXT_CHECK=true
      ;;
    -h|--help)
      cat <<'EOF'
Usage: ./startApp.sh [--host=IP] [--port=PORT] [--no-browser] [--skip-ext-check]

Spustí CodeIgniter server v adresári codei/ a otvorí URL v prehliadači.

Voľby:
  --host=IP      Predvolené: 127.0.0.1
  --port=PORT    Predvolené: 8080
  --no-browser   Neotvorí prehliadač
  --skip-ext-check
                 Preskočí kontrolu povinných PHP extensionov (intl, mbstring)
EOF
      exit 0
      ;;
    *)
      echo "Unknown argument: $arg" >&2
      echo "Use --help for usage." >&2
      exit 1
      ;;
  esac
done

if [[ ! -f "$APP_DIR/spark" ]]; then
  echo "Error: missing CodeIgniter launcher at $APP_DIR/spark" >&2
  exit 1
fi

if ! command -v php >/dev/null 2>&1; then
  echo "Error: php command is not available on PATH." >&2
  exit 1
fi

if [[ "$SKIP_EXT_CHECK" == false ]]; then
  REQUIRED_EXTENSIONS=("intl" "mbstring")
  MISSING_EXTENSIONS=()

  for extension in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m | grep -qi "^${extension}$"; then
      MISSING_EXTENSIONS+=("$extension")
    fi
  done

  if [[ "${#MISSING_EXTENSIONS[@]}" -gt 0 ]]; then
    echo "Error: missing required PHP extension(s): ${MISSING_EXTENSIONS[*]}" >&2
    echo "CodeIgniter 4 requires these extensions to start." >&2
    echo "Install them and rerun ./startApp.sh, or use --skip-ext-check." >&2
    exit 1
  fi
fi

cd "$APP_DIR"
APP_URL="http://${HOST}:${PORT}/"

echo "Starting CodeIgniter server at ${APP_URL}"
php spark serve --host "$HOST" --port "$PORT" &
SERVER_PID=$!

if ! kill -0 "$SERVER_PID" >/dev/null 2>&1; then
  echo "Error: CodeIgniter server failed to start." >&2
  wait "$SERVER_PID" || true
  exit 1
fi

cleanup() {
  if kill -0 "$SERVER_PID" >/dev/null 2>&1; then
    kill "$SERVER_PID" >/dev/null 2>&1 || true
  fi
}
trap cleanup EXIT INT TERM

if [[ "$NO_BROWSER" == false ]]; then
  if [[ -n "${BROWSER:-}" ]]; then
    "$BROWSER" "$APP_URL" >/dev/null 2>&1 || true
  elif command -v xdg-open >/dev/null 2>&1; then
    xdg-open "$APP_URL" >/dev/null 2>&1 || true
  else
    echo "Browser launcher not found. Open manually: $APP_URL"
  fi
fi

wait "$SERVER_PID"