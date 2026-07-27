'use strict';

document.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('sessions-table');
    const status = document.getElementById('gate-dashboard-status');

    if (!(table instanceof HTMLTableElement) || !(status instanceof HTMLElement)) {
        return;
    }

    const body = table.tBodies.item(0);
    const sessionsUrl = table.dataset.sessionsUrl;
    const sessionBaseUrl = table.dataset.sessionBaseUrl;
    const knownStates = new Set(['locked', 'verifying', 'open']);

    if (!(body instanceof HTMLTableSectionElement) || !sessionsUrl || !sessionBaseUrl) {
        status.textContent = 'Konfigurácia dashboardu nie je úplná.';
        return;
    }

    const textCell = (row, value) => {
        const cell = row.insertCell();
        cell.textContent = value === null || value === undefined ? '—' : String(value);
        return cell;
    };

    const render = (sessions) => {
        body.replaceChildren();

        const sorted = [...sessions].sort((left, right) => {
            const order = { open: 0, verifying: 1, locked: 2 };
            const stateComparison = (order[left.gate_state] ?? 3) - (order[right.gate_state] ?? 3);
            return stateComparison !== 0 ? stateComparison : Number(right.id ?? 0) - Number(left.id ?? 0);
        });

        for (const session of sorted) {
            if (!session || typeof session !== 'object') {
                continue;
            }

            const id = Number(session.id);
            if (!Number.isSafeInteger(id) || id <= 0) {
                continue;
            }

            const row = body.insertRow();
            textCell(row, id);
            textCell(row, session.project_name);
            textCell(row, session.agent_name);

            const state = knownStates.has(session.gate_state) ? session.gate_state : 'locked';
            const stateCell = textCell(row, state);
            stateCell.classList.add(`state-${state}`);

            textCell(row, session.created_at);

            const detailCell = row.insertCell();
            const link = document.createElement('a');
            link.textContent = 'Otvoriť';
            link.href = `${sessionBaseUrl}/${encodeURIComponent(String(id))}`;
            detailCell.appendChild(link);
        }

        status.textContent = sorted.length === 0
            ? 'Nie sú evidované žiadne sessions.'
            : `Načítané sessions: ${sorted.length}.`;
    };

    const load = async () => {
        try {
            const response = await fetch(sessionsUrl, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error(`HTTP_${response.status}`);
            }

            const payload = await response.json();
            if (!payload || payload.ok !== true || !Array.isArray(payload.sessions)) {
                throw new Error('INVALID_RESPONSE');
            }

            render(payload.sessions);
        } catch {
            status.textContent = 'Sessions sa nepodarilo bezpečne načítať.';
        }
    };

    void load();
});
