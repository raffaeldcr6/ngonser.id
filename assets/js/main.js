document.addEventListener('DOMContentLoaded', () => {

    const flash = document.querySelector('.flash');
    if (flash) setTimeout(() => flash.style.display = 'none', 5000);

    document.querySelectorAll('.ticket-counter').forEach(counter => {
        const btn_minus = counter.querySelector('.btn-minus');
        const btn_plus = counter.querySelector('.btn-plus');
        const input = counter.querySelector('input');
        const form = counter.closest('form');

        const max = parseInt(input.getAttribute('max') || '99');
        const harga = parseFloat(form?.dataset.harga || 0);

        const totalEl = form?.querySelector('#total-harga');
        const hiddenTotal = form?.querySelector('#hidden-total');

        function updateTotal() {
            let val = parseInt(input.value);

            if (isNaN(val) || val < 1) val = 1;
            if (val > max) val = max;

            input.value = val;

            const total = val * harga;

            if (totalEl) {
                totalEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            }

            if (hiddenTotal) {
                hiddenTotal.value = total;
            }
        }

        btn_minus?.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            if (val > 1) {
                input.value = val - 1;
                updateTotal();
            }
        });

        btn_plus?.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            if (val < max) {
                input.value = val + 1;
                updateTotal();
            }
        });

        input.addEventListener('input', updateTotal);

        updateTotal();
    });

    document.querySelectorAll('[data-modal-target]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.modalTarget;
            document.getElementById(id)?.classList.add('open');
        });
    });

    document.querySelectorAll('.modal-close, .modal-overlay').forEach(el => {
        el.addEventListener('click', (e) => {
            if (e.target === el) e.currentTarget.closest('.modal-overlay')?.classList.remove('open');
        });
    });

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', e => e.stopPropagation());
    });

    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm || 'Yakin?')) e.preventDefault();
        });
    });

    const currentPath = window.location.pathname.split('/').pop();
    document.querySelectorAll('.sidebar-item').forEach(item => {
        const href = item.getAttribute('href')?.split('/').pop();
        if (href === currentPath) item.classList.add('active');
    });
});

