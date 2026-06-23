<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Log Aktivitas Kereta</title>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=IBM+Plex+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
:root {
    --bg:         #08090d;
    --surface:    #0e1117;
    --panel:      #12161f;
    --border:     #1f2535;
    --border-hi:  #2a3448;
    --text:       #c9d4e8;
    --text-dim:   #4a5878;
    --text-muted: #6b7fa0;
    --tutup:      #e05252;
    --tutup-bg:   rgba(224,82,82,0.08);
    --tutup-bd:   rgba(224,82,82,0.2);
    --buka:       #3eb87a;
    --buka-bg:    rgba(62,184,122,0.08);
    --buka-bd:    rgba(62,184,122,0.2);
    --accent:     #4f8ef7;
    --mono:       'IBM Plex Mono', monospace;
    --sans:       'IBM Plex Sans', sans-serif;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html { scroll-behavior: smooth; }

body {
    font-family: var(--sans);
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    font-size: 14px;
    line-height: 1.5;
}

/* ── TOPBAR ───────────────────────────── */
.topbar {
    position: sticky;
    top: 0;
    z-index: 50;
    height: 52px;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
}

.topbar-brand {
    font-family: var(--mono);
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.12em;
    color: var(--text);
    text-transform: uppercase;
}

.topbar-brand span {
    color: var(--text-dim);
    font-weight: 400;
    margin-right: 10px;
}

.topbar-actions {
    display: flex;
    gap: 8px;
}

.btn {
    font-family: var(--sans);
    font-size: 12px;
    font-weight: 500;
    padding: 6px 14px;
    border-radius: 4px;
    border: 1px solid var(--border-hi);
    background: var(--panel);
    color: var(--text-muted);
    cursor: pointer;
    letter-spacing: 0.03em;
    transition: border-color 0.15s, color 0.15s, background 0.15s;
}

.btn:hover {
    border-color: var(--accent);
    color: var(--text);
    background: rgba(79,142,247,0.06);
}

/* ── LAYOUT ───────────────────────────── */
.wrap {
    max-width: 960px;
    margin: 0 auto;
    padding: 36px 24px 60px;
}

/* ── PAGE HEAD ────────────────────────── */
.page-head {
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.page-title {
    font-family: var(--sans);
    font-size: 22px;
    font-weight: 600;
    color: var(--text);
    letter-spacing: -0.01em;
}

.page-meta {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--text-dim);
    letter-spacing: 0.06em;
    margin-top: 4px;
    text-transform: uppercase;
}

.live-tag {
    font-family: var(--mono);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.1em;
    color: var(--buka);
    border: 1px solid var(--buka-bd);
    background: var(--buka-bg);
    padding: 4px 10px;
    border-radius: 3px;
    text-transform: uppercase;
    position: relative;
    display: flex;
    align-items: center;
    gap: 7px;
}

.live-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--buka);
    animation: blink 1.6s ease infinite;
    flex-shrink: 0;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.2; }
}

/* ── STATS ────────────────────────────── */
.stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

.stat {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 16px 20px;
}

.stat-label {
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.08em;
    color: var(--text-dim);
    text-transform: uppercase;
    margin-bottom: 8px;
}

.stat-val {
    font-family: var(--mono);
    font-size: 28px;
    font-weight: 600;
    color: var(--text);
    line-height: 1;
}

.stat-val.red  { color: var(--tutup); }
.stat-val.green{ color: var(--buka);  }

/* ── TABLE CARD ───────────────────────── */
.card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 6px;
    overflow: hidden;
}

.card-head {
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.card-label {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--text-dim);
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.refresh-info {
    font-family: var(--mono);
    font-size: 11px;
    color: var(--text-dim);
    letter-spacing: 0.04em;
}

/* ── TABLE ────────────────────────────── */
.tbl-wrap {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

col.col-no     { width: 70px; }
col.col-status { width: 160px; }
col.col-s1     { width: 100px; }
col.col-s2     { width: 100px; }
col.col-waktu  { width: auto; }

thead th {
    padding: 11px 20px;
    text-align: left;
    font-family: var(--mono);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-dim);
    background: var(--panel);
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}

tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.1s;
}
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: rgba(79,142,247,0.04); }

td {
    padding: 11px 20px;
    font-size: 13px;
    vertical-align: middle;
}

/* No column */
.c-no {
    font-family: var(--mono);
    font-size: 12px;
    color: var(--text-dim);
}

