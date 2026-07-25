#!/usr/bin/env bash
set -Eeuo pipefail

BASE_URL="${KROK11_BASE_URL:-http://127.0.0.1:8091}"
HOST="${KROK11_HOST:-127.0.0.1}"
PORT="${KROK11_PORT:-8091}"
DIAGNOSTICS_TOKEN="${METODIKA_DIAGNOSTICS_TOKEN:-krok11-secret-token}"
RUN_DIRECTORY="${KROK11_RUN_DIRECTORY:-writable/diagnostics/concurrency}"
TMP_DIR="$(mktemp -d)"
COOKIE_JAR="${TMP_DIR}/cookies.txt"
SERVER_LOG="${TMP_DIR}/server.log"
SERVER_PID=""
RUN_ID=""
REQUEST_REFERENCE="krok11-http-$(date +%s)-${RANDOM}"

cleanup() {
    local status=$?

    if [[ -n "${SERVER_PID}" ]] && kill -0 "${SERVER_PID}" 2>/dev/null; then
        kill "${SERVER_PID}" 2>/dev/null || true
        wait "${SERVER_PID}" 2>/dev/null || true
    fi

    if [[ -n "${RUN_ID}" ]]; then
        rm -f "${RUN_DIRECTORY}/${RUN_ID}.json" \
              "${RUN_DIRECTORY}/${RUN_ID}.lock" \
              "${RUN_DIRECTORY}/${RUN_ID}.json.tmp."* 2>/dev/null || true
    fi

    if [[ -n "${REQUEST_REFERENCE}" ]]; then
        php -r '
            mysqli_report(MYSQLI_REPORT_OFF);
            $host = getenv("database_default_hostname") ?: "127.0.0.1";
            $user = getenv("database_default_username") ?: "root";
            $pass = getenv("database_default_password") ?: "root";
            $name = getenv("database_default_database") ?: "metodika_krok11";
            $port = (int) (getenv("database_default_port") ?: 3306);
            $request = getenv("KROK11_REQUEST_REFERENCE") ?: "";
            if ($request === "") { exit(0); }
            $db = @new mysqli($host, $user, $pass, $name, $port);
            if ($db->connect_errno) { exit(0); }
            $requestEscaped = $db->real_escape_string($request);
            $ids = [];
            if ($result = $db->query("SELECT id FROM question_derivation_runs WHERE request_reference = \"{$requestEscaped}\"")) {
                while ($row = $result->fetch_assoc()) { $ids[] = (int) $row["id"]; }
            }
            if ($ids !== []) {
                $idList = implode(",", $ids);
                $db->query("DELETE FROM question_derivation_run_domain_terms WHERE run_id IN ({$idList})");
                $db->query("DELETE FROM question_derivation_runs WHERE id IN ({$idList})");
            }
            $db->query("DELETE FROM question_derivation_request_reservations WHERE request_reference = \"{$requestEscaped}\"");
        ' >/dev/null 2>&1 || true
    fi

    if [[ ${status} -ne 0 ]]; then
        echo "--- Krok 11 HTTP server log ---" >&2
        cat "${SERVER_LOG}" >&2 2>/dev/null || true
    fi

    rm -rf "${TMP_DIR}"
    exit "${status}"
}
trap cleanup EXIT INT TERM

export KROK11_REQUEST_REFERENCE="${REQUEST_REFERENCE}"

extract_csrf() {
    local html_file=$1
    php -r '
        $html = file_get_contents($argv[1]);
        if (!preg_match("/<input[^>]+type=\"hidden\"[^>]+name=\"([^\"]+)\"[^>]+value=\"([^\"]+)\"/i", $html, $m)
            && !preg_match("/<input[^>]+name=\"([^\"]+)\"[^>]+value=\"([^\"]+)\"[^>]+type=\"hidden\"/i", $html, $m)) {
            fwrite(STDERR, "CSRF hidden input not found\n");
            exit(1);
        }
        echo html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5), "\n", html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5), "\n";
    ' "${html_file}"
}

json_value() {
    local file=$1
    local expression=$2
    php -r '
        $data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
        $value = $data;
        foreach (explode(".", $argv[2]) as $key) {
            if (!is_array($value) || !array_key_exists($key, $value)) { exit(2); }
            $value = $value[$key];
        }
        if (is_bool($value)) { echo $value ? "true" : "false"; }
        elseif ($value === null) { echo "null"; }
        elseif (is_scalar($value)) { echo (string) $value; }
        else { echo json_encode($value, JSON_UNESCAPED_SLASHES); }
    ' "${file}" "${expression}"
}