class DeadlockSimulator {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.logEl = document.getElementById('deadlock-log');
        this.state = 'idle';
        this.step = 0;
        this.steps = [
            { actor: 'Andi', action: 'lock', res: 'Tiket A', msg: '🔒 Andi mengunci <b>Tiket A (VIP)</b>' },
            { actor: 'Budi', action: 'lock', res: 'Tiket B', msg: '🔒 Budi mengunci <b>Tiket B (Festival)</b>' },
            { actor: 'Andi', action: 'wait', res: 'Tiket B', msg: '⏳ Andi meminta <b>Tiket B</b> — menunggu Budi melepaskan...' },
            { actor: 'Budi', action: 'wait', res: 'Tiket A', msg: '⏳ Budi meminta <b>Tiket A</b> — menunggu Andi melepaskan...' },
            { actor: 'DBMS', action: 'detect', res: null, msg: '🚨 DEADLOCK TERDETEKSI! Circular wait: Andi→Tiket B→Budi→Tiket A→Andi' },
            { actor: 'DBMS', action: 'rollback', res: null, msg: '↩️ DBMS memilih <b>Budi</b> sebagai victim → ROLLBACK transaksi Budi' },
            { actor: 'Andi', action: 'proceed', res: 'Tiket B', msg: '✅ Andi berhasil mengunci Tiket B — transaksi Andi COMMIT' },
            { actor: 'Budi', action: 'retry', res: null, msg: '🔄 Budi me-retry transaksi dengan Lock Ordering (ID rendah lebih dulu)' },
        ];
    }

    log(msg, type = 'info') {
        if (!this.logEl) return;
        const line = document.createElement('div');
        line.className = `log-line log-${type}`;
        line.innerHTML = `<span class="log-time">${new Date().toLocaleTimeString('id-ID')}</span> ${msg}`;
        this.logEl.appendChild(line);
        this.logEl.scrollTop = this.logEl.scrollHeight;
    }

    renderCanvas() {
        const svg = this.container?.querySelector('.arrow-svg');
        if (!svg) return;

        const andi = document.getElementById('node-andi');
        const budi = document.getElementById('node-budi');
        const tikA = document.getElementById('node-tikA');
        const tikB = document.getElementById('node-tikB');

        if (!andi || !budi || !tikA || !tikB) return;

        const getCenter = (el) => {
            const r = el.getBoundingClientRect();
            const cr = this.container.getBoundingClientRect();
            return {
                x: r.left - cr.left + r.width / 2,
                y: r.top - cr.top + r.height / 2
            };
        };

        const arrows = [];

        if (this.step >= 1) arrows.push({ from: getCenter(andi), to: getCenter(tikA), color: '#50b4f8', label: 'holds' });
        if (this.step >= 2) arrows.push({ from: getCenter(budi), to: getCenter(tikB), color: '#50b4f8', label: 'holds' });
        if (this.step >= 3) arrows.push({ from: getCenter(andi), to: getCenter(tikB), color: '#f05060', label: 'waits', dashed: true });
        if (this.step >= 4) arrows.push({ from: getCenter(budi), to: getCenter(tikA), color: '#f05060', label: 'waits', dashed: true });

        svg.innerHTML = '<defs><marker id="ah" markerWidth="8" markerHeight="8" refX="6" refY="3" orient="auto"><path d="M0,0 L0,6 L8,3 z" fill="currentColor"/></marker></defs>';

        arrows.forEach(a => {
            const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');

            line.setAttribute('x1', a.from.x);
            line.setAttribute('y1', a.from.y);
            line.setAttribute('x2', a.to.x);
            line.setAttribute('y2', a.to.y);
            line.setAttribute('stroke', a.color);
            line.setAttribute('stroke-width', '2');
            line.setAttribute('marker-end', `url(#ah)`);

            if (a.dashed) line.setAttribute('stroke-dasharray', '6,4');

            g.appendChild(line);
            svg.appendChild(g);
        });
    }

    async nextStep() {
        if (this.step >= this.steps.length) return;

        const s = this.steps[this.step];
        const typeMap = {
            lock: 'success',
            wait: 'warning',
            detect: 'danger',
            rollback: 'danger',
            proceed: 'success',
            retry: 'info'
        };

        this.log(s.msg, typeMap[s.action] || 'info');

        if (s.action === 'wait') {
            document.getElementById(`node-${s.actor.toLowerCase()}`)?.classList.add('waiting');
        }

        if (s.action === 'rollback') {
            document.getElementById('node-budi')?.classList.remove('waiting');
            document.getElementById('node-budi').style.opacity = '.4';
        }

        if (s.action === 'proceed') {
            document.getElementById('node-andi')?.classList.remove('waiting');
        }

        this.step++;
        this.renderCanvas();

        const stepCounter = document.getElementById('step-counter');
        if (stepCounter) stepCounter.textContent = `Langkah ${this.step}/${this.steps.length}`;
    }

    reset() {
        this.step = 0;

        if (this.logEl) this.logEl.innerHTML = '';

        document.querySelectorAll('.process-node').forEach(n => {
            n.classList.remove('waiting');
            n.style.opacity = '1';
        });

        this.renderCanvas();

        const stepCounter = document.getElementById('step-counter');
        if (stepCounter) stepCounter.textContent = `Langkah 0/${this.steps.length}`;

        this.log('Simulator direset. Klik "Langkah Berikutnya" untuk memulai.', 'info');
    }

    autoRun(delay = 1000) {
        const run = () => {
            if (this.step < this.steps.length) {
                this.nextStep();
                setTimeout(run, delay);
            }
        };
        run();
    }
}

window.deadlockSim = null;

window.initDeadlock = (containerId) => {
    window.deadlockSim = new DeadlockSimulator(containerId);
    window.deadlockSim.reset();
};
