<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Railway Control System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css'])
</head>

<body>
<div class="page">

    <!-- ══════════════════════════════
         NAVBAR
         ══════════════════════════════ -->
    <nav class="navbar">
        <div class="brand">
            <div>
                <div class="brand-name">JPL<span>69</span></div>
                <div class="brand-sub">Control &amp; Monitoring</div>
            </div>
        </div>
        <div id="clock">
            <div class="clock-box">
                <div class="clock-time">--:--:--</div>
            </div>
        </div>
        <div class="nav-actions">
            <button class="btn-ghost" onclick="window.location='/log'">LOG</button>
            <form method="POST" action="/logout" style="display:inline">
                @csrf
                <button type="submit" class="btn-logout">LOGOUT</button>
            </form>
        </div>
    </nav>

    <!-- ══════════════════════════════
         BODY GRID  (2 col × 2 row)
         ══════════════════════════════ -->
    <div class="body-grid">

        <!-- ══ SEL (1,1) — ATAS KIRI ══ -->
        <div class="col-left-top">

            <!-- 4 Stat Boxes -->
            <div class="top-grid">
                <div class="box">
                    <h4>Palang</h4>
                    <div id="status" class="big">···</div>
                </div>
                <div class="box">
                    <h4>Sirine</h4>
                    <div id="sirine" class="big">···</div>
                </div>
                <div class="box">
                    <h4>Status Sistem</h4>
                    <div id="system" class="big" style="color:var(--jade)">NORMAL</div>
                </div>
                <div class="box">
                    <h4>Sensor</h4>
                    <div id="sensorStatus" class="big">···</div>
                </div>
            </div>

            <!-- Kendali Palang -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <span class="card-dot"></span>
                        Kendali Palang
                    </div>
                </div>
                <div class="btn-grid">
                    <button class="ctrl-btn btn-open"  id="btnBuka"  onclick="cmdBuka()">BUKA</button>
                    <button class="ctrl-btn btn-close" id="btnTutup" onclick="cmdTutup()">TUTUP</button>
                </div>
            </div>

            <!-- Kendali Sensor -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <span class="card-dot"></span>
                        Kendali Sensor
                    </div>
                </div>
                <div class="btn-grid">
                    <button class="ctrl-btn btn-sensor-on"  id="btnSensorOn"  onclick="cmdSensorOn()">SENSOR ON</button>
                    <button class="ctrl-btn btn-sensor-off" id="btnSensorOff" onclick="cmdSensorOff()">SENSOR OFF</button>
                </div>
            </div>

        </div><!-- /col-left-top -->

        <!-- ══ SEL (1,2) — ATAS KANAN ══ -->
        <div class="col-right-top">
            <div class="right-top-row">

                <!-- Alert + Sensor stacked -->
                <div class="right-top-left">
                    <div class="card compact-card">
                        <div class="card-header">
                            <div class="card-title">
                                <span class="card-dot"></span>
                                Alert &amp; Gangguan
                            </div>
                        </div>
                        <div id="alertBox" class="alert-box">
                            <div class="no-alert">Sistem berjalan normal</div>
                        </div>
                    </div>

                    <div class="card compact-card">
                        <div class="card-header">
                            <div class="card-title">
                                <span class="card-dot"></span>
                                Status Deteksi Jalur
                            </div>
                        </div>
                        <div class="eta-display">
                            <div>
                                <div class="eta-value"><b id="eta">—</b></div>
                                <div class="eta-sub" id="sensorDetail">Estimasi berdasarkan sensor</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══ Palang Real-Time ══ -->
                <div class="right-top-right">
                    <div class="card palang-card">
                        <div class="card-header">
                            <div class="card-title">
                                <span class="card-dot"></span>
                                Palang Real-Time
                            </div>
                        </div>

                        <div class="palang-body">
                            <div class="palang-scene">

                                <!-- Rel / Track -->
                                <div style="
                                    position:absolute;
                                    bottom:0; left:0; right:0;
                                    height:6px;
                                    display:flex; gap:8px; align-items:center;
                                    opacity:.25;
                                ">
                                    <div style="flex:1;height:3px;background:var(--tx-lo);border-radius:2px;"></div>
                                    <div style="flex:1;height:3px;background:var(--tx-lo);border-radius:2px;"></div>
                                    <div style="flex:1;height:3px;background:var(--tx-lo);border-radius:2px;"></div>
                                    <div style="flex:1;height:3px;background:var(--tx-lo);border-radius:2px;"></div>
                                    <div style="flex:1;height:3px;background:var(--tx-lo);border-radius:2px;"></div>
                                </div>

                                <!-- Tiang -->
                                <div style="
                                    position:absolute;
                                    bottom:0; left:28px;
                                    width:12px; height:90px;
                                    background:linear-gradient(180deg,var(--bg-card2) 0%,var(--tx-lo) 100%);
                                    border-radius:4px 4px 2px 2px;
                                    border:1px solid var(--bd-2);
                                "></div>

                                <!-- Lampu sinyal -->
                                <div id="lamp" style="
                                    position:absolute;
                                    bottom:22px; left:26px;
                                    width:16px; height:16px;
                                    background:var(--rose);
                                    border-radius:50%;
                                    box-shadow:var(--rose-glow);
                                    border:2px solid rgba(255,255,255,0.15);
                                "></div>

                                <!-- Palang arm -->
                                <div id="palangArm" style="
                                    position:absolute;
                                    bottom:77px; left:34px;
                                    width:160px; height:6px;
                                    background:repeating-linear-gradient(
                                        90deg,
                                        var(--rose)   0,  var(--rose)   12px,
                                        var(--tx-hi) 12px, var(--tx-hi) 24px
                                    );
                                    transform-origin:left center;
                                    transform:rotate(0deg);
                                    transition: transform 0.72s cubic-bezier(0.34,1.2,0.64,1);
                                    border-radius:4px;
                                    opacity:.90;
                                "></div>

                            </div><!-- /palang-scene -->
                        </div><!-- /palang-body -->
                    </div><!-- /palang-card -->
                </div><!-- /right-top-right -->

            </div><!-- /right-top-row -->
        </div><!-- /col-right-top -->

        <!-- ══ SEL (2,1) — CHART bawah kiri ══ -->
        <div class="card chart-card">
            <div class="card-header">
                <div class="card-title">
                    <span class="card-dot"></span>
                    Riwayat Status Sensor
                </div>
                <span class="card-badge">auto refresh 1s</span>
            </div>

            <!-- Custom Legend -->
            <div class="chart-legend">
                <span class="legend-item">
                    <span class="legend-dot" style="background:#22c55e;"></span>
                    Sensor 1
                </span>
                <span class="legend-item">
                    <span class="legend-dot" style="background:#3b82f6;"></span>
                    Sensor 2
                </span>
            </div>

            <div class="chart-wrapper">
                <canvas id="chart"></canvas>
            </div>

            <div id="lastUpdate" style="
                padding: 0 14px 12px;
                font-size: .78rem;
                color: var(--tx-lo);
                opacity: .8;
                text-align:right;
            ">
                Update terakhir: --:--:--
            </div>
        </div>

        <!-- ══ SEL (2,2) — JADWAL bawah kanan ══ -->
        <div class="card jadwal-card">
            <div class="card-header jadwal-header">
                <div class="card-title">
                    <span class="card-dot"></span>
                    Jadwal Perjalanan Kereta
                </div>
            </div>
            <div class="jadwal-inner">
                <iframe
                    src="/jadwal_kereta.pdf#toolbar=0&navpanes=0&scrollbar=0&view=FitH"
                    title="Jadwal Kereta">
                </iframe>
            </div>
        </div>

    </div><!-- /body-grid -->
