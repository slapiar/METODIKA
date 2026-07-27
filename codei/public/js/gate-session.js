'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const stepsTable = document.getElementById('steps-table');
    const gateState = document.getElementById('gate-state');
    const status = document.getElementById('gate-session-status');
    const knownStates = new Set(['locked', 'verifying', 'open']);
    const knownStepStatuses = new Set(['pending', 'valid', 'invalid']);

    if (
        !(stepsTable instanceof HTMLTableSectionElement)
        || !(gateState instanceof HTMLElement)
        || !(status instanceof HTMLElement)
    ) {
        return;
    }

    const stepsUrl = body.dataset.stepsUrl;
    const stateUrl = body.dataset.stateUrl;
    const stepWriteUrl = body.dataset.stepWriteUrl;
    let csrfHash = body.dataset.csrfHash;

    if (!stepsUrl || !stateUrl || !stepWriteUrl || !csrfHash) {
        status.textContent = 'Konfigurácia detailu session nie je úplná.';
        return;
    }

    const textCell = (row, value) => {
        const cell = row.insertCell();
        cell.textContent = value === null || value === undefined || value === '' ? '—' : String(value);
        return cell;
    };

    const setGateState = (value) => {
        const safeState = knownStates.has(value) ? value : 'locked';
        gateState.textContent = safeState;
        gateState.className = `state-${safeState}`;
    };

    const writeStep = async (step, nextStatus) => {
        status.textContent = `Zapisujem krok ${step.step_number}…`;

        const response = await fetch(stepWriteUrl, {
            method: 'POST',
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfHash,
            },
            body: JSON.stringify({
                step_number: step.step_number,
                name: step.name || `Krok ${step.step_number}`,
                status: nextStatus,
            }),
        });

        const payload = await response.json().catch(() => null);
        if (payload && typeof payload.csrfHash === 'string' && payload.csrfHash !== '') {
            csrfHash = payload.csrfHash;
            body.dataset.csrfHash = csrfHash;
        }

        if (!response.ok || !payload || payload.ok !== true) {
            throw new Error('STEP_WRITE_FAILED');
        }

        if (typeof payload.state === 'string') {
            setGateState(payload.state);
        }
    };

    const makeActionButton = (step, nextStatus, label) => {
        const button = document.createElement('button');
        button.type = 'button';
        button.dataset.status = nextStatus;
        button.textContent = label;
        button.addEventListener('click', async () => {
            button.disabled = true;
            try {
                await writeStep(step, nextStatus);
                await loadSteps();
                status.textContent = `Krok ${step.step_number} bol bezpečne aktualizovaný.`;
            } catch {
                status.textContent = `Krok ${step.step_number} sa nepodarilo aktualizovať.`;
            } finally {
                button.disabled = false;
            }
        });
        return button;
    };

    const renderSteps = (steps) => {
        stepsTable.replaceChildren();

        for (const step of steps) {
            if (!step || typeof step !== 'object') {
                continue;
            }

            const stepNumber = Number(step.step_number);
            if (!Number.isSafeInteger(stepNumber) || stepNumber < 1 || stepNumber > 15) {
                continue;
            }

            const safeStatus = knownStepStatuses.has(step.status) ? step.status : 'pending';
            const safeStep = {
                step_number: stepNumber,
                name: typeof step.name === 'string' ? step.name : `Krok ${stepNumber}`,
            };

            const row = stepsTable.insertRow();
            row.dataset.step = String(stepNumber);
            textCell(row, stepNumber);
            textCell(row, safeStep.name);

            const stateCell = textCell(row, safeStatus);
            stateCell.classList.add(`status-${safeStatus}`);
            textCell(row, step.validated_at);

            const actions = row.insertCell();
            actions.append(
                makeActionButton(safeStep, 'valid', 'Valid'),
                makeActionButton(safeStep, 'invalid', 'Invalid'),
                makeActionButton(safeStep, 'pending', 'Reset'),
            );
        }
    };

    const loadSteps = async () => {
        const response = await fetch(stepsUrl, {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { Accept: 'application/json' },
        });
        const payload = await response.json().catch(() => null);

        if (!response.ok || !payload || payload.ok !== true || !Array.isArray(payload.steps)) {
            throw new Error('STEPS_READ_FAILED');
        }

        renderSteps(payload.steps);
    };

    const loadState = async () => {
        const response = await fetch(stateUrl, {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { Accept: 'application/json' },
        });
        const payload = await response.json().catch(() => null);

        if (!response.ok || !payload || payload.ok !== true || typeof payload.state !== 'string') {
            throw new Error('STATE_READ_FAILED');
        }

        setGateState(payload.state);
    };

    Promise.all([loadSteps(), loadState()])
        .then(() => {
            status.textContent = 'Session je načítaná.';
        })
        .catch(() => {
            status.textContent = 'Session sa nepodarilo bezpečne načítať.';
        });
});