http_code() {
    local method=$1
    local url=$2
    local output=$3
    shift 3
    curl --silent --show-error \
        --request "${method}" \
        --cookie "${COOKIE_JAR}" \
        --cookie-jar "${COOKIE_JAR}" \
        --output "${output}" \
        --write-out '%{http_code}' \
        "$@" \
        "${url}"
}

mkdir -p "${RUN_DIRECTORY}"

php spark serve --host "${HOST}" --port "${PORT}" >"${SERVER_LOG}" 2>&1 &
SERVER_PID=$!

for _ in $(seq 1 60); do
    code="$(curl --silent --output "${TMP_DIR}/probe.html" --write-out '%{http_code}' "${BASE_URL}/diagnostics/database/login" || true)"
    if [[ "${code}" == "200" ]]; then
        break
    fi
    sleep 0.25
done

if [[ "${code:-}" != "200" ]]; then
    echo "Diagnostický server sa nespustil." >&2
    exit 1
fi

curl --silent --show-error --cookie-jar "${COOKIE_JAR}" \
    "${BASE_URL}/diagnostics/database/login" >"${TMP_DIR}/login.html"
mapfile -t login_csrf < <(extract_csrf "${TMP_DIR}/login.html")
LOGIN_CSRF_NAME="${login_csrf[0]}"
LOGIN_CSRF_HASH="${login_csrf[1]}"

login_code="$(http_code POST "${BASE_URL}/diagnostics/database/login" "${TMP_DIR}/login-response.html" \
    --data-urlencode "${LOGIN_CSRF_NAME}=${LOGIN_CSRF_HASH}" \
    --data-urlencode "diagnostics_token=${DIAGNOSTICS_TOKEN}")"
[[ "${login_code}" == "302" ]] || { echo "LOGIN HTTP ${login_code}" >&2; exit 1; }

page_code="$(http_code GET "${BASE_URL}/diagnostics/database" "${TMP_DIR}/database.html")"
[[ "${page_code}" == "200" ]] || { echo "DATABASE HTTP ${page_code}" >&2; exit 1; }
grep -F 'PRIPRAVENE' "${TMP_DIR}/database.html" >/dev/null

mapfile -t start_csrf < <(extract_csrf "${TMP_DIR}/database.html")
START_CSRF_NAME="${start_csrf[0]}"
START_CSRF_HASH="${start_csrf[1]}"

start_code="$(http_code POST "${BASE_URL}/diagnostics/concurrency/start" "${TMP_DIR}/start.json" \
    --data-urlencode "${START_CSRF_NAME}=${START_CSRF_HASH}" \
    --data-urlencode "requestReference=${REQUEST_REFERENCE}" \
    --data-urlencode 'derivationApplicationInput={"krok":11,"mode":"http-e2e"}')"
[[ "${start_code}" == "200" ]] || { echo "START HTTP ${start_code}" >&2; cat "${TMP_DIR}/start.json" >&2; exit 1; }
[[ "$(json_value "${TMP_DIR}/start.json" ok)" == "true" ]]
RUN_ID="$(json_value "${TMP_DIR}/start.json" runId)"
TOKEN_A="$(json_value "${TMP_DIR}/start.json" participantTokenA)"
TOKEN_B="$(json_value "${TMP_DIR}/start.json" participantTokenB)"

[[ -f "${RUN_DIRECTORY}/${RUN_ID}.json" ]]
[[ -f "${RUN_DIRECTORY}/${RUN_ID}.lock" ]]

(
    curl --silent --show-error --request POST --cookie "${COOKIE_JAR}" \
        --output "${TMP_DIR}/hit-a.json" --write-out '%{http_code}' \
        --data-urlencode "runId=${RUN_ID}" \
        --data-urlencode "participantToken=${TOKEN_A}" \
        "${BASE_URL}/diagnostics/concurrency/hit/a" >"${TMP_DIR}/hit-a.status"
) &
pid_a=$!
(
    curl --silent --show-error --request POST --cookie "${COOKIE_JAR}" \
        --output "${TMP_DIR}/hit-b.json" --write-out '%{http_code}' \
        --data-urlencode "runId=${RUN_ID}" \
        --data-urlencode "participantToken=${TOKEN_B}" \
        "${BASE_URL}/diagnostics/concurrency/hit/b" >"${TMP_DIR}/hit-b.status"
) &
pid_b=$!

wait "${pid_a}"
wait "${pid_b}"
[[ "$(cat "${TMP_DIR}/hit-a.status")" == "200" ]]
[[ "$(cat "${TMP_DIR}/hit-b.status")" == "200" ]]

result_code=""
for _ in $(seq 1 40); do
    result_code="$(http_code GET "${BASE_URL}/diagnostics/concurrency/result/${RUN_ID}" "${TMP_DIR}/result.json")"
    if [[ "${result_code}" == "200" ]]; then
        break
    fi
    sleep 0.25
