<?php
require "../php/auth_check.php";
requireSuperAdmin();
$avatarLetter = strtoupper(substr(CURRENT_USER, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>LGU-Connect | Municipality of San Julian</title>
<link rel="icon" href="../assets/img/logo.png" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap-icons.min.css"/>
<link rel="stylesheet" href="../assets/css/sidebar_header.css"/>
<style>
:root {
  --red-main: #8B1A1A;
  --red-light: #f8f0f0;
  --red-border: #e8c4c4;
  --card-radius: 12px;
}

.export-content { padding: 24px; }

/* ── Section title ── */
.section-label {
  font-size: 11px;
  font-weight: 700;
  color: #999;
  text-transform: uppercase;
  letter-spacing: .08em;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.section-label::after {
  content: '';
  flex: 1;
  height: 1px;
  background: #efefef;
}

/* ── Export Cards Grid ── */
.export-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 18px;
  margin-bottom: 32px;
}

.export-card {
  background: #fff;
  border-radius: var(--card-radius);
  border: 1px solid #e8e8e8;
  overflow: hidden;
  transition: box-shadow .2s, border-color .2s;
}
.export-card:hover {
  box-shadow: 0 4px 20px rgba(0,0,0,.08);
  border-color: var(--red-border);
}

.export-card-header {
  padding: 18px 20px 14px;
  display: flex;
  align-items: flex-start;
  gap: 14px;
}
.export-card-icon {
  width: 44px; height: 44px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}
.export-card-icon.red   { background: #fff0f0; color: var(--red-main); }
.export-card-icon.green { background: #eef8f0; color: #1e7c3b; }
.export-card-icon.blue  { background: #eef5ff; color: #1a6fbf; }
.export-card-icon.amber { background: #fff8ee; color: #b06c10; }

.export-card-info h3 {
  font-size: 14px;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0 0 4px;
}
.export-card-info p {
  font-size: 12px;
  color: #888;
  margin: 0;
  line-height: 1.5;
}

/* ── Columns preview ── */
.col-preview {
  padding: 0 20px 14px;
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}
.col-tag {
  background: #f5f5f5;
  color: #666;
  font-size: 10.5px;
  padding: 2px 8px;
  border-radius: 10px;
  border: 1px solid #ececec;
}
.col-tag.highlight { background: var(--red-light); color: var(--red-main); border-color: var(--red-border); }

/* ── Export card footer ── */
.export-card-footer {
  padding: 14px 20px;
  border-top: 1px solid #f5f5f5;
  display: flex;
  gap: 8px;
}
.btn-export {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 9px 14px;
  border-radius: 7px;
  font-size: 12.5px;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: all .18s;
  text-decoration: none;
}
.btn-export.csv {
  background: #f0f9f0;
  color: #1e7c3b;
  border: 1px solid #c8e6c9;
}
.btn-export.csv:hover { background: #1e7c3b; color: #fff; }
.btn-export.excel {
  background: #e8f5e9;
  color: #155724;
  border: 1px solid #a5d6a7;
}
.btn-export.excel:hover { background: #155724; color: #fff; }

/* ── Filters panel ── */
.filters-panel {
  background: #fff;
  border-radius: var(--card-radius);
  border: 1px solid #e8e8e8;
  padding: 20px;
  margin-bottom: 28px;
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  gap: 16px;
}
.filter-field { display: flex; flex-direction: column; gap: 6px; }
.filter-field label {
  font-size: 11px;
  font-weight: 600;
  color: #777;
  text-transform: uppercase;
  letter-spacing: .05em;
}
.filter-field select,
.filter-field input[type=date] {
  padding: 8px 12px;
  font-size: 13px;
  border: 1px solid #ddd;
  border-radius: 7px;
  background: #fafafa;
  color: #333;
  min-width: 160px;
}
.filter-field select:focus,
.filter-field input:focus {
  outline: none;
  border-color: var(--red-main);
  box-shadow: 0 0 0 3px rgba(139,26,26,.08);
}

/* ── Recent exports log ── */
.export-log {
  background: #fff;
  border-radius: var(--card-radius);
  border: 1px solid #e8e8e8;
  overflow: hidden;
}
.export-log-header {
  padding: 14px 18px;
  border-bottom: 1px solid #f5f5f5;
  font-size: 13.5px;
  font-weight: 600;
  color: #1a1a1a;
  display: flex;
  align-items: center;
  gap: 7px;
}
.export-log-header i { color: var(--red-main); }
.log-list { padding: 8px 0; max-height: 260px; overflow-y: auto; }
.log-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 18px;
  border-bottom: 1px solid #fafafa;
  font-size: 12.5px;
  transition: background .12s;
}
.log-item:last-child { border-bottom: none; }
.log-item:hover { background: #fafafa; }
.log-icon { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
.log-icon.csv   { background: #f0f9f0; color: #1e7c3b; }
.log-icon.excel { background: #e8f5e9; color: #155724; }
.log-name  { flex: 1; color: #333; font-weight: 500; }
.log-time  { color: #aaa; font-size: 11px; }
.log-empty { padding: 28px; text-align: center; color: #bbb; font-size: 13px; }

/* ── Stats bar ── */
.stats-bar {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  margin-bottom: 24px;
}
.stats-box {
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 10px;
  padding: 14px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.stats-box i { font-size: 22px; color: var(--red-main); opacity: .7; }
.stats-box .sv { font-size: 22px; font-weight: 700; color: #1a1a1a; }
.stats-box .sl { font-size: 11px; color: #999; margin-top: 2px; }
</style>
</head>
<body>
<div class="app-shell">

  <!-- ══════════ SIDEBAR ══════════ -->
  <aside class="sidebar" id="sidebar">
    <div class="sb-brand">
      <img src="../assets/img/logo.png" class="sb-logo-img" alt="Logo"
           onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
      <div class="sb-logo-fallback" style="display:none">SJ</div>
      <div class="sb-brand-text">
        <div class="sb-name">LGU<span>-Connect</span></div>
        <div class="sb-sub">San Julian, E. Samar</div>
      </div>
    </div>
    <div class="sb-role">
      <div class="role-dot"></div>
      <div>
        <div class="role-name"><?= htmlspecialchars(CURRENT_USER) ?></div>
        <div class="role-sub">Super Administrator</div>
      </div>
    </div>
    <div class="sb-section">Main</div>
    <ul class="sb-nav">
      <li><a href="admin_dashboard.php"><span class="nav-icon"><i class="bi bi-speedometer2"></i></span> Dashboard</a></li>
      <li><a href="admin_departments.php"><span class="nav-icon"><i class="bi bi-building"></i></span> Departments</a></li>
      <li><a href="admin_allfeedback.php"><span class="nav-icon"><i class="bi bi-clipboard-check"></i></span> All Feedback <span class="nav-badge" id="sbFeedbackCount">0</span></a></li>
    </ul>
    <div class="sb-section">Reports</div>
    <ul class="sb-nav">
      <li><a href="admin_csmr_generator.php"><span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span> CSMR Generator</a></li>
      <li><a href="admin_analytics.php"><span class="nav-icon"><i class="bi bi-bar-chart-line"></i></span> Analytics</a></li>
      <li><a href="admin_exportdata.php" class="active"><span class="nav-icon"><i class="bi bi-download"></i></span> Export Data</a></li>
    </ul>
    <div class="sb-section">System</div>
    <ul class="sb-nav">
      <li><a href="admin_manage_users.php"><span class="nav-icon"><i class="bi bi-people"></i></span> Manage Users</a></li>
      <li><a href="admin_qrcodes.php"><span class="nav-icon"><i class="bi bi-qr-code"></i></span> QR Codes</a></li>
      <li><a href="admin_settings.php"><span class="nav-icon"><i class="bi bi-gear"></i></span> Settings</a></li>
    </ul>
    <div class="sb-footer">
      <a href="../php/logout.php" onclick="return confirm('Sign out?')">
        <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span> Sign Out
      </a>
    </div>
  </aside>

  <!-- ══════════ MAIN AREA ══════════ -->
  <div class="main-area">

    <!-- Topbar -->
    <div class="topbar">
      <button class="menu-toggle" id="menuToggle">&#9776;</button>
      <div class="topbar-title">
        Export Data
        <span class="tb-subtitle">Download feedback data as CSV or Excel</span>
      </div>
      <div class="topbar-actions">
        <button class="tb-btn" onclick="location.reload()">
          <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="tb-btn primary" onclick="location.href='admin_csmr_generator.php'">
          <i class="bi bi-file-earmark-text"></i> Generate CSMR
        </button>
        <div class="tb-avatar" id="topbarAvatar" onclick="toggleAvatarDropdown(event)">
          <?= $avatarLetter ?>
          <div class="avatar-dropdown" id="avatarDropdown">
            <div class="av-header">
              <div class="av-name"><?= htmlspecialchars(CURRENT_USER) ?></div>
              <div class="av-role">Super Administrator</div>
            </div>
            <div class="av-menu">
              <a href="admin_settings.php" class="av-item"><i class="bi bi-person-circle"></i> My Profile</a>
              <a href="admin_settings.php" class="av-item"><i class="bi bi-gear"></i> Settings</a>
              <div class="av-divider"></div>
              <a href="../php/logout.php" class="av-item danger" onclick="return confirm('Sign out?')">
                <i class="bi bi-box-arrow-right"></i> Sign Out
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">
      <div class="live-bar">
        <div class="live-dot"></div>
        <span class="live-text">Live &nbsp;&middot;&nbsp; Export feedback records, summaries, and SQD scores</span>
        <span class="live-date" id="todayDate"></span>
      </div>

      <div class="export-content">

        <!-- ── Quick Stats ── -->
        <div class="stats-bar" id="statsBar">
          <div class="stats-box">
            <i class="bi bi-clipboard-data"></i>
            <div>
              <div class="sv" id="statTotalFeedback">—</div>
              <div class="sl">Total Feedback Records</div>
            </div>
          </div>
          <div class="stats-box">
            <i class="bi bi-building"></i>
            <div>
              <div class="sv" id="statTotalDepts">—</div>
              <div class="sl">Active Departments</div>
            </div>
          </div>
          <div class="stats-box">
            <i class="bi bi-calendar-range"></i>
            <div>
              <div class="sv" id="statDateRange">—</div>
              <div class="sl">Selected Date Range</div>
            </div>
          </div>
        </div>

        <!-- ── Filters ── -->
        <div class="section-label"><i class="bi bi-funnel"></i> Export Filters</div>
        <div class="filters-panel">
          <div class="filter-field">
            <label><i class="bi bi-building" style="margin-right:3px"></i> Department</label>
            <select id="filterDept">
              <option value="">All Departments</option>
            </select>
          </div>
          <div class="filter-field">
            <label><i class="bi bi-calendar" style="margin-right:3px"></i> Date From</label>
            <input type="date" id="filterDateFrom"/>
          </div>
          <div class="filter-field">
            <label><i class="bi bi-calendar" style="margin-right:3px"></i> Date To</label>
            <input type="date" id="filterDateTo"/>
          </div>
          <div class="filter-field">
            <label>Quick Range</label>
            <select id="quickRange" onchange="applyQuickRange()">
              <option value="">Custom</option>
              <option value="this_month" selected>This Month</option>
              <option value="last_month">Last Month</option>
              <option value="this_quarter">This Quarter</option>
              <option value="this_year">This Year</option>
              <option value="all_time">All Time</option>
            </select>
          </div>
          <div class="filter-field" style="margin-left:auto">
            <label>&nbsp;</label>
            <button onclick="updateStats()" style="padding:8px 18px;background:var(--red-main);color:#fff;border:none;border-radius:7px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px">
              <i class="bi bi-arrow-clockwise"></i> Update Preview
            </button>
          </div>
        </div>

        <!-- ── Export Cards ── -->
        <div class="section-label"><i class="bi bi-download"></i> Choose Export Type</div>
        <div class="export-grid">

          <!-- Raw Feedback -->
          <div class="export-card">
            <div class="export-card-header">
              <div class="export-card-icon red"><i class="bi bi-clipboard-data-fill"></i></div>
              <div class="export-card-info">
                <h3>Raw Feedback Records</h3>
                <p>All individual feedback submissions with full details including SQD scores, comments, and respondent demographics.</p>
              </div>
            </div>
            <div class="col-preview">
              <span class="col-tag highlight">ID</span>
              <span class="col-tag highlight">Department</span>
              <span class="col-tag highlight">Rating</span>
              <span class="col-tag">Respondent Type</span>
              <span class="col-tag">Sex</span>
              <span class="col-tag">Age Group</span>
              <span class="col-tag">SQD0–SQD8</span>
              <span class="col-tag">Comment</span>
              <span class="col-tag">Suggestions</span>
              <span class="col-tag">Date</span>
            </div>
            <div class="export-card-footer">
              <button class="btn-export csv" onclick="doExport('feedback','csv')">
                <i class="bi bi-filetype-csv"></i> Export CSV
              </button>
              <button class="btn-export excel" onclick="doExport('feedback','excel')">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
              </button>
            </div>
          </div>

          <!-- Department Summary -->
          <div class="export-card">
            <div class="export-card-header">
              <div class="export-card-icon green"><i class="bi bi-building-check"></i></div>
              <div class="export-card-info">
                <h3>Department Summary</h3>
                <p>Aggregated statistics per department — total responses, average rating, satisfaction rate, and rating breakdown.</p>
              </div>
            </div>
            <div class="col-preview">
              <span class="col-tag highlight">Department</span>
              <span class="col-tag highlight">Total Responses</span>
              <span class="col-tag highlight">Avg Rating</span>
              <span class="col-tag highlight">Satisfaction %</span>
              <span class="col-tag">Excellent</span>
              <span class="col-tag">Good</span>
              <span class="col-tag">Average</span>
              <span class="col-tag">Poor</span>
              <span class="col-tag">Avg SQD0–8</span>
            </div>
            <div class="export-card-footer">
              <button class="btn-export csv" onclick="doExport('summary','csv')">
                <i class="bi bi-filetype-csv"></i> Export CSV
              </button>
              <button class="btn-export excel" onclick="doExport('summary','excel')">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
              </button>
            </div>
          </div>

          <!-- SQD Scores -->
          <div class="export-card">
            <div class="export-card-header">
              <div class="export-card-icon blue"><i class="bi bi-bar-chart-steps"></i></div>
              <div class="export-card-info">
                <h3>SQD Scores Report</h3>
                <p>Service Quality Dimension scores per department — all 9 SQD dimensions with overall averages. Ideal for ARTA compliance.</p>
              </div>
            </div>
            <div class="col-preview">
              <span class="col-tag highlight">Department</span>
              <span class="col-tag highlight">SQD0–SQD8 Scores</span>
              <span class="col-tag highlight">Overall SQD Average</span>
              <span class="col-tag">Responses</span>
            </div>
            <div class="export-card-footer">
              <button class="btn-export csv" onclick="doExport('sqd','csv')">
                <i class="bi bi-filetype-csv"></i> Export CSV
              </button>
              <button class="btn-export excel" onclick="doExport('sqd','excel')">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
              </button>
            </div>
          </div>

          <!-- Departments List -->
          <div class="export-card">
            <div class="export-card-header">
              <div class="export-card-icon amber"><i class="bi bi-buildings-fill"></i></div>
              <div class="export-card-info">
                <h3>Departments Directory</h3>
                <p>Complete list of all registered departments with their head officers, status, and total feedback received.</p>
              </div>
            </div>
            <div class="col-preview">
              <span class="col-tag highlight">Department Name</span>
              <span class="col-tag highlight">Code</span>
              <span class="col-tag highlight">Head Officer</span>
              <span class="col-tag">Status</span>
              <span class="col-tag">Total Feedback</span>
              <span class="col-tag">Avg Rating</span>
            </div>
            <div class="export-card-footer">
              <button class="btn-export csv" onclick="doExport('departments','csv')">
                <i class="bi bi-filetype-csv"></i> Export CSV
              </button>
              <button class="btn-export excel" onclick="doExport('departments','excel')">
                <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
              </button>
            </div>
          </div>

        </div>

        <!-- ── Export Log ── -->
        <div class="section-label"><i class="bi bi-clock-history"></i> Export History <span style="font-weight:400;color:#bbb;font-size:10px;text-transform:none;letter-spacing:0">(this session)</span></div>
        <div class="export-log">
          <div class="log-list" id="exportLog">
            <div class="log-empty">No exports yet this session. Choose an export type above.</div>
          </div>
        </div>

      </div><!-- /export-content -->
    </div><!-- /page-content -->
  </div><!-- /main-area -->
</div><!-- /app-shell -->

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/jquery-4.0.0.min.js"></script>
<script src="../js/admin/admin_sidebarcount.js"></script>
<script>

// ── Date display ──
document.getElementById('todayDate').textContent =
  new Date().toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});

// ── Default date range ──
const now = new Date();
document.getElementById('filterDateFrom').value = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
document.getElementById('filterDateTo').value   = new Date(now.getFullYear(), now.getMonth()+1, 0).toISOString().split('T')[0];

// ── Load departments ──
$.ajax({
  url: '../php/get/get_departments.php',
  method: 'GET',
  success(res) {
    const depts = Array.isArray(res) ? res : (res.data || res.departments || []);
    const sel   = document.getElementById('filterDept');
    depts.forEach(d => {
      const opt = document.createElement('option');
      opt.value = d.code;
      opt.textContent = d.name;
      sel.appendChild(opt);
    });
    updateStats();
  }
});

// ── Quick range ──
function applyQuickRange() {
  const val = document.getElementById('quickRange').value;
  const n   = new Date();
  let from, to;
  switch(val) {
    case 'this_month':
      from = new Date(n.getFullYear(), n.getMonth(), 1);
      to   = new Date(n.getFullYear(), n.getMonth()+1, 0); break;
    case 'last_month':
      from = new Date(n.getFullYear(), n.getMonth()-1, 1);
      to   = new Date(n.getFullYear(), n.getMonth(), 0); break;
    case 'this_quarter':
      const q = Math.floor(n.getMonth()/3);
      from = new Date(n.getFullYear(), q*3, 1);
      to   = new Date(n.getFullYear(), q*3+3, 0); break;
    case 'this_year':
      from = new Date(n.getFullYear(), 0, 1);
      to   = new Date(n.getFullYear(), 11, 31); break;
    case 'all_time':
      from = new Date('2000-01-01');
      to   = new Date(); break;
    default: return;
  }
  document.getElementById('filterDateFrom').value = from.toISOString().split('T')[0];
  document.getElementById('filterDateTo').value   = to.toISOString().split('T')[0];
  updateStats();
}

// ── Update stats preview ──
function updateStats() {
  const from = document.getElementById('filterDateFrom').value;
  const to   = document.getElementById('filterDateTo').value;
  const dept = document.getElementById('filterDept').value;

  // Show range in stat box
  const fmtDate = d => new Date(d).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'});
  document.getElementById('statDateRange').textContent =
    from && to ? fmtDate(from) + ' – ' + fmtDate(to) : '—';

  // Fetch quick counts via AJAX
  $.ajax({
    url: '../php/get/get_analytics_data.php',
    method: 'POST',
    dataType: 'json',
    data: { period: 'custom', dept_id: dept, date_from: from, date_to: to },
    success(res) {
      if (res.success) {
        document.getElementById('statTotalFeedback').textContent = Number(res.kpi.total_responses).toLocaleString();
        document.getElementById('statTotalDepts').textContent    = res.kpi.dept_count;
      }
    }
  });
}

// ── Export ──
const exportLog = [];

function doExport(type, format) {
  const from = document.getElementById('filterDateFrom').value;
  const to   = document.getElementById('filterDateTo').value;
  const dept = document.getElementById('filterDept').value;
  const deptName = document.getElementById('filterDept').selectedOptions[0].text;

  if (!from || !to) { alert('Please select a date range first.'); return; }

  const params = new URLSearchParams({
    type, format,
    dept_id: dept,
    date_from: from,
    date_to: to
  });

  // Trigger download
  window.location.href = '../php/get/get_export_data.php?' + params.toString();

  // Log the export
  const now    = new Date();
  const labels = {
    feedback: 'Raw Feedback Records',
    summary:  'Department Summary',
    sqd:      'SQD Scores Report',
    departments: 'Departments Directory'
  };
  exportLog.unshift({
    type, format,
    label: labels[type],
    dept: dept ? deptName : 'All Departments',
    from, to,
    time: now.toLocaleTimeString('en-PH', {hour:'2-digit',minute:'2-digit'})
  });
  renderLog();
}

function renderLog() {
  const el = document.getElementById('exportLog');
  if (exportLog.length === 0) {
    el.innerHTML = '<div class="log-empty">No exports yet this session.</div>';
    return;
  }

  const fmtDate = d => new Date(d).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'});
  const typeIcon = { feedback:'bi-clipboard-data-fill', summary:'bi-building-check', sqd:'bi-bar-chart-steps', departments:'bi-buildings-fill' };

  el.innerHTML = exportLog.map(e => `
    <div class="log-item">
      <div class="log-icon ${e.format}">
        <i class="bi ${typeIcon[e.type] || 'bi-download'}"></i>
      </div>
      <div class="log-name">
        ${e.label}
        <span style="font-size:11px;color:#aaa;font-weight:400;margin-left:6px">
          ${e.dept} · ${fmtDate(e.from)} – ${fmtDate(e.to)}
        </span>
      </div>
      <span style="font-size:10px;background:${e.format==='csv'?'#f0f9f0':'#e8f5e9'};
        color:${e.format==='csv'?'#1e7c3b':'#155724'};
        padding:2px 8px;border-radius:10px;font-weight:600;margin-right:6px">
        .${e.format==='excel'?'xls':'csv'}
      </span>
      <div class="log-time">${e.time}</div>
    </div>
  `).join('');
}

// ── Avatar dropdown ──
function toggleAvatarDropdown(e) {
  e.stopPropagation();
  document.getElementById('avatarDropdown').classList.toggle('show');
}
document.addEventListener('click', () => {
  document.getElementById('avatarDropdown').classList.remove('show');
});

// ── Custom range detection ──
document.getElementById('filterDateFrom').addEventListener('change', () => {
  document.getElementById('quickRange').value = '';
});
document.getElementById('filterDateTo').addEventListener('change', () => {
  document.getElementById('quickRange').value = '';
});
</script>
</body>
</html>