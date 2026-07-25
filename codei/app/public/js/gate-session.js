document.addEventListener('DOMContentLoaded', () => {

    const sessionId = document.body.dataset.sessionId;
    const stepsTable = document.querySelector('#steps-table');
    const gateStateBox = document.querySelector('#gate-state');

    // Zvuky
    const invalidStepSound = new Audio('/sounds/invalid-step.mp3');
    const gateOpenSound = new Audio('/sounds/gate-open.mp3');

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

    // Animácia pri invalid kroku
    function animateInvalidRow(row) {
        row.style.animation = 'invalid-blink 1s ease';
        setTimeout(() => {
            row.style.animation = '';
        }, 1000);
    }

    // CSS animácia pre invalid krok
    const style = document.createElement('style');
    style.textContent = `
        @keyframes invalid-blink {
            0% { background-color: #f8d4d4; }
            50% { background-color: #ffffff; }
            100% { background-color: #f8d4d4; }
        }

        @keyframes gate-open-blink {
            0% { background-color: #d4f8d4; }
            50% { background-color: #ffffff; }
            100% { background-color: #d4f8d4; }
        }
    `;
    document.head.appendChild(style);

    // Blikajúci indikátor otvorenej brány
    function blinkGateOpen() {
        gateStateBox.style.animation = 'gate-open-blink 1s infinite';
    }

    // WebSocket – živé bez refreshu
    let ws = null;

    function connectWebSocket() {
        ws = new WebSocket('wss://codei.dremont.in/ws/gate-session/' + sessionId);

        ws.onopen = () => {
            notify('WebSocket pripojený', '#007bff');
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);

            if (data.type === 'step-update') {
                renderSteps(data.steps);

                if (data.status === 'invalid') {
                    invalidStepSound.play().catch(() => {});
                    notify(`Krok ${data.step_number} je INVALID`, '#d9534f');

                    const row = document.querySelector(`tr[data-step="${data.step_number}"]`);
                    if (row) animateInvalidRow(row);
                }
            }

            if (data.type === 'gate-open') {
                gateOpenSound.play().catch(() => {});
                notify('Brána je OTVORENÁ!', '#5cb85c');
                blinkGateOpen();
            }
        };

        ws.onclose = () => {
            notify('WebSocket odpojený – pokus o opätovné pripojenie...', '#f0ad4e');
            setTimeout(connectWebSocket, 3000);
        };
    }

   // connectWebSocket();

    // Renderovanie krokov
    function renderSteps(steps) {
        stepsTable.innerHTML = '';

        steps.forEach(step => {
            const row = document.createElement('tr');
            row.dataset.step = step.step_number;

            row.innerHTML = `
                <td>${step.step_number}</td>
                <td>${step.name}</td>
                <td class="status-${step.status}">${step.status}</td>
                <td>${step.validated_at ?? '-'}</td>
                <td>
                    <button data-status="valid" data-id="${step.step_number}">Valid</button>
                    <button data-status="invalid" data-id="${step.step_number}">Invalid</button>
                    <button data-status="pending" data-id="${step.step_number}">Reset</button>
                </td>
            `;

            stepsTable.appendChild(row);
        });

        attachStepButtons();
    }

    function attachStepButtons() {
        document.querySelectorAll('button[data-status]').forEach(btn => {
            btn.addEventListener('click', async () => {
                const status = btn.dataset.status;
                const stepNumber = btn.dataset.id;

                await fetch(`/api/gate/session/${sessionId}/step`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        step_number: stepNumber,
                        name: `Krok ${stepNumber}`,
                        status: status
                    })
                });

                // Lokálne animácie
                if (status === 'invalid') {
                    invalidStepSound.play().catch(() => {});
                    notify(`Krok ${stepNumber} je INVALID`, '#d9534f');

                    const row = document.querySelector(`tr[data-step="${stepNumber}"]`);
                    if (row) animateInvalidRow(row);
                }

                await updateGateState();
                await loadSteps();
            });
        });
    }

    async function loadSteps() {
        const response = await fetch(`/api/gate/session/${sessionId}/steps`);
        const steps = await response.json();
        renderSteps(steps);
    }

    async function updateGateState() {
        const response = await fetch(`/api/gate/session/${sessionId}/state`);
        const data = await response.json();

        gateStateBox.textContent = data.state;
        gateStateBox.className = `state-${data.state}`;

        if (data.state === 'open') {
            blinkGateOpen();
            gateOpenSound.play().catch(() => {});
            notify('Brána je OTVORENÁ!', '#5cb85c');
        }
    }

    // Načítanie pri štarte
    loadSteps();
    updateGateState();
});