</div><!-- /page -->

<script>
/* ════════════════════════════════════
   CLOCK
   ════════════════════════════════════ */
const clockEl = document.querySelector('#clock .clock-time');
function updateClock() {
    const now = new Date();
    clockEl.textContent = now.toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        hour12: false
    });
}
updateClock();
setInterval(updateClock, 1000);

/* ════════════════════════════════════
   CHART DEFAULTS
   ════════════════════════════════════ */
Chart.defaults.color       = '#4d5c6e';
Chart.defaults.borderColor = 'rgba(136,153,187,.07)';

let chart      = null;
let lastSensor = null;
const MAX_POINTS = 20;

/* ════════════════════════════════════
   FLAG KONTROL MANUAL
   ════════════════════════════════════ */
let isManualCmd    = false;
let manualCmdTimer = null;

function setManualFlag() {
    isManualCmd = true;
    if (manualCmdTimer) clearTimeout(manualCmdTimer);
    manualCmdTimer = setTimeout(() => {
        isManualCmd = false;
    }, 2500);
}

/* ════════════════════════════════════
   FORMAT WAKTU
   ════════════════════════════════════ */
function formatWaktu(raw) {
    if (!raw) return '--:--:--';
    const s = String(raw).trim();
    let d;
    const hasTimezone = /Z$|[+-]\d{2}:\d{2}$/.test(s);
    if (hasTimezone) {
        d = new Date(s.replace(' ', 'T'));
    } else {
        d = new Date(s.replace(' ', 'T') + '+07:00');
    }
    if (isNaN(d.getTime())) {
        const parts = s.split(' ');
        return parts[1] ? parts[1].substring(0, 8) : s;
    }
    return d.toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        hour12: false
    });
}