/* Status badge */
.badge {
    display: inline-block;
    font-family: var(--mono);
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 4px 10px;
    border-radius: 3px;
    border: 1px solid transparent;
}
.badge-tutup {
    color: var(--tutup);
    background: var(--tutup-bg);
    border-color: var(--tutup-bd);
}
.badge-buka {
    color: var(--buka);
    background: var(--buka-bg);
    border-color: var(--buka-bd);
}

/* Sensor columns */
.c-sensor {
    font-family: var(--mono);
    font-size: 12px;
    text-align: center;
    display: block;
}
.s-on  { color: var(--buka); }
.s-off { color: var(--text-dim); }

/* Waktu */
.c-waktu {
    font-family: var(--mono);
    font-size: 12px;
    color: var(--text-muted);
    white-space: nowrap;
}

/* Empty / error */
.msg-state {
    text-align: center;
    padding: 52px 20px;
}
.msg-state p {
    font-family: var(--mono);
    font-size: 12px;
    color: var(--text-dim);
    letter-spacing: 0.06em;
}

/* Scrollbar */
::-webkit-scrollbar { width: 5px; height: 5px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border-hi); border-radius: 3px; }
</style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="topbar-brand">
        <span></span>LOG AKTIVITAS PALANG KERETA
    </div>
    <div class="topbar-actions">
        <button class="btn" onclick="window.location='/dashboard'">Dashboard</button>
        <button class="btn" onclick="window.location='/export-log'">Export Excel</button>
    </div>
</div>

<!-- MAIN -->
<div class="wrap">

    <!-- HEAD -->
    <div class="page-head">
        <div>
            <div class="page-title">Riwayat Aktivitas</div>
            <div class="page-meta">Tabel history — 20 data terakhir</div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="card">
        <div class="card-head">
            <span class="card-label">history</span>
            <span class="refresh-info" id="lastRefresh">memuat...</span>
        </div>
        <div class="tbl-wrap">
            <table>
                <colgroup>
                    <col class="col-no">
                    <col class="col-status">
                    <col class="col-s1">
                    <col class="col-s2">
                    <col class="col-waktu">
                </colgroup>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Status Palang</th>
                        <th style="text-align:center">Sensor 1</th>
                        <th style="text-align:center">Sensor 2</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="5">
                            <div class="msg-state"><p>memuat data...</p></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function statusBadge(val) {
    const v = (val || '').toLowerCase().trim();
    if (v === 'tutup' || v === 'close' || v === 'closed') {
        return `<span class="badge badge-tutup">tutup</span>`;
    }
    if (v === 'buka' || v === 'open') {
        return `<span class="badge badge-buka">buka</span>`;
    }
    return `<span class="badge" style="color:var(--text-muted);border-color:var(--border)">${val || '-'}</span>`;
}

function sensorCell(val) {
    const on = val === 1 || val === true || val === '1';
    return `<span class="c-sensor ${on ? 's-on' : 's-off'}">${on ? 'ON' : 'OFF'}</span>`;
}

function fmtWaktu(w) {
    if (!w) return '-';
    const d = new Date(w);
    if (isNaN(d)) return w;
    const pad = n => String(n).padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}  ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
}

function loadLog() {
    fetch('/history')
        .then(r => { if (!r.ok) throw new Error(r.status); return r.json(); })
        .then(data => {
            const body = document.getElementById('tableBody');

            if (!Array.isArray(data) || data.length === 0) {
                body.innerHTML = `<tr><td colspan="5"><div class="msg-state"><p>tidak ada data</p></div></td></tr>`;
                return;
            }

            let html = '';

            data.forEach((row, i) => {

                html += `<tr>
                    <td class="c-no">${i + 1}</td>
                    <td>${statusBadge(row.status_palang)}</td>
                    <td style="text-align:center">${sensorCell(row.sensor1)}</td>
                    <td style="text-align:center">${sensorCell(row.sensor2)}</td>
                    <td class="c-waktu">${fmtWaktu(row.waktu)}</td>
                </tr>`;
            });

            body.innerHTML = html;



            const now = new Date();
            const pad = n => String(n).padStart(2,'0');
            document.getElementById('lastRefresh').textContent =
                `diperbarui ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
        })
        .catch(err => {
            document.getElementById('tableBody').innerHTML =
                `<tr><td colspan="5"><div class="msg-state"><p>gagal memuat data — ${err.message}</p></div></td></tr>`;
            document.getElementById('lastRefresh').textContent = 'gagal';
        });
}

loadLog();
setInterval(loadLog, 3000);
</script>
</body>
</html>