document.addEventListener('DOMContentLoaded', () => {

    const table = document.querySelector('#sessions-table');

    // Zvuky
    const gateOpenSound = new Audio('/sounds/gate-open.mp3');
    const invalidStepSound = new Audio('/sounds/invalid-step.mp3');

    // Mini notifikácie
    const notifyBox = document.createElement('div');
    notifyBox.style.position = 'fixed';
    notifyBox.style.top = '20px';
    notifyBox.style.right = '20px';
    notifyBox.style.zIndex = '9999';
    document.body.appendChild(notifyBox);

    function notify(message, color = '#333') {
        const note = document.createElement('div');
        note.textContent = message;
        note.style.background = color;
        note.style.color = 'white';
        note.style.padding = '10px 15px';
        note.style.marginTop = '10px';
        note.style.borderRadius = '6px';
        note.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';
        note.style.opacity = '1';
        note.style.transition = 'opacity 1s ease';

        notifyBox.appendChild(note);

        setTimeout(() => {
            note.style.opacity = '0';
            setTimeout(() => note.remove(), 1000);
        }, 3000);
    }

    // Farebné animácie
    function animateStateChange(cell, newState) {
        cell.classList.remove('state-locked', 'state-verifying', 'state-open');
        cell.classList.add(`state-${newState}`);

        cell.style.transition = 'background-color 0.6s ease, color 0.6s ease';
        cell.style.backgroundColor =
            newState === 'open' ? '#d4f8d4' :
            newState === 'verifying' ? '#fff4d4' :
            '#f8d4d4';

        setTimeout(() => {
            cell.style.backgroundColor = '';
        }, 600);
    }

    // Blikajúci indikátor otvorenej brány
    function blinkOpenGate(cell) {
        if (!cell) return;

        cell.style.animation = 'blink-open 1s infinite';
    }

    // CSS animácia pre blikajúci efekt
    const style = document.createElement('style');
    style.textContent = `
        @keyframes blink-open {
            0% { background-color: #d4f8d4; }
            50% { background-color: #ffffff; }
            100% { background-color: #d4f8d4; }
        }
    `;
    document.head.appendChild(style);

    // Zoradenie podľa stavu
    function sortSessions(sessions) {
        const order = { open: 0, verifying: 1, locked: 2 };
        return sessions.sort((a, b) => order[a.gate_state] - order[b.gate_state]);
    }

    // WebSocket – živé dáta bez refreshu
    let ws = null;

    function connectWebSocket() {
        ws = new WebSocket('wss://codei.dremont.in/ws/gate');

        ws.onopen = () => {
            notify('WebSocket pripojený', '#007bff');
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);

            if (data.type === 'session-update') {
                renderSessions(data.sessions);
            }

            if (data.type === 'invalid-step') {
                invalidStepSound.play().catch(() => {});
                notify(`Krok ${data.step_number} je INVALID`, '#d9534f');
            }

            if (data.type === 'gate-open') {
                gateOpenSound.play().catch(() => {});
                notify('Brána je OTVORENÁ!', '#5cb85c');
            }
        };

        ws.onclose = () => {
            notify('WebSocket odpojený – pokus o opätovné pripojenie...', '#f0ad4e');
            setTimeout(connectWebSocket, 3000);
        };
    }

    //connectWebSocket();

    // Renderovanie sessions
    function renderSessions(sessions) {
        sessions = sortSessions(sessions);
        table.innerHTML = '';

        sessions.forEach(s => {
            const row = document.createElement('tr');

            row.innerHTML = `
                <td>${s.id}</td>
                <td>${s.project_name}</td>
                <td>${s.agent_name}</td>
                <td class="state-${s.gate_state}">${s.gate_state}</td>
                <td>${s.created_at}</td>
                <td><a href="/gate/session/${s.id}">Otvoriť</a></td>
            `;

            table.appendChild(row);

            const stateCell = row.children[3];

            animateStateChange(stateCell, s.gate_state);

            if (s.gate_state === 'open') {
                blinkOpenGate(stateCell);
            }
        });
    }

});