/* ════════════════════════════════════
   LOAD STATUS
   ════════════════════════════════════ */
function load(skipChart = false) {
    fetch('/status')
        .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        })
        .then(d => {
            updatePalang(d.status_palang);
            updateSirine(d.sirine);
            updateSensor(d.sensor);
            updateAlert(d.gangguan);
            updateEta(d.status_palang, d.sensor1, d.sensor2, d.arah);

            document.getElementById('system').textContent = 'NORMAL';
            document.getElementById('system').style.color = 'var(--jade)';
        })
        .catch(err => {
            console.error('[load] Gagal:', err);
            document.getElementById('system').textContent = 'ERROR';
            document.getElementById('system').style.color = 'var(--rose)';
        });
}

/* ════════════════════════════════════
   UPDATE PALANG
   ════════════════════════════════════ */
function updatePalang(v) {
    const el   = document.getElementById('status');
    const arm  = document.getElementById('palangArm');
    const lamp = document.getElementById('lamp');

    el.textContent = v.toUpperCase();

    if (v === 'tutup') {
        el.style.color        = 'var(--rose)';
        arm.style.transform   = 'rotate(0deg)';
        lamp.style.background = 'var(--rose)';
        lamp.style.boxShadow  = 'var(--rose-glow)';
        lamp.style.animation  = 'blink 0.6s ease-in-out infinite';
    } else {
        el.style.color        = 'var(--jade)';
        arm.style.transform   = 'rotate(-90deg)';
        lamp.style.background = 'var(--jade)';
        lamp.style.boxShadow  = 'var(--jade-glow)';
        lamp.style.animation  = 'none';
    }
}

/* ════════════════════════════════════
   UPDATE SIRINE
   ════════════════════════════════════ */
function updateSirine(v) {
    const el = document.getElementById('sirine');
    el.textContent = (v ?? 'off').toUpperCase();
    el.style.color = v === 'on' ? 'var(--rose)' : 'var(--teal)';
}

/* ════════════════════════════════════
   UPDATE SENSOR
   ════════════════════════════════════ */
function updateSensor(v) {

    const el      = document.getElementById('sensorStatus');
    const btnSOn  = document.getElementById('btnSensorOn');
    const btnSOff = document.getElementById('btnSensorOff');

    // STATUS SENSOR
    el.textContent = v === 'on'
        ? 'ON'
        : 'OFF';

    // WARNA STATUS
    el.style.color = v === 'on'
        ? 'var(--jade)'
        : 'var(--rose)';

    // UPDATE BUTTON SENSOR
    if (v !== lastSensor) {

        lastSensor = v;

        // SENSOR ON BUTTON
        btnSOn.style.opacity =
            v === 'on' ? '1' : '0.38';

        // SENSOR OFF BUTTON
        btnSOff.style.opacity =
            v === 'off' ? '1' : '0.38';
    }
}

/* ════════════════════════════════════
   UPDATE ALERT
   ════════════════════════════════════ */
function updateAlert(gangguan) {

    document.getElementById('alertBox').innerHTML = gangguan
        ? `<div class="alert">${gangguan}</div>`
        : `<div class="no-alert">Sistem berjalan normal</div>`;
}

/* ════════════════════════════════════
   UPDATE ETA / SENSOR DETAIL
   ════════════════════════════════════ */
