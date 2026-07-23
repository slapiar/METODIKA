<?php
$title = 'METODIKA | DB capability';
$activeNav = 'diagnostics';
$showFooter = false;
?>

<?= $this->extend('layouts/app') ?>

<?= $this->section('meta') ?>
<meta name="robots" content="noindex,nofollow,noarchive">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="panel">
    <h1>Diagnostika databazy METODIKA</h1>
    <table class="table">
        <tr>
            <td>Externe prostredie nacitane</td>
            <td class="<?= $externalEnvironmentLoaded ? 'ok' : 'bad' ?>"><?= $externalEnvironmentLoaded ? 'ANO' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>Databazove spojenie</td>
            <td class="<?= $inspection['connection'] ? 'ok' : 'bad' ?>"><?= $inspection['connection'] ? 'OK' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>Databazovy server</td>
            <td><?= esc($inspection['serverVersion'] !== '' ? $inspection['serverVersion'] : 'NEZISTENA') ?></td>
        </tr>
        <tr>
            <td>InnoDB</td>
            <td class="<?= $inspection['innodb'] ? 'ok' : 'bad' ?>"><?= $inspection['innodb'] ? 'OK' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>utf8mb4_bin</td>
            <td class="<?= $inspection['utf8mb4Bin'] ? 'ok' : 'bad' ?>"><?= $inspection['utf8mb4Bin'] ? 'OK' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>DATETIME(6)</td>
            <td class="<?= $inspection['datetime6'] ? 'ok' : 'bad' ?>"><?= $inspection['datetime6'] ? 'OK' : 'NIE' ?></td>
        </tr>
        <tr>
            <td>Celkovy vysledok</td>
            <td class="<?= $overallReady ? 'ok' : 'bad' ?>"><?= $overallReady ? 'PRIPRAVENE' : 'NEPRIPRAVENE' ?></td>
        </tr>
        <tr>
            <td>Cas diagnostiky</td>
            <td><?= esc((string) $inspection['diagnosedAt']) ?></td>
        </tr>
    </table>

    <div class="actions">
        <form method="post" action="<?= site_url('diagnostics/database/run') ?>">
            <?= csrf_field() ?>
            <button type="submit">Spustit znovu</button>
        </form>
        <form method="post" action="<?= site_url('diagnostics/database/logout') ?>">
            <?= csrf_field() ?>
            <button type="submit">Odhlasit diagnostiku</button>
        </form>
    </div>

    <p class="notice">Diagnostika je iba citacia. Migracie neboli spustene.</p>

    <?php if (($concurrencyWebEnabled ?? false) === true): ?>
        <hr class="diag-separator">
        <section class="diag-concurrency" aria-labelledby="diag-concurrency-title">
            <h2 id="diag-concurrency-title">Webove subezne overenie</h2>
            <p class="diag-help">Spusti jeden diagnosticky run. Stranka odosle dva paralelne requesty a vyhodnoti vysledok bez zobrazenia citlivych internych detailov.</p>

            <div class="actions">
                <button id="diag-concurrency-start" type="button">Start</button>
            </div>

            <p id="diag-concurrency-status" class="diag-status" role="status" aria-live="polite">Pripravene.</p>

            <dl class="diag-axes" id="diag-axes" hidden>
                <div>
                    <dt>DB unikatnost</dt>
                    <dd id="diag-axis-db">NEPOTVRDENE</dd>
                </div>
                <div>
                    <dt>Aplikacny replay</dt>
                    <dd id="diag-axis-replay">NEPOTVRDENE</dd>
                </div>
                <div>
                    <dt>Cleanup</dt>
                    <dd id="diag-axis-cleanup">NEPOTVRDENE</dd>
                </div>
                <div>
                    <dt>Celkove</dt>
                    <dd id="diag-axis-overall">NEPOTVRDENE</dd>
                </div>
            </dl>

            <div id="diag-participants" class="diag-participants" hidden>
                <p><strong>A:</strong> <span id="diag-participant-a">-</span></p>
                <p><strong>B:</strong> <span id="diag-participant-b">-</span></p>
            </div>
        </section>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>