done
[[ "${result_code}" == "200" ]] || { echo "RESULT HTTP ${result_code}" >&2; exit 1; }

[[ "$(json_value "${TMP_DIR}/result.json" state)" == "COMPLETED_SUCCESS" ]]
[[ "$(json_value "${TMP_DIR}/result.json" assertions.dbUniquenessConfirmed)" == "true" ]]
[[ "$(json_value "${TMP_DIR}/result.json" assertions.appReplayConfirmed)" == "true" ]]
[[ "$(json_value "${TMP_DIR}/result.json" assertions.cleanupConfirmed)" == "true" ]]
[[ "$(json_value "${TMP_DIR}/result.json" assertions.overallSuccess)" == "true" ]]

outcome_a="$(json_value "${TMP_DIR}/result.json" participants.a.outcome)"
outcome_b="$(json_value "${TMP_DIR}/result.json" participants.b.outcome)"
outcomes="$(printf '%s\n%s\n' "${outcome_a}" "${outcome_b}" | sort | paste -sd+ -)"
[[ "${outcomes}" == "ALREADY_EXISTS+CREATED" ]]

grep -F '"readOnceConsumedAt"' "${TMP_DIR}/result.json" >/dev/null
! grep -F '"input"' "${TMP_DIR}/result.json" >/dev/null
! grep -F '"tokenHash"' "${TMP_DIR}/result.json" >/dev/null

[[ -f "${RUN_DIRECTORY}/${RUN_ID}.json" ]]
[[ -f "${RUN_DIRECTORY}/${RUN_ID}.lock" ]]
if compgen -G "${RUN_DIRECTORY}/${RUN_ID}.json.tmp.*" >/dev/null; then
    echo "Po E2E zostal dočasný run-store súbor." >&2
    exit 1
fi

php -r '
    $path = $argv[1];
    $document = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    $document["deleteAfter"] = gmdate("c", time() - 1);
    $temp = $path . ".krok11-sweep";
    file_put_contents($temp, json_encode($document, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    rename($temp, $path);
' "${RUN_DIRECTORY}/${RUN_ID}.json"

sweep_code="$(http_code GET "${BASE_URL}/diagnostics/concurrency/result/${RUN_ID}" "${TMP_DIR}/sweep.json")"
[[ "${sweep_code}" == "404" ]]
[[ ! -e "${RUN_DIRECTORY}/${RUN_ID}.json" ]]
[[ ! -e "${RUN_DIRECTORY}/${RUN_ID}.lock" ]]

php -r '
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli(
        getenv("database_default_hostname") ?: "127.0.0.1",
        getenv("database_default_username") ?: "root",
        getenv("database_default_password") ?: "root",
        getenv("database_default_database") ?: "metodika_krok11",
        (int) (getenv("database_default_port") ?: 3306),
    );
    $request = $db->real_escape_string(getenv("KROK11_REQUEST_REFERENCE"));
    $tables = [
        "question_derivation_request_reservations" => "request_reference",
        "question_derivation_runs" => "request_reference",
    ];
    foreach ($tables as $table => $column) {
        $count = (int) $db->query("SELECT COUNT(*) c FROM {$table} WHERE {$column}=\"{$request}\"")->fetch_assoc()["c"];
        echo $table, "=", $count, PHP_EOL;
        if ($count !== 0) { exit(2); }
    }
' >"${TMP_DIR}/db-cleanup.txt"

page_code="$(http_code GET "${BASE_URL}/diagnostics/database" "${TMP_DIR}/database-before-logout.html")"
[[ "${page_code}" == "200" ]]
mapfile -t logout_csrf < <(extract_csrf "${TMP_DIR}/database-before-logout.html")
LOGOUT_CSRF_NAME="${logout_csrf[0]}"
LOGOUT_CSRF_HASH="${logout_csrf[1]}"

logout_code="$(http_code POST "${BASE_URL}/diagnostics/database/logout" "${TMP_DIR}/logout.html" \
    --data-urlencode "${LOGOUT_CSRF_NAME}=${LOGOUT_CSRF_HASH}")"
[[ "${logout_code}" == "302" ]]

after_logout_code="$(http_code GET "${BASE_URL}/diagnostics/database" "${TMP_DIR}/after-logout.html")"
[[ "${after_logout_code}" == "404" ]]

printf '%s\n' \
    'KROK11_HTTP_E2E_CONFIRMED' \
    'DB_UNIQUENESS=true' \
    'OUTCOMES=CREATED+ALREADY_EXISTS' \
    'CLEANUP=true' \
    'STATE=COMPLETED_SUCCESS' \
    'TOMBSTONE=true' \
    'SWEEP=true' \
    'LOGIN_DATABASE_LOGOUT=true'