function updateEta(statusPalang, s1, s2, arah) {

    document.getElementById('eta').textContent =
        statusPalang === 'tutup'
            ? 'Kereta Terdeteksi'
            : 'Jalur Aman';

    if (arah === 'manual') {

        document.getElementById('sensorDetail').textContent =
            'Estimasi berdasarkan sensor';

        return;
    }

    const aktif = [
        s1 ? 'S1' : null,
        s2 ? 'S2' : null
    ].filter(Boolean);

    document.getElementById('sensorDetail').textContent =
        aktif.length
            ? 'Sensor aktif: ' + aktif.join(', ')
            : 'Estimasi berdasarkan sensor';
}

/* ════════════════════════════════════
   KENDALI MANUAL
   ════════════════════════════════════ */
function cmdBuka() {

    setManualFlag();

    fetch('/open')
        .then(r => r.json())
        .then(() => load(true))
        .catch(console.error);
}

function cmdTutup() {

    setManualFlag();

    fetch('/close?arah=manual')
        .then(r => r.json())
        .then(() => load(true))
        .catch(console.error);
}

/* ════════════════════════════════════
   SENSOR CONTROL
   ════════════════════════════════════ */
function cmdSensorOn() {

    setManualFlag();

    fetch('/sensor?status=on')
        .then(r => r.json())
        .then(() => {

            updateSensor('on');

            load(true);
        })
        .catch(console.error);
}

function cmdSensorOff() {

    setManualFlag();

    fetch('/sensor?status=off')
        .then(r => r.json())
        .then(() => {

            updateSensor('off');

            load(true);
        })
        .catch(console.error);
}

/* ════════════════════════════════════
   INIT CHART — dibuat SEKALI
   ════════════════════════════════════ */
function initChart() {
    const ctx = document.getElementById('chart').getContext('2d');
    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Sensor 1 Aktif',
                    data: [],
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34,197,94,0.08)',
                    borderWidth: 3,
                    tension: 0.2,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#111418',
                    pointBorderWidth: 1.5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#22c55e',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                },
                {
                    label: 'Sensor 2 Aktif',
                    data: [],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 3,
                    tension: 0.2,
                    fill: true,
                    pointRadius: 4,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#111418',
                    pointBorderWidth: 1.5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#3b82f6',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 400 },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(15,23,42,0.85)',
                    titleColor: '#94a3b8',
                    bodyColor: '#e2e8f0',
                    borderColor: 'rgba(255,255,255,0.08)',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: ctx =>
                            ` ${ctx.dataset.label}: ${ctx.parsed.y === 1 ? 'AKTIF' : 'IDLE'}`
                    }
                }
            },
            scales: {
                y: {
                    min: 0, max: 1,
                    grid: { color: 'rgba(255,255,255,0.06)', lineWidth: 0.5 },
                    border: { dash: [4, 4] },
                    ticks: {
                        stepSize: 1,
                        color: '#64748b',
                        font: { size: 11 },
                        callback: v => v === 1 ? 'AKTIF' : 'IDLE',
                    }
                },
                x: {
                    grid: { color: 'rgba(255,255,255,0.04)', lineWidth: 0.5 },
                    ticks: {
                        color: '#64748b',
                        font: { size: 11 },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 8,
                    }
                }
            }
        }
    });
}

/* ════════════════════════════════════
   LOAD CHART
   ════════════════════════════════════ */
function loadChart() {
    fetch('/history')
        .then(r => r.json())
        .then(rawData => {
            const d      = rawData.slice(-MAX_POINTS);
            const labels = d.map(x => formatWaktu(x.waktu));
            document.getElementById('lastUpdate').textContent =
                'Update terakhir: ' +
                (labels.length ? labels[labels.length - 1] : '--:--:--');
            const s1 = d.map(x => parseInt(x.sensor1) || 0);
            const s2 = d.map(x => parseInt(x.sensor2) || 0);

            if (!chart) {
                initChart();
            }
            chart.data.labels           = labels;
            chart.data.datasets[0].data = s1;
            chart.data.datasets[1].data = s2;
            chart.update('none');
        })
        .catch(e => console.error('[chart]', e));
}

/* ════════════════════════════════════
   AUTO REFRESH — setiap 1 detik
   ════════════════════════════════════ */
setInterval(() => load(true), 200);

setInterval(() => {
    if (!isManualCmd) {
        loadChart();
    }
}, 1000);

load();
loadChart();
</script>
</body>
</html>