<?php if (($concurrencyWebEnabled ?? false) === true): ?>
    <?= $this->section('styles') ?>
    <style>
        .diag-separator {
            margin: 1.5rem 0;
            border: 0;
            border-top: 1px solid #d8e0ec;
        }

        .diag-help {
            color: #4b5c75;
        }

        .diag-status {
            margin-top: 0.8rem;
            font-weight: 600;
        }

        .diag-axes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.6rem;
            margin: 1rem 0 0;
            padding: 0;
        }

        .diag-axes div {
            border: 1px solid #d8e0ec;
            border-radius: 0.5rem;
            background: #eef2f7;
            padding: 0.7rem;
        }

        .diag-axes dt {
            font-size: 0.82rem;
            color: #4b5c75;
            margin: 0;
        }

        .diag-axes dd {
            margin: 0.25rem 0 0;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .diag-pass {
            color: #166534;
        }

        .diag-fail {
            color: #991b1b;
        }

        .diag-participants {
            margin-top: 0.8rem;
            font-size: 0.9rem;
            color: #10213b;
        }
    </style>
    <?= $this->endSection() ?>

    <?= $this->section('scripts') ?>
    <script nonce="<?= esc((string) ($scriptNonce ?? '')) ?>">
        (function () {
            const startButton = document.getElementById('diag-concurrency-start');
            const statusNode = document.getElementById('diag-concurrency-status');
            const axes = document.getElementById('diag-axes');
            const axisDb = document.getElementById('diag-axis-db');
            const axisReplay = document.getElementById('diag-axis-replay');
            const axisCleanup = document.getElementById('diag-axis-cleanup');
            const axisOverall = document.getElementById('diag-axis-overall');
            const participants = document.getElementById('diag-participants');
            const participantA = document.getElementById('diag-participant-a');
            const participantB = document.getElementById('diag-participant-b');

            if (!startButton || !statusNode || !axes || !axisDb || !axisReplay || !axisCleanup || !axisOverall || !participants || !participantA || !participantB) {
                return;
            }

            const csrfName = <?= json_encode(csrf_token(), JSON_UNESCAPED_SLASHES) ?>;
            const csrfHash = <?= json_encode(csrf_hash(), JSON_UNESCAPED_SLASHES) ?>;

            const setAxis = (node, value) => {
                if (value === true) {
                    node.textContent = 'POTVRDENE';
                    node.className = 'diag-pass';
                    return;
                }

                node.textContent = value === false ? 'NEPOTVRDENE' : 'NEZNAME';
                node.className = 'diag-fail';
            };

            const summarizeParticipant = (slot) => {
                if (!slot || typeof slot !== 'object') {
                    return 'nezname';
                }

                const outcome = typeof slot.outcome === 'string' && slot.outcome !== '' ? slot.outcome : 'bez-vysledku';
                const errorCode = typeof slot.errorCode === 'string' && slot.errorCode !== '' ? slot.errorCode : null;

                return errorCode ? outcome + ' (' + errorCode + ')' : outcome;
            };

            const parseJson = async (response) => {
                const contentType = response.headers.get('content-type') || '';
                if (contentType.indexOf('application/json') !== -1) {
                    return await response.json();
                }

                const text = await response.text();
                return JSON.parse(text);
            };

            const fetchResult = async (runId) => {
                for (let i = 0; i < 24; i++) {
                    const response = await fetch('/diagnostics/concurrency/result/' + encodeURIComponent(runId), {
                        method: 'GET',
                        credentials: 'same-origin',
                        cache: 'no-store',
                    });

                    if (response.status === 200) {
                        return await parseJson(response);
                    }

                    await new Promise((resolve) => window.setTimeout(resolve, 300));
                }

                throw new Error('result-timeout');
            };

            startButton.addEventListener('click', async () => {
                startButton.disabled = true;
                axes.hidden = true;
                participants.hidden = true;
                statusNode.textContent = 'Spustam run...';

                try {
                    const startData = new URLSearchParams();
                    startData.set(csrfName, csrfHash);

                    const startResponse = await fetch('/diagnostics/concurrency/start', {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                        },
                        body: startData.toString(),
                    });

                    if (startResponse.status !== 200) {
                        throw new Error('start-failed');
                    }

                    const started = await parseJson(startResponse);
                    if (!started || typeof started.runId !== 'string' || typeof started.participantTokenA !== 'string' || typeof started.participantTokenB !== 'string') {
                        throw new Error('start-invalid');
                    }

                    statusNode.textContent = 'Odosielam paralelne HIT A/B...';

                    const hitRequest = (path, token) => {
                        const body = new URLSearchParams();
                        body.set('runId', started.runId);
                        body.set('participantToken', token);

                        return fetch(path, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                            },
                            body: body.toString(),
                        });
                    };

                    const hitResults = await Promise.all([
                        hitRequest('/diagnostics/concurrency/hit/a', started.participantTokenA),
                        hitRequest('/diagnostics/concurrency/hit/b', started.participantTokenB),
                    ]);

                    if (hitResults[0].status !== 200 || hitResults[1].status !== 200) {
                        throw new Error('hit-failed');
                    }

                    statusNode.textContent = 'Nacitavam vysledok...';

                    const result = await fetchResult(started.runId);
                    const assertions = result && typeof result === 'object' ? result.assertions : null;

                    setAxis(axisDb, assertions && assertions.dbUniquenessConfirmed === true);
                    setAxis(axisReplay, assertions && assertions.appReplayConfirmed === true);
                    setAxis(axisCleanup, assertions && assertions.cleanupConfirmed === true);
                    setAxis(axisOverall, assertions && assertions.overallSuccess === true);

                    participantA.textContent = summarizeParticipant(result.participants ? result.participants.a : null);
                    participantB.textContent = summarizeParticipant(result.participants ? result.participants.b : null);

                    axes.hidden = false;
                    participants.hidden = false;
                    statusNode.textContent = 'Hotovo. Stav runu: ' + (typeof result.state === 'string' ? result.state : 'UNKNOWN');
                } catch (error) {
                    statusNode.textContent = 'Beh zlyhal alebo vyprsal. Skus Start znovu.';
                    axes.hidden = true;
                    participants.hidden = true;
                } finally {
                    startButton.disabled = false;
                }
            });
        })();
    </script>
    <?= $this->endSection() ?>
<?php endif; ?>
