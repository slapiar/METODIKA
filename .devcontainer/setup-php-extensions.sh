#!/usr/bin/env bash
set -euo pipefail

# Rebuild active Codespaces PHP runtime with required extensions.
# Target: same PHP patch version, plus intl + mysqli.

PHP_BIN="${PHP_BIN:-$(command -v php || true)}"
if [[ -z "$PHP_BIN" || ! -x "$PHP_BIN" ]]; then
  echo "Error: php binary not found on PATH." >&2
  exit 1
fi

PHP_BIN="$(readlink -f "$PHP_BIN")"
PHP_VERSION="$($PHP_BIN -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION . "." . PHP_RELEASE_VERSION;')"

has_extension() {
  local ext="$1"
  "$PHP_BIN" -m | grep -qi "^${ext}$"
}

if has_extension "intl" && has_extension "mysqli"; then
  echo "PHP ${PHP_VERSION} already has intl and mysqli."
  exit 0
fi

if ! command -v sudo >/dev/null 2>&1; then
  echo "Error: sudo is required for runtime installation." >&2
  exit 1
fi

if ! sudo -n true >/dev/null 2>&1; then
  echo "Error: passwordless sudo is required for non-interactive setup." >&2
  exit 1
fi

INSTALL_DIR="/usr/local/php/${PHP_VERSION}-metodika"
PHP_CURRENT_LINK="/usr/local/php/current"
USER_CURRENT_LINK="/home/codespace/.php/current"

if [[ -x "${INSTALL_DIR}/bin/php" ]]; then
  if "${INSTALL_DIR}/bin/php" -m | grep -qi '^intl$' && "${INSTALL_DIR}/bin/php" -m | grep -qi '^mysqli$'; then
    sudo ln -sfn "$INSTALL_DIR" "$PHP_CURRENT_LINK"
    ln -sfn "$PHP_CURRENT_LINK" "$USER_CURRENT_LINK"
    echo "Reusing existing runtime: ${INSTALL_DIR}"
    "$PHP_CURRENT_LINK/bin/php" -m | grep -E '^(intl|mysqli)$'
    exit 0
  fi
fi

sudo apt-get update
sudo apt-get install -y --no-install-recommends \
  build-essential \
  autoconf \
  pkg-config \
  re2c \
  bison \
  libxml2-dev \
  libsqlite3-dev \
  zlib1g-dev \
  libcurl4-openssl-dev \
  libonig-dev \
  libssl-dev \
  libsodium-dev \
  libargon2-dev \
  libedit-dev \
  libicu-dev \
  xz-utils \
  ca-certificates \
  curl \
  wget

BUILD_DIR="$(mktemp -d)"
trap 'rm -rf "$BUILD_DIR"' EXIT

PHP_TARBALL="${BUILD_DIR}/php-${PHP_VERSION}.tar.xz"
PHP_SRC_DIR="${BUILD_DIR}/php-src"
mkdir -p "$PHP_SRC_DIR"

wget -q -O "$PHP_TARBALL" "https://www.php.net/distributions/php-${PHP_VERSION}.tar.xz"
tar -xf "$PHP_TARBALL" -C "$PHP_SRC_DIR" --strip-components=1

pushd "$PHP_SRC_DIR" >/dev/null

./configure \
  --prefix="${INSTALL_DIR}" \
  --with-config-file-path="${INSTALL_DIR}/ini" \
  --with-config-file-scan-dir="${INSTALL_DIR}/ini/conf.d" \
  --enable-option-checking=fatal \
  --with-curl \
  --with-libedit \
  --enable-mbstring \
  --with-openssl \
  --with-zlib \
  --with-password-argon2 \
  --with-sodium=shared \
  --enable-intl \
  --with-mysqli \
  --with-pear

make -j"$(nproc)"
sudo make install

sudo mkdir -p "${INSTALL_DIR}/ini/conf.d"
sudo cp -f php.ini-development "${INSTALL_DIR}/ini/php.ini-development"
sudo cp -f php.ini-production "${INSTALL_DIR}/ini/php.ini-production"
sudo cp -f php.ini-production "${INSTALL_DIR}/ini/php.ini"

popd >/dev/null

sudo ln -sfn "$INSTALL_DIR" "$PHP_CURRENT_LINK"
ln -sfn "$PHP_CURRENT_LINK" "$USER_CURRENT_LINK"

NEW_PHP="${PHP_CURRENT_LINK}/bin/php"
if [[ ! -x "$NEW_PHP" ]]; then
  echo "Error: rebuilt PHP binary is missing: $NEW_PHP" >&2
  exit 1
fi

if ! "$NEW_PHP" -m | grep -qi '^intl$' || ! "$NEW_PHP" -m | grep -qi '^mysqli$'; then
  echo "Error: rebuilt PHP runtime does not expose intl and mysqli." >&2
  exit 1
fi

echo "Rebuilt PHP runtime activated at ${PHP_CURRENT_LINK}."
"$NEW_PHP" -v | sed -n '1,3p'
"$NEW_PHP" --ini | sed -n '1,8p'
"$NEW_PHP" -m | grep -E '^(intl|mysqli)$'
