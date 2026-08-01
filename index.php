<?php
session_start();
define('ADMIN_PASSWORD', 'micn2026'); // ← Change this password

if (isset($_POST['action']) && $_POST['action'] === 'logout') {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
if (isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['micn_auth'] = true;
    } else {
        $loginError = true;
    }
}

if (empty($_SESSION['micn_auth'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MICN Calendar — Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body {
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #0d1b2a 0%, #1b3a4b 100%);
            font-family: 'Inter', sans-serif;
        }
        .card {
            background: #fff; border-radius: 18px; padding: 40px 36px;
            width: 100%; max-width: 360px;
            box-shadow: 0 28px 70px rgba(0,0,0,.35);
        }
        .logo {
            width: 52px; height: 52px; background: linear-gradient(135deg,#dc2626,#b91c1c); border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(220,38,38,.35);
            margin: 0 auto 16px;
        }
        h1 { text-align:center; font-size: 1.1rem; font-weight: 700; color: #1a2535; margin-bottom: 4px; }
        p  { text-align:center; font-size: .84rem; color: #64748b; margin-bottom: 28px; }
        label { display:block; font-size:.76rem; font-weight:700; text-transform:uppercase;
                letter-spacing:.6px; color:#64748b; margin-bottom:6px; }
        input[type=password] {
            width:100%; padding:10px 13px; border:1.5px solid #dde3ec; border-radius:8px;
            font-family:'Inter',sans-serif; font-size:.9rem; color:#1a2535;
            background:#f8fafc; transition:border-color .14s, box-shadow .14s;
        }
        input[type=password]:focus { outline:none; border-color:#4cc9f0; box-shadow:0 0 0 3px rgba(76,201,240,.15); background:#fff; }
        .err { font-size:.75rem; color:#b91c1c; margin-top:6px; }
        button {
            width:100%; margin-top:18px; padding:11px; border:none; border-radius:8px;
            background:linear-gradient(135deg,#1b3a4b,#274c5b); color:#fff;
            font-family:'Inter',sans-serif; font-size:.9rem; font-weight:600;
            cursor:pointer; transition:filter .14s;
        }
        button:hover { filter:brightness(1.12); }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <svg viewBox="0 0 24 18" width="30" height="22" fill="none">
            <!-- Ambulance body -->
            <rect x="0" y="2" width="16" height="13" rx="2" fill="white"/>
            <!-- Cab -->
            <path d="M16 7 L16 15 L23 15 L23 10.5 L20.5 7 Z" fill="white"/>
            <!-- Windshield -->
            <path d="M17 7.5 L17 10 L22 10 L20.5 7.5 Z" fill="#fca5a5"/>
            <!-- Medical cross on body -->
            <rect x="5.5" y="6" width="2" height="6" rx=".6" fill="#dc2626"/>
            <rect x="3.5" y="8" width="6" height="2" rx=".6" fill="#dc2626"/>
            <!-- Wheels -->
            <circle cx="5" cy="15.2" r="2.2" fill="#dc2626"/>
            <circle cx="5" cy="15.2" r="1" fill="white"/>
            <circle cx="18.5" cy="15.2" r="2.2" fill="#dc2626"/>
            <circle cx="18.5" cy="15.2" r="1" fill="white"/>
            <!-- Red stripe -->
            <rect x="0" y="9" width="16" height="2" fill="#fca5a5" opacity=".4"/>
        </svg>
    </div>
    <h1>MICN Calendar</h1>
    <p>Admin access — enter your password</p>
    <form method="POST">
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" autofocus placeholder="Enter password…">
        <?php if (!empty($loginError)): ?>
            <div class="err">Incorrect password. Try again.</div>
        <?php endif; ?>
        <button type="submit">Sign In</button>
    </form>
</div>
</body>
</html>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>MICN Calendar</title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <style>
        :root {
            --bg: #eef1f6; --surface: #ffffff; --border: #dde3ec;
            --text: #1a2535; --text-muted: #64748b;
            --day-bg:#0ea5e9; --day-bd:#0284c7;
            --noc-bg:#1e3a5f; --noc-bd:#172d4a;
            --8hr-bg:#0d9488; --8hr-bd:#0b7a6e;
            --4hr-bg:#38bdf8; --4hr-bd:#0ea5e9;
            --cust-bg:#6366f1; --cust-bd:#4f46e5;
            --s-sched:#f59e0b; --s-conf:#10b981; --s-canc:#ef4444; --s-comp:#94a3b8;
            --open-text: rgba(185,28,28,0.7);
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html, body { height:100%; overflow:hidden; font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); -webkit-tap-highlight-color:transparent; }

        /* TOP BAR */
        .topbar { height:54px; background:linear-gradient(135deg,#0d1b2a 0%,#1b3a4b 100%); color:#fff; display:flex; align-items:center; padding:0 14px; gap:10px; box-shadow:0 2px 12px rgba(0,0,0,.22); position:relative; z-index:200; flex-shrink:0; }
        .bar-brand { display:flex; align-items:center; gap:10px; flex:1; min-width:0; }
        .bar-logo { width:36px; height:36px; flex-shrink:0; background:linear-gradient(135deg,#dc2626,#b91c1c); border-radius:9px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 8px rgba(220,38,38,.4); }
        .bar-title { font-size:1.06rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .bar-title .hi { color:#4cc9f0; }
        .bar-sub { font-size:.67rem; opacity:.5; letter-spacing:.8px; text-transform:uppercase; }
        .bar-date { font-size:.78rem; opacity:.55; white-space:nowrap; flex-shrink:0; display:none; }
        @media (min-width:640px) { .bar-date { display:block; } }
        .bar-btn { flex-shrink:0; background:rgba(255,255,255,0.1); border:none; border-radius:7px; width:36px; height:36px; cursor:pointer; color:#fff; display:flex; align-items:center; justify-content:center; text-decoration:none; transition:background .15s; }
        .bar-btn:hover { background:rgba(255,255,255,0.18); }

        /* LAYOUT */
        .layout { display:flex; height:calc(100vh - 54px); overflow:hidden; }
        .cal-wrap { flex:1; min-width:0; padding:14px; overflow:hidden; }
        @media (max-width:640px) { .cal-wrap { padding:8px; } }
        #calendar { height:100%; }

        /* FULLCALENDAR */
        .fc { font-family:'Inter',sans-serif !important; }
        .fc .fc-toolbar { margin-bottom:10px !important; flex-wrap:wrap; gap:6px; }
        .fc .fc-toolbar-title { font-size:1.14rem !important; font-weight:600 !important; color:var(--text) !important; }
        .fc .fc-button { font-family:'Inter',sans-serif !important; font-size:.84rem !important; font-weight:500 !important; padding:6px 11px !important; border-radius:6px !important; border:none !important; transition:background .14s !important; min-height:34px; }
        .fc .fc-button-primary { background:#1b3a4b !important; border-color:#1b3a4b !important; }
        .fc .fc-button-primary:hover { background:#274c5b !important; }
        .fc .fc-button-primary:not(:disabled).fc-button-active { background:#0d1b2a !important; box-shadow:inset 0 2px 4px rgba(0,0,0,.25) !important; }
        .fc .fc-button-primary:focus { box-shadow:0 0 0 3px rgba(76,201,240,.3) !important; }
        .fc .fc-col-header-cell { background:var(--surface) !important; border-bottom:2px solid var(--border) !important; }
        .fc .fc-col-header-cell-cushion { padding:7px 4px !important; font-size:.82rem !important; font-weight:600 !important; color:var(--text-muted) !important; text-decoration:none !important; }
        .fc .fc-col-header-cell.fc-day-today .fc-col-header-cell-cushion { color:#0ea5e9 !important; }
        .fc .fc-timegrid-slot-label { font-size:.69rem !important; color:#94a3b8 !important; }
        @media (min-width:768px) {
            .fc .fc-toolbar-title { font-size:.98rem !important; }
            .fc .fc-button { font-size:.78rem !important; padding:4px 9px !important; min-height:28px !important; }
            .ev-name { font-size:.78rem !important; }
            .ev-badge { font-size:.61rem !important; }
        }
        .fc .fc-day-today { background:rgba(76,201,240,.04) !important; }
        .fc .fc-highlight { background:rgba(76,201,240,.12) !important; }
        .fc .fc-scrollgrid { border-radius:10px !important; overflow:hidden !important; border-color:var(--border) !important; box-shadow:0 1px 6px rgba(0,0,0,.07) !important; }
        .fc td, .fc th { border-color:var(--border) !important; }
        .fc .fc-timegrid-now-indicator-line { border-color:#ef4444 !important; border-width:2px !important; }
        .fc .fc-timegrid-now-indicator-arrow { border-top-color:#ef4444 !important; }
        .fc .fc-daygrid-event, .fc .fc-timegrid-event { border-radius:6px !important; border:none !important; cursor:grab !important; transition:opacity .1s,filter .1s,transform .1s !important; overflow:hidden; }
        .fc .fc-daygrid-event:hover, .fc .fc-timegrid-event:hover { filter:brightness(1.07); transform:scale(1.015); box-shadow:0 4px 14px rgba(0,0,0,.16) !important; z-index:10 !important; }

        /* OPEN block (time grid) */
        .fc-event.open-shift-block { border-radius:6px !important; cursor:pointer !important; box-shadow:none !important; }
        .fc-event.open-shift-block:hover { filter:brightness(1.04); }

        /* Day summary (month view) */
        .fc-event.day-summary-event { background:transparent !important; border:none !important; box-shadow:none !important; cursor:pointer !important; padding:0 !important; }
        .fc-event.day-summary-event:hover { filter:none !important; transform:none !important; }
        .cov-bar { display:flex; gap:2px; padding:2px 3px 1px; }
        .cov-seg { flex:1; height:7px; border-radius:2px; }
        @media (max-width:640px) { .cov-seg { height:5px; } .cov-name { font-size:.64rem; } }
        .cov-name { font-size:.72rem; font-weight:500; color:var(--text-muted); padding:0 4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.5; }

        /* Event card */
        .ev-card { padding:2px 6px; height:100%; display:flex; flex-direction:row; align-items:center; overflow:hidden; min-height:0; gap:4px; }
        .ev-dot { width:6px; height:6px; border-radius:50%; flex-shrink:0; }
        .ev-name { font-size:.87rem; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.3; flex:1; }
        .ev-name.struck { text-decoration:line-through; opacity:.65; }
        .ev-badge { font-size:.66rem; font-weight:700; padding:1px 5px; border-radius:8px; background:rgba(255,255,255,.22); color:inherit; letter-spacing:.3px; text-transform:uppercase; white-space:nowrap; flex-shrink:0; }
        .ev-open { width:100%; height:100%; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px; }
        .ev-open-lbl { font-size:.75rem; font-weight:800; letter-spacing:1.4px; text-transform:uppercase; color:var(--open-text); }
        .ev-open-plus { font-size:.9rem; line-height:1; color:rgba(185,28,28,.38); }

        /* FAB */
        .fab { display:none; position:fixed; bottom:22px; right:18px; width:58px; height:58px; background:linear-gradient(135deg,#4cc9f0,#0ea5e9); color:#fff; border:none; border-radius:50%; font-size:1.7rem; line-height:1; cursor:pointer; box-shadow:0 4px 18px rgba(76,201,240,.5); z-index:400; align-items:center; justify-content:center; transition:transform .15s,box-shadow .15s; }
        .fab:hover { transform:scale(1.09); box-shadow:0 6px 24px rgba(76,201,240,.6); }
        .fab:active { transform:scale(.93); }
        @media (max-width:767px) { .fab { display:flex; } }

        /* MODAL */
        .overlay { display:none; position:fixed; inset:0; background:rgba(10,20,32,.55); backdrop-filter:blur(5px); -webkit-backdrop-filter:blur(5px); z-index:1000; align-items:center; justify-content:center; padding:16px; }
        .overlay.on { display:flex; }
        .modal { background:var(--surface); border-radius:18px; width:100%; max-width:520px; overflow:hidden; box-shadow:0 28px 70px rgba(0,0,0,.3),0 4px 18px rgba(0,0,0,.12); animation:popIn .2s ease; }
        @keyframes popIn { from { opacity:0; transform:translateY(18px) scale(.96); } to { opacity:1; transform:translateY(0) scale(1); } }
        .modal-hd { background:linear-gradient(135deg,#0d1b2a 0%,#1b3a4b 100%); color:#fff; padding:18px 22px 14px; display:flex; align-items:center; justify-content:space-between; }
        .modal-hd h2 { font-size:1.06rem; font-weight:600; }
        .modal-x { background:rgba(255,255,255,.12); border:none; color:#fff; width:27px; height:27px; border-radius:50%; cursor:pointer; font-size:1rem; display:flex; align-items:center; justify-content:center; transition:background .14s; }
        .modal-x:hover { background:rgba(255,255,255,.22); }
        .modal-bd { padding:18px 22px; }
        .fg { margin-bottom:13px; }
        .fg label { display:block; font-size:.76rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:var(--text-muted); margin-bottom:5px; }
        .fg input, .fg select, .fg textarea { width:100%; padding:9px 11px; border:1.5px solid var(--border); border-radius:8px; font-family:'Inter',sans-serif; font-size:.94rem; color:var(--text); background:#f8fafc; transition:border-color .14s,box-shadow .14s; appearance:auto; }
        .fg input:focus, .fg select:focus, .fg textarea:focus { outline:none; border-color:#4cc9f0; box-shadow:0 0 0 3px rgba(76,201,240,.15); background:#fff; }
        .fg textarea { resize:vertical; }
        .form-row { display:flex; gap:10px; }
        .form-row .fg { flex:1; min-width:0; }
        .modal-ft { padding:14px 22px; border-top:1px solid var(--border); display:flex; gap:8px; align-items:center; }
        .btn { padding:9px 16px; border:none; border-radius:8px; font-family:'Inter',sans-serif; font-size:.88rem; font-weight:500; cursor:pointer; transition:all .14s; display:flex; align-items:center; gap:5px; white-space:nowrap; }
        .btn-save { background:linear-gradient(135deg,#1b3a4b,#274c5b); color:#fff; flex:1; }
        .btn-save:hover { filter:brightness(1.12); transform:translateY(-1px); }
        .btn-del { background:#fff5f5; color:#b91c1c; border:1.5px solid rgba(239,68,68,.22); }
        .btn-del:hover { background:#fee2e2; }
        .btn-sec { background:#f1f5f9; color:var(--text-muted); }
        .btn-sec:hover { background:#e2e8f0; }

        /* TOAST */
        .toast { position:fixed; bottom:88px; left:50%; transform:translateX(-50%) translateY(14px); background:#1b3a4b; color:#fff; padding:9px 20px; border-radius:24px; font-size:.82rem; font-weight:500; box-shadow:0 4px 18px rgba(0,0,0,.22); opacity:0; transition:opacity .2s,transform .2s; z-index:2000; pointer-events:none; white-space:nowrap; }
        .toast.on { opacity:1; transform:translateX(-50%) translateY(0); }
    </style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="bar-brand">
        <div class="bar-logo">
            <svg viewBox="0 0 24 18" width="22" height="16" fill="none">
                <rect x="0" y="2" width="16" height="13" rx="2" fill="white"/>
                <path d="M16 7 L16 15 L23 15 L23 10.5 L20.5 7 Z" fill="white"/>
                <path d="M17 7.5 L17 10 L22 10 L20.5 7.5 Z" fill="#fca5a5"/>
                <rect x="5.5" y="6" width="2" height="6" rx=".6" fill="#dc2626"/>
                <rect x="3.5" y="8" width="6" height="2" rx=".6" fill="#dc2626"/>
                <circle cx="5" cy="15.2" r="2.2" fill="#dc2626"/>
                <circle cx="5" cy="15.2" r="1" fill="white"/>
                <circle cx="18.5" cy="15.2" r="2.2" fill="#dc2626"/>
                <circle cx="18.5" cy="15.2" r="1" fill="white"/>
            </svg>
        </div>
        <div>
            <div class="bar-title">MICN <span class="hi">Calendar</span></div>
            <div class="bar-sub">Mobile Intensive Care Nurse</div>
        </div>
    </div>
    <div class="bar-date" id="barDate"></div>

    <!-- View-only link -->
    <a href="readonly.html" target="_blank" title="Open read-only view" class="bar-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
            <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
        </svg>
    </a>

    <!-- Logout -->
    <form method="POST" style="margin:0">
        <input type="hidden" name="action" value="logout">
        <button type="submit" class="bar-btn" title="Sign out">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </button>
    </form>
</div>

<!-- LAYOUT -->
<div class="layout">
    <div class="cal-wrap">
        <div id="calendar"></div>
    </div>
</div>

<!-- FAB -->
<button class="fab" id="fabBtn" title="Add shift" aria-label="Add shift">+</button>

<!-- TOAST -->
<div class="toast" id="toast" role="status" aria-live="polite"></div>

<!-- MODAL -->
<div class="overlay" id="editModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal">
        <div class="modal-hd">
            <h2 id="modalTitle">Add Shift</h2>
            <button class="modal-x" onclick="closeModal()" aria-label="Close">×</button>
        </div>
        <div class="modal-bd">
            <input type="hidden" id="shiftId">
            <div class="fg">
                <label>Nurse Name</label>
                <input type="text" id="nurseName" placeholder="Enter nurse name…" autocomplete="name">
            </div>
            <div class="form-row">
                <div class="fg"><label>Start</label><input type="datetime-local" id="shiftStart"></div>
                <div class="fg"><label>End</label><input type="datetime-local" id="shiftEnd"></div>
            </div>
            <div class="form-row">
                <div class="fg">
                    <label>Status</label>
                    <select id="shiftStatus">
                        <option value="scheduled">🟡 Scheduled</option>
                        <option value="confirmed">🟢 Confirmed</option>
                        <option value="completed">⚫ Completed</option>
                        <option value="cancelled">🔴 Cancelled</option>
                    </select>
                </div>
                <div class="fg">
                    <label>Shift Type</label>
                    <select id="shiftTypeSelect">
                        <option value="auto">Auto-detect</option>
                        <option value="12hr-day">12H Day</option>
                        <option value="12hr-noc">12H Night</option>
                        <option value="8hr">8-Hour</option>
                        <option value="4hr">4-Hour</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
            </div>
            <div class="fg" style="display:flex;align-items:center;gap:9px;margin-bottom:13px">
                <input type="checkbox" id="isPeds" style="width:16px;height:16px;cursor:pointer;accent-color:#0ea5e9">
                <label for="isPeds" style="font-size:.82rem;font-weight:500;text-transform:none;letter-spacing:0;color:var(--text);cursor:pointer;margin:0">
                    Peds <span style="font-size:.72rem;color:var(--text-muted);font-weight:400">(Pediatrics department)</span>
                </label>
            </div>
            <div class="fg">
                <label>Notes <span style="font-weight:400;opacity:.55;">(optional)</span></label>
                <textarea id="shiftNotes" rows="2" placeholder="Any additional notes…"></textarea>
            </div>
        </div>
        <div class="modal-ft">
            <button class="btn btn-del" id="deleteBtn" onclick="deleteShift()">🗑 Delete</button>
            <button class="btn btn-sec" onclick="closeModal()">Cancel</button>
            <button class="btn btn-save" onclick="saveShift()">✓ Save Shift</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const $ = id => document.getElementById(id);
    const pad = n => String(n).padStart(2, '0');

    function toLocalISO(d) {
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    }
    function toMySQLLocal(d) {
        return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:00`;
    }

    const isMobile = () => window.innerWidth < 768;

    $('barDate').textContent = new Date().toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric', year:'numeric' });

    function defaultShiftTimes(baseDate) {
        const h = new Date().getHours();
        const base = baseDate || new Date();
        const s = new Date(base.getFullYear(), base.getMonth(), base.getDate());
        const e = new Date(s);
        if (h >= 7 && h < 19) { s.setHours(7,0,0,0); e.setHours(19,0,0,0); }
        else { s.setHours(19,0,0,0); e.setDate(e.getDate()+1); e.setHours(7,0,0,0); }
        return { start: s, end: e };
    }

    $('fabBtn').addEventListener('click', () => {
        const { start, end } = defaultShiftTimes();
        openCreateModal(start, end);
    });

    let _toastTimer;
    function showToast(msg) {
        const t = $('toast'); t.textContent = msg; t.classList.add('on');
        clearTimeout(_toastTimer); _toastTimer = setTimeout(() => t.classList.remove('on'), 2600);
    }
    window.showToast = showToast;

    const COLORS = {
        '12hr-day': { bg:'#0ea5e9', bd:'#0284c7', tx:'#fff' },
        '12hr-noc': { bg:'#1e3a5f', bd:'#172d4a', tx:'#bfdbfe' },
        '8hr':      { bg:'#0d9488', bd:'#0b7a6e', tx:'#fff' },
        '4hr':      { bg:'#38bdf8', bd:'#0ea5e9', tx:'#fff' },
        'custom':   { bg:'#6366f1', bd:'#4f46e5', tx:'#fff' },
    };
    const fallback = COLORS['custom'];
    const STATUS_DOT = { scheduled:'#f59e0b', confirmed:'#10b981', cancelled:'#ef4444', completed:'#94a3b8' };
    const TYPE_LBL   = { '12hr-day':'12H Day', '12hr-noc':'12H Night', '8hr':'8H', '4hr':'4H', 'custom':'Custom' };

    const cal = new FullCalendar.Calendar($('calendar'), {
        initialView: isMobile() ? 'timeGridDay' : 'dayGridMonth',
        headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,timeGridDay' },
        slotMinTime: '07:00:00', slotMaxTime: '31:00:00', scrollTime: '07:00:00',
        slotDuration: '01:00:00', expandRows: true, nowIndicator: true,
        lazyFetching: false,
        editable: true, droppable: true, selectable: true, selectMirror: true,
        dayMaxEvents: true, views: { dayGridMonth: { dayMaxEvents: false } },
        weekends: true, longPressDelay: 300, eventLongPressDelay: 300, selectLongPressDelay: 300, unselectAuto: false,

        events: function (info, ok, fail) {
            fetch(`api.php?action=list&start=${info.startStr}&end=${info.endStr}`)
                .then(r => r.json())
                .then(data => {
                    const rangeDays = (info.end - info.start) / 86400000;
                    const isMonth   = rangeDays > 14;
                    const out = [];
                    const fixDT = s => s ? s.replace(' ', 'T') : s;

                    if (isMonth) {
                        const BLOCK_HOURS = [3, 7, 11, 15, 19, 23];
                        const dayData = {};
                        data.forEach(ev => {
                            const p = ev.extendedProps || {};
                            const start = fixDT(ev.start), end = fixDT(ev.end);
                            const day = start.substring(0, 10);
                            if (!dayData[day]) dayData[day] = { openSet: new Set(), nurses: [] };
                            if (p.is_open) {
                                dayData[day].openSet.add(parseInt(start.substring(11, 13)));
                            } else if (p.nurse_name) {
                                const sh = start.substring(11, 16).replace(':00','');
                                const eh = end ? end.substring(11, 16).replace(':00','') : '';
                                const key = p.nurse_name + sh;
                                if (!dayData[day].nurses.find(n => n.key === key))
                                    dayData[day].nurses.push({ key, name: p.nurse_name, start: sh, end: eh,
                                        id: ev.id, startFull: start, endFull: end,
                                        shift_type: p.shift_type || 'custom', status: p.status || 'scheduled',
                                        notes: p.notes || '', is_peds: !!p.is_peds });
                            }
                        });
                        Object.entries(dayData).forEach(([day, d]) => {
                            out.push({ id: 'summary-' + day, title: '', start: day, allDay: true,
                                backgroundColor: 'transparent', borderColor: 'transparent',
                                classNames: ['day-summary-event'], editable: false,
                                extendedProps: { is_day_summary: true,
                                    openBlocks: BLOCK_HOURS.map(h => d.openSet.has(h)), nurses: d.nurses } });
                        });
                    } else {
                        data.forEach(ev => {
                            const p = ev.extendedProps || {};
                            const start = fixDT(ev.start), end = fixDT(ev.end);
                            if (p.is_open) {
                                out.push({ ...ev, start, end, backgroundColor:'rgba(255,242,242,0.85)', borderColor:'transparent', textColor:'rgba(185,28,28,0.7)', classNames:['open-shift-block'], editable:false });
                            } else {
                                const c = COLORS[p.shift_type] || fallback;
                                out.push({ ...ev, start, end, backgroundColor:c.bg, borderColor:c.bd, textColor:c.tx });
                            }
                        });
                    }
                    ok(out);
                }).catch(fail);
        },

        eventDidMount: function (arg) {
            if (arg.event.extendedProps.is_day_summary) {
                arg.el.style.cursor = 'pointer';
                arg.el.querySelectorAll('[data-nurse-idx]').forEach(el => {
                    el.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const n = arg.event.extendedProps.nurses[+this.dataset.nurseIdx];
                        if (!n) return;
                        openEditModal({ id: n.id, title: n.name,
                            extendedProps: { nurse_name: n.name, shift_type: n.shift_type, status: n.status, notes: n.notes, is_peds: n.is_peds },
                            start: new Date(n.startFull), end: new Date(n.endFull) });
                    });
                });
                arg.el.addEventListener('click', function(e) {
                    e.stopPropagation();
                    cal.changeView('timeGridDay', arg.event.startStr);
                });
                return;
            }
            if (arg.event.extendedProps.is_open) {
                const el = arg.el;
                el.style.setProperty('background','repeating-linear-gradient(-45deg,rgba(239,68,68,0.07),rgba(239,68,68,0.07) 5px,rgba(255,242,242,0.85) 5px,rgba(255,242,242,0.85) 15px)','important');
                el.style.setProperty('border','1.5px dashed rgba(239,68,68,0.45)','important');
                el.style.setProperty('box-shadow','none','important');
            }
        },

        select: function (info) {
            let s, e;
            if (info.allDay) { const dt = defaultShiftTimes(info.start); s = dt.start; e = dt.end; }
            else {
                s = new Date(info.start); e = new Date(info.start);
                const h = info.start.getHours();
                if (h < 7) { s.setHours(3,0,0,0); e.setHours(7,0,0,0); }
                else if (h < 19) { s.setHours(7,0,0,0); e.setHours(19,0,0,0); }
                else { s.setHours(19,0,0,0); e.setDate(e.getDate()+1); e.setHours(7,0,0,0); }
            }
            openCreateModal(s, e); cal.unselect();
        },

        eventClick: function (info) {
            const p = info.event.extendedProps;
            if (p.is_day_summary) { cal.changeView('timeGridDay', info.event.startStr); return; }
            if (p.is_open) {
                const s = new Date(info.event.start), e = new Date(info.event.start);
                const h = s.getHours();
                if (h < 7) { s.setHours(3,0,0,0); e.setHours(7,0,0,0); }
                else if (h < 19) { s.setHours(7,0,0,0); e.setHours(19,0,0,0); }
                else { s.setHours(19,0,0,0); e.setDate(e.getDate()+1); e.setHours(7,0,0,0); }
                openCreateModal(s, e);
            } else { openEditModal(info.event); }
        },

        eventDrop: function (info) {
            if (info.event.extendedProps.is_open) { info.revert(); return; }
            patchTimes(info.event); showToast('Shift moved ✓');
        },
        eventResize: function (info) {
            if (info.event.extendedProps.is_open) { info.revert(); return; }
            patchTimes(info.event); showToast('Shift resized ✓');
        },

        eventContent: function (arg) {
            const p = arg.event.extendedProps;
            if (p.is_day_summary) {
                const segs = (p.openBlocks || []).map(isOpen =>
                    `<div class="cov-seg" style="background:${isOpen ? 'rgba(239,68,68,0.55)' : 'rgba(16,185,129,0.65)'}"></div>`).join('');
                const MAX = window.innerWidth < 640 ? 3 : 6;
                const visible = p.nurses ? p.nurses.slice(0, MAX) : [];
                const overflow = p.nurses ? p.nurses.length - MAX : 0;
                const names = visible.length
                    ? visible.map((n, i) => `<div class="cov-name" data-nurse-idx="${i}" style="cursor:pointer;border-radius:3px;padding:0 4px" onmouseover="this.style.background='rgba(0,0,0,0.06)'" onmouseout="this.style.background=''"><span style="opacity:.55;margin-right:4px;font-variant-numeric:tabular-nums">${n.start}-${n.end}</span>${n.name}${n.is_peds ? '<span style="color:#0ea5e9;font-size:.6rem;margin-left:3px;font-weight:600">(Peds)</span>' : ''}</div>`).join('')
                      + (overflow > 0 ? `<div class="cov-name" style="opacity:.5;padding:0 4px">+${overflow} more</div>` : '') : '';
                return { html: `<div><div class="cov-bar">${segs}</div>${names}</div>` };
            }
            if (p.is_open) {
                return { html: `<div class="ev-open"><div class="ev-open-lbl">OPEN</div><div class="ev-open-plus">＋</div></div>` };
            }
            const name = p.nurse_name || arg.event.title || 'Unassigned';
            const type = p.shift_type || 'custom', status = p.status || 'scheduled';
            const dot = STATUS_DOT[status] || '#f59e0b', lbl = TYPE_LBL[type] || type;
            const struck = status === 'cancelled';
            const isMonth = cal.view.type === 'dayGridMonth' || cal.view.type === 'dayGrid';
            return { html: `<div class="ev-card"><div class="ev-dot" style="background:${dot}"></div><div class="ev-name${struck ? ' struck' : ''}">${name}</div>${isMonth ? '' : `<div class="ev-badge">${lbl}</div>`}</div>` };
        }
    });

    cal.render();

    function patchTimes(event) {
        const p = event.extendedProps;
        fetch('api.php?action=update', { method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ id: event.id, nurse_name: p.nurse_name || event.title,
                shift_start: toMySQLLocal(event.start), shift_end: toMySQLLocal(event.end),
                shift_type: p.shift_type, status: p.status, notes: p.notes || '', color: event.backgroundColor })
        }).then(r => r.json())
          .then(d => { if (d.success) cal.refetchEvents(); else { showToast('Save failed'); cal.refetchEvents(); } })
          .catch(() => cal.refetchEvents());
    }

    function openCreateModal(start, end, preset) {
        $('modalTitle').textContent = 'Add Shift';
        $('shiftId').value = ''; $('nurseName').value = '';
        $('shiftStart').value = toLocalISO(start); $('shiftEnd').value = toLocalISO(end);
        $('shiftStatus').value = 'scheduled'; $('shiftTypeSelect').value = preset || 'auto';
        $('shiftNotes').value = ''; $('isPeds').checked = false;
        $('deleteBtn').style.display = 'none'; $('editModal').classList.add('on');
        setTimeout(() => $('nurseName').focus(), 130);
        $('nurseName').onkeydown = e => { if (e.key === 'Enter') saveShift(); };
    }

    function openEditModal(event) {
        $('modalTitle').textContent = 'Edit Shift';
        $('shiftId').value = event.id;
        $('nurseName').value = event.extendedProps.nurse_name || event.title;
        $('shiftStart').value = toLocalISO(event.start); $('shiftEnd').value = toLocalISO(event.end);
        $('shiftStatus').value = event.extendedProps.status || 'scheduled';
        $('shiftTypeSelect').value = event.extendedProps.shift_type || 'auto';
        $('shiftNotes').value = event.extendedProps.notes || '';
        $('isPeds').checked = !!event.extendedProps.is_peds;
        $('deleteBtn').style.display = 'flex'; $('editModal').classList.add('on');
        $('nurseName').onkeydown = e => { if (e.key === 'Enter') saveShift(); };
    }

    window.closeModal = function () { $('editModal').classList.remove('on'); };

    const TYPE_HOURS = { '12hr-day':12, '12hr-noc':12, '8hr':8, '4hr':4 };
    $('shiftTypeSelect').addEventListener('change', function () {
        const hours = TYPE_HOURS[this.value];
        if (!hours || !$('shiftStart').value) return;
        const start = new Date($('shiftStart').value);
        if (isNaN(start)) return;
        $('shiftEnd').value = toLocalISO(new Date(start.getTime() + hours * 3600000));
    });

    $('editModal').addEventListener('click', e => { if (e.target === $('editModal')) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    window.saveShift = function () {
        const id = $('shiftId').value;
        const nurseName  = $('nurseName').value.trim() || 'Open';
        const shiftStart = $('shiftStart').value.replace('T',' ');
        const shiftEnd   = $('shiftEnd').value.replace('T',' ');
        const status = $('shiftStatus').value, notes = $('shiftNotes').value.trim();
        const isPeds = $('isPeds').checked;
        let type = $('shiftTypeSelect').value;
        if (type === 'auto') {
            const startHour = parseInt(shiftStart.split(' ')[1].split(':')[0]);
            const dur = (new Date(shiftEnd.replace(' ','T')) - new Date(shiftStart.replace(' ','T'))) / 3600000;
            if      (dur >= 11.5 && dur <= 12.5) type = (startHour === 7) ? '12hr-day' : '12hr-noc';
            else if (dur >= 7.5  && dur <= 8.5)  type = '8hr';
            else if (dur >= 3.5  && dur <= 4.5)  type = '4hr';
            else                                  type = 'custom';
        }
        const colorMap = { '12hr-day':'#0ea5e9','12hr-noc':'#1e3a5f','8hr':'#0d9488','4hr':'#38bdf8','custom':'#6366f1' };
        const color = colorMap[type] || '#0ea5e9';
        const action = id ? 'update' : 'create';
        const payload = { id: id || undefined, nurse_name: nurseName, shift_start: shiftStart, shift_end: shiftEnd, shift_type: type, status, notes, color, is_peds: isPeds };
        fetch(`api.php?action=${action}`, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) })
            .then(r => r.json())
            .then(d => { if (d.success) { closeModal(); cal.refetchEvents(); showToast(id ? 'Shift updated ✓' : 'Shift added ✓'); } else showToast('Failed to save'); })
            .catch(() => showToast('Network error'));
    };

    window.deleteShift = function () {
        const id = $('shiftId').value;
        if (!id) { closeModal(); return; }
        if (!confirm('Delete this shift? This cannot be undone.')) return;
        fetch(`api.php?action=delete&id=${id}`, { method:'DELETE' })
            .then(r => r.json())
            .then(() => { closeModal(); cal.refetchEvents(); showToast('Shift deleted'); })
            .catch(() => showToast('Delete failed'));
    };
});
</script>
</body>
</html>
