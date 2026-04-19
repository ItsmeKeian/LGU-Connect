<?php
require "../php/auth_check.php";
requireDeptUser();
require "../php/dbconnect.php";

$avatarLetter = strtoupper(substr(CURRENT_USER, 0, 1));
$dept_code    = CURRENT_DEPT;

$deptStmt = $conn->prepare("SELECT * FROM departments WHERE code = ? LIMIT 1");
$deptStmt->execute([$dept_code]);
$deptInfo = $deptStmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Generate CSMR | <?= htmlspecialchars($deptInfo['name'] ?? 'Department') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="icon" href="../assets/img/logo.png" type="image/x-icon">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="../assets/css/bootstrap-icons.min.css"/>
<link rel="stylesheet" href="../assets/css/sidebar_header.css"/>
<link rel="stylesheet" href="../assets/css/admin_dashboard.css"/>
<style>
.sb-dept-badge{margin:8px 10px 4px;background:rgba(139,26,26,.35);border:1px solid rgba(139,26,26,.5);border-radius:8px;padding:9px 12px;display:flex;align-items:center;gap:9px}
.sb-dept-badge-icon{width:28px;height:28px;background:#8B1A1A;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:13px;color:#fff;flex-shrink:0}
.sb-dept-badge-name{font-size:11px;font-weight:600;color:#fff;line-height:1.3}
.sb-dept-badge-code{font-size:10px;color:rgba(255,255,255,.45);margin-top:1px}

:root{--red:#8B1A1A;--red-dark:#6e1414;--red-light:#fdf0f0;--red-border:#e8c4c4;}

.csmr-content{padding:24px;display:grid;grid-template-columns:280px 1fr;gap:20px;align-items:start;}
@media(max-width:960px){.csmr-content{grid-template-columns:1fr;}}

.filter-panel{background:#fff;border-radius:12px;border:1px solid #e8e8e8;overflow:hidden;position:sticky;top:20px;}
.filter-panel-header{background:linear-gradient(135deg,var(--red),var(--red-dark));padding:16px 18px;color:#fff;display:flex;align-items:center;gap:10px;}
.filter-panel-header i{font-size:18px;}
.filter-panel-header h2{font-size:14px;font-weight:700;margin:0;}
.filter-panel-header small{font-size:11px;opacity:.75;display:block;margin-top:2px;}
.filter-body{padding:18px;}
.filter-group{margin-bottom:16px;}
.filter-group label{font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.06em;display:block;margin-bottom:8px;}
.filter-group input[type=date],.filter-group input[type=text]{width:100%;padding:9px 12px;font-size:13px;border:1px solid #ddd;border-radius:8px;background:#fafafa;color:#333;font-family:inherit;margin-bottom:8px;}
.filter-group input:focus{outline:none;border-color:var(--red);box-shadow:0 0 0 3px rgba(139,26,26,.08);}
.filter-divider{border:none;border-top:1px solid #f0f0f0;margin:16px 0;}

.period-chips{display:flex;flex-wrap:wrap;gap:6px;}
.period-chip{padding:5px 12px;border-radius:16px;font-size:12px;font-weight:600;border:1.5px solid #ddd;background:#fafafa;color:#777;cursor:pointer;transition:all .18s;user-select:none;}
.period-chip:hover{border-color:var(--red);color:var(--red);background:var(--red-light);}
.period-chip.active{background:var(--red);color:#fff;border-color:var(--red);}

.dept-notice{background:var(--red-light);border:1px solid var(--red-border);border-radius:8px;padding:10px 12px;margin-bottom:16px;font-size:12px;color:var(--red);display:flex;align-items:center;gap:8px;}
.dept-notice i{font-size:14px;flex-shrink:0;}

.btn-generate{width:100%;padding:12px;background:linear-gradient(135deg,var(--red),var(--red-dark));color:#fff;border:none;border-radius:9px;font-size:13.5px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 3px 10px rgba(139,26,26,.25);transition:all .2s;margin-bottom:10px;}
.btn-generate:hover{box-shadow:0 5px 16px rgba(139,26,26,.35);transform:translateY(-1px);}
.btn-generate:disabled{opacity:.7;pointer-events:none;}
.btn-print{width:100%;padding:11px;background:#fff;color:var(--red);border:1.5px solid var(--red);border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;display:none;align-items:center;justify-content:center;gap:8px;transition:all .2s;}
.btn-print:hover{background:var(--red-light);}
.btn-print.show{display:flex;}
@keyframes spin{to{transform:rotate(360deg);}}
.spin-anim{animation:spin .7s linear infinite;display:inline-block;}

.preview-panel{display:flex;flex-direction:column;gap:16px;}

.csmr-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;}
@media(max-width:700px){.csmr-stats{grid-template-columns:repeat(2,1fr);}}
.csmr-stat{background:#fff;border-radius:10px;border:1px solid #e8e8e8;padding:14px 16px;display:flex;align-items:center;gap:12px;}
.csmr-stat-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;}
.csmr-stat-icon.red{background:#fff0f0;color:var(--red);}
.csmr-stat-icon.green{background:#eef8f0;color:#1e7c3b;}
.csmr-stat-icon.blue{background:#eef5ff;color:#1a6fbf;}
.csmr-stat-icon.gold{background:#fff8ee;color:#b06c10;}
.csmr-stat-val{font-size:20px;font-weight:700;color:#1a1a1a;line-height:1;}
.csmr-stat-label{font-size:11px;color:#999;margin-top:3px;}

.preview-card{background:#fff;border-radius:12px;border:1px solid #e8e8e8;overflow:hidden;}
.preview-card-header{padding:14px 20px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;justify-content:space-between;}
.preview-card-header h3{font-size:14px;font-weight:700;color:#1a1a1a;margin:0;}
.period-badge{background:var(--red-light);color:var(--red);font-size:11px;font-weight:600;padding:4px 12px;border-radius:12px;border:1px solid var(--red-border);display:flex;align-items:center;gap:5px;}

.preview-empty{padding:60px 20px;text-align:center;color:#bbb;}
.preview-empty i{font-size:48px;display:block;margin-bottom:14px;opacity:.4;}
.preview-empty h4{font-size:15px;font-weight:600;color:#999;margin-bottom:6px;}
.preview-empty p{font-size:13px;color:#bbb;}

.preview-spinner{padding:40px;text-align:center;display:none;}
.spin-circle{width:36px;height:36px;border:3px solid #f0f0f0;border-top-color:var(--red);border-radius:50%;animation:spin .7s linear infinite;margin:0 auto 12px;}
.preview-spinner p{font-size:13px;color:#aaa;}

.csmr-charts{display:grid;grid-template-columns:1fr 1fr;gap:14px;padding:16px 20px;}
@media(max-width:700px){.csmr-charts{grid-template-columns:1fr;}}
.chart-section h5{font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px;}

.rating-bar-row{display:flex;align-items:center;gap:8px;margin-bottom:8px;}
.rating-bar-row .rbl{font-size:11.5px;color:#555;width:90px;flex-shrink:0;}
.rating-bar-row .rbw{flex:1;height:10px;background:#f0f0f0;border-radius:5px;overflow:hidden;}
.rating-bar-row .rbf{height:100%;border-radius:5px;transition:width .8s ease;}
.rating-bar-row .rbc{font-size:11px;font-weight:600;color:#555;width:60px;text-align:right;flex-shrink:0;}

.sqd-bar-row{display:flex;align-items:center;gap:8px;margin-bottom:8px;}
.sqd-bar-row .sbl{font-size:11px;color:#666;width:46px;flex-shrink:0;font-weight:700;}
.sqd-bar-row .sbw{flex:1;height:8px;background:#f0f0f0;border-radius:4px;overflow:hidden;}
.sqd-bar-row .sbf{height:100%;border-radius:4px;transition:width .8s ease;}
.sqd-bar-row .sbc{font-size:11px;font-weight:700;width:36px;text-align:right;flex-shrink:0;}

.demo-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:0 20px 16px;}
.demo-item{display:flex;justify-content:space-between;font-size:12px;padding:6px 0;border-bottom:1px solid #f5f5f5;}
.demo-item:last-child{border-bottom:none;}
.demo-lbl{color:#777;}
.demo-val{font-weight:700;color:#333;}

.comments-section{padding:16px 20px;border-top:1px solid #f0f0f0;}
.comments-section h5{font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;}
.comment-item{background:#fafafa;border:1px solid #f0f0f0;border-left:3px solid var(--red);border-radius:0 8px 8px 0;padding:10px 14px;margin-bottom:8px;}
.comment-meta{font-size:11px;color:#aaa;margin-bottom:5px;display:flex;gap:10px;flex-wrap:wrap;}
.comment-text{font-size:13px;color:#444;line-height:1.5;}
</style>
</head>
<body>
<div class="app-shell">

  <!-- ══ SIDEBAR ══ -->
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
    <div class="sb-dept-badge">
      <div class="sb-dept-badge-icon"><i class="bi bi-building"></i></div>
      <div>
        <div class="sb-dept-badge-name"><?= htmlspecialchars($deptInfo['name'] ?? 'Department') ?></div>
        <div class="sb-dept-badge-code"><?= htmlspecialchars($dept_code) ?></div>
      </div>
    </div>
    <div class="sb-role">
      <div class="role-dot"></div>
      <div>
        <div class="role-name"><?= htmlspecialchars(CURRENT_USER) ?></div>
        <div class="role-sub">Department User</div>
      </div>
    </div>
    <div class="sb-section">My Department</div>
    <ul class="sb-nav">
      <li><a href="dept_dashboard.php"><span class="nav-icon"><i class="bi bi-speedometer2"></i></span> My Dashboard</a></li>
      <li><a href="dept_feedback.php"><span class="nav-icon"><i class="bi bi-clipboard-check"></i></span> Feedback Inbox <span class="nav-badge" id="sbFeedbackCount">0</span></a></li>
      <li><a href="dept_qrcode.php"><span class="nav-icon"><i class="bi bi-qr-code"></i></span> My QR Code</a></li>
    </ul>
    <div class="sb-section">Reports</div>
    <ul class="sb-nav">
      <li><a href="dept_csmr.php" class="active"><span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span> Generate CSMR</a></li>
      <li><a href="dept_analytics.php"><span class="nav-icon"><i class="bi bi-bar-chart-line"></i></span> My Analytics</a></li>
      <li><a href="dept_export.php"><span class="nav-icon"><i class="bi bi-download"></i></span> Export Data</a></li>
    </ul>
    <div class="sb-footer">
      <a href="../php/logout.php" onclick="return confirm('Are you sure you want to sign out?')">
        <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span> Sign Out
      </a>
    </div>
  </aside>

  <!-- ══ MAIN AREA ══ -->
  <div class="main-area">
    <div class="topbar">
      <button class="menu-toggle" id="menuToggle">&#9776;</button>
      <div class="topbar-title">
        Generate CSMR
        <span class="tb-subtitle"><?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></span>
      </div>
      <div class="topbar-actions">
        <button class="tb-btn" onclick="location.reload()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
        <button class="tb-btn primary" id="topbarPrintBtn" onclick="openPrint()" style="display:none">
          <i class="bi bi-printer"></i> Print / PDF
        </button>
        <div class="tb-avatar" id="topbarAvatar" onclick="toggleAvatarDropdown(event)">
          <?= $avatarLetter ?>
          <div class="avatar-dropdown" id="avatarDropdown">
            <div class="av-header">
              <div class="av-name"><?= htmlspecialchars(CURRENT_USER) ?></div>
              <div class="av-role">Department User</div>
              <div style="font-size:.68rem;color:#888;margin-top:1px"><?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></div>
            </div>
            <div class="av-menu">
              <div class="av-divider"></div>
              <a href="../php/logout.php" class="av-item danger" onclick="return confirm('Sign out?')">
                <i class="bi bi-box-arrow-right"></i> Sign Out
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="page-content">
      <div class="live-bar">
        <div class="live-dot"></div>
        <span class="live-text">Live &nbsp;&middot;&nbsp; Client Satisfaction Measurement Report — <?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></span>
        <span class="live-date" id="todayDate"></span>
      </div>

      <div class="csmr-content">

        <!-- ── LEFT: Filter Panel ── -->
        <div class="filter-panel">
          <div class="filter-panel-header">
            <i class="bi bi-file-earmark-bar-graph"></i>
            <div>
              <h2>CSMR Generator</h2>
              <small><?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></small>
            </div>
          </div>
          <div class="filter-body">

            <div class="dept-notice">
              <i class="bi bi-lock-fill"></i>
              <div>
                <strong><?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></strong><br>
                <span style="font-size:11px;opacity:.8">This report is for your department only</span>
              </div>
            </div>

            <div class="filter-group">
              <label><i class="bi bi-calendar3" style="margin-right:4px"></i> Quick Period</label>
              <div class="period-chips">
                <span class="period-chip" data-period="today">Today</span>
                <span class="period-chip" data-period="this_week">This Week</span>
                <span class="period-chip active" data-period="this_month">This Month</span>
                <span class="period-chip" data-period="last_month">Last Month</span>
                <span class="period-chip" data-period="this_quarter">This Quarter</span>
                <span class="period-chip" data-period="this_year">This Year</span>
                <span class="period-chip" data-period="custom">Custom</span>
              </div>
            </div>

            <div id="customDateGroup" style="display:none">
              <div class="filter-group">
                <label>Date From</label>
                <input type="date" id="dateFrom"/>
              </div>
              <div class="filter-group">
                <label>Date To</label>
                <input type="date" id="dateTo"/>
              </div>
            </div>

            <div id="selectedDatesDisplay" class="filter-group">
              <label>Selected Period</label>
              <div id="datesDisplay" style="font-size:12px;color:#555;background:#f5f5f5;padding:8px 12px;border-radius:7px;border:1px solid #efefef"></div>
              <input type="hidden" id="dateFrom" />
              <input type="hidden" id="dateTo" />
            </div>

            <hr class="filter-divider"/>

            <div class="filter-group">
              <label><i class="bi bi-pencil" style="margin-right:4px"></i> Report Title <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#bbb">(optional)</span></label>
              <input type="text" id="reportTitle" placeholder="e.g. Q2 2026 Satisfaction Report"/>
            </div>

            <div class="filter-group">
              <label><i class="bi bi-list-check" style="margin-right:4px"></i> Include in Report</label>
              <div style="display:flex;flex-direction:column;gap:9px;margin-top:4px">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;text-transform:none;letter-spacing:0;color:#444;font-weight:400;cursor:pointer">
                  <input type="checkbox" id="inclCharts" checked style="accent-color:var(--red)"/> Charts &amp; Graphs
                </label>
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;text-transform:none;letter-spacing:0;color:#444;font-weight:400;cursor:pointer">
                  <input type="checkbox" id="inclSQD" checked style="accent-color:var(--red)"/> SQD Score Breakdown
                </label>
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;text-transform:none;letter-spacing:0;color:#444;font-weight:400;cursor:pointer">
                  <input type="checkbox" id="inclComments" checked style="accent-color:var(--red)"/> Recent Comments
                </label>
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;text-transform:none;letter-spacing:0;color:#444;font-weight:400;cursor:pointer">
                  <input type="checkbox" id="inclDemo" style="accent-color:var(--red)"/> Demographics Breakdown
                </label>
              </div>
            </div>

            <hr class="filter-divider"/>

            <button class="btn-generate" id="generateBtn" onclick="generateReport()">
              <i class="bi bi-eye"></i> Preview Report
            </button>
            <button class="btn-print" id="printBtn" onclick="openPrint()">
              <i class="bi bi-printer"></i> Print / Export PDF
            </button>
          </div>
        </div>

        <!-- ── RIGHT: Preview Panel ── -->
        <div class="preview-panel">

          <!-- Stat cards -->
          <div id="statCards" style="display:none">
            <div class="csmr-stats">
              <div class="csmr-stat">
                <div class="csmr-stat-icon red"><i class="bi bi-people-fill"></i></div>
                <div><div class="csmr-stat-val" id="statTotal">0</div><div class="csmr-stat-label">Total Respondents</div></div>
              </div>
              <div class="csmr-stat">
                <div class="csmr-stat-icon green"><i class="bi bi-emoji-smile-fill"></i></div>
                <div><div class="csmr-stat-val" id="statSat">0%</div><div class="csmr-stat-label">Satisfaction Rate</div></div>
              </div>
              <div class="csmr-stat">
                <div class="csmr-stat-icon blue"><i class="bi bi-star-fill"></i></div>
                <div><div class="csmr-stat-val" id="statAvg">0</div><div class="csmr-stat-label">Avg. Rating</div></div>
              </div>
              <div class="csmr-stat">
                <div class="csmr-stat-icon gold"><i class="bi bi-bar-chart-fill"></i></div>
                <div><div class="csmr-stat-val" id="statSqd">0</div><div class="csmr-stat-label">SQD Avg Score</div></div>
              </div>
            </div>
          </div>

          <!-- Preview card -->
          <div class="preview-card">
            <div class="preview-card-header">
              <h3><i class="bi bi-file-earmark-bar-graph" style="color:var(--red);margin-right:7px"></i> Report Preview</h3>
              <div id="periodBadgeWrap" style="display:none">
                <div class="period-badge"><i class="bi bi-calendar-range"></i><span id="periodBadgeText">—</span></div>
              </div>
            </div>

            <div class="preview-empty" id="previewEmpty">
              <i class="bi bi-file-earmark-text"></i>
              <h4>No Report Generated Yet</h4>
              <p>Select a period and click <strong>Preview Report</strong><br>to generate your CSMR.</p>
            </div>

            <div class="preview-spinner" id="previewSpinner">
              <div class="spin-circle"></div>
              <p>Fetching feedback data…</p>
            </div>

            <div id="chartsSection" style="display:none">
              <div class="csmr-charts">
                <div class="chart-section">
                  <h5>Rating Distribution</h5>
                  <div id="ratingBars"></div>
                </div>
              </div>
            </div>

            <!-- SQD section — independent of charts -->
            <div id="sqdSection" style="display:none">
              <div style="padding:0 20px 16px;border-top:1px solid #f0f0f0">
                <h5 style="font-size:12px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.05em;margin:14px 0 10px">SQD Scores</h5>
                <div id="sqdBars"></div>
              </div>
            </div>

            <div id="demoSection" style="display:none">
              <hr style="margin:0;border-color:#f0f0f0"/>
              <div class="demo-grid">
                <div>
                  <div style="font-size:11px;font-weight:700;color:#888;margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em">By Respondent Type</div>
                  <div id="demoType"></div>
                </div>
                <div>
                  <div style="font-size:11px;font-weight:700;color:#888;margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em">By Age Group</div>
                  <div id="demoAge"></div>
                </div>
              </div>
            </div>

            <div id="commentsSection" style="display:none">
              <div class="comments-section">
                <h5>Recent Comments</h5>
                <div id="commentsList"></div>
              </div>
            </div>

          </div><!-- /preview-card -->
        </div><!-- /preview-panel -->

      </div><!-- /csmr-content -->
    </div><!-- /page-content -->
  </div><!-- /main-area -->
</div><!-- /app-shell -->

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/jquery-4.0.0.min.js"></script>
<script src="../assets/js/mobile_toggle.js"></script>
<script>
const DEPT_CODE = <?= json_encode($dept_code) ?>;
const DEPT_NAME = <?= json_encode($deptInfo['name'] ?? $dept_code) ?>;

let lastReportData = null;
let selectedFrom   = '';
let selectedTo     = '';

// ── Init ──
document.getElementById('todayDate').textContent =
  new Date().toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});
document.getElementById('menuToggle')?.addEventListener('click',()=>
  document.getElementById('sidebar').classList.toggle('sb-open'));
function toggleAvatarDropdown(e){e.stopPropagation();document.getElementById('avatarDropdown').classList.toggle('show');}
document.addEventListener('click',()=>document.getElementById('avatarDropdown')?.classList.remove('show'));

// Sidebar badge
$.get('../php/get/get_feedback.php',{dept:DEPT_CODE,per_page:1,page:1},function(res){
  if(res.success) $('#sbFeedbackCount').text(res.summary.total||0);
});

// Set default to this month
setDateRange('this_month');

// Period chips
document.querySelectorAll('.period-chip').forEach(chip=>{
  chip.addEventListener('click',function(){
    document.querySelectorAll('.period-chip').forEach(c=>c.classList.remove('active'));
    this.classList.add('active');
    const period=this.dataset.period;
    const customGroup=document.getElementById('customDateGroup');
    const displayGroup=document.getElementById('selectedDatesDisplay');
    if(period==='custom'){
      customGroup.style.display='block';
      displayGroup.style.display='none';
    } else {
      customGroup.style.display='none';
      displayGroup.style.display='block';
      setDateRange(period);
    }
  });
});

function setDateRange(period){
  const n=new Date(); let from,to;
  switch(period){
    case 'today':        from=to=new Date(n.getFullYear(),n.getMonth(),n.getDate()); break;
    case 'this_week':    {const d=n.getDay()===0?6:n.getDay()-1;from=new Date(n);from.setDate(n.getDate()-d);to=new Date(n);to.setDate(n.getDate()+(6-d));break;}
    case 'this_month':   from=new Date(n.getFullYear(),n.getMonth(),1);to=new Date(n.getFullYear(),n.getMonth()+1,0);break;
    case 'last_month':   from=new Date(n.getFullYear(),n.getMonth()-1,1);to=new Date(n.getFullYear(),n.getMonth(),0);break;
    case 'this_quarter': {const q=Math.floor(n.getMonth()/3);from=new Date(n.getFullYear(),q*3,1);to=new Date(n.getFullYear(),q*3+3,0);break;}
    case 'this_year':    from=new Date(n.getFullYear(),0,1);to=new Date(n.getFullYear(),11,31);break;
    default: return;
  }
  selectedFrom=from.toISOString().split('T')[0];
  selectedTo=to.toISOString().split('T')[0];
  const fmt=d=>new Date(d).toLocaleDateString('en-PH',{month:'long',day:'numeric',year:'numeric'});
  document.getElementById('datesDisplay').textContent=`${fmt(selectedFrom)} – ${fmt(selectedTo)}`;
}

// ── Generate Report ──
function generateReport(){
  // Get dates
  const customGroup=document.getElementById('customDateGroup');
  if(customGroup.style.display!=='none'){
    selectedFrom=document.querySelector('#customDateGroup #dateFrom')?.value||'';
    selectedTo=document.querySelector('#customDateGroup #dateTo')?.value||'';
  }
  if(!selectedFrom||!selectedTo){alert('Please select a date range.');return;}

  document.getElementById('previewEmpty').style.display='none';
  document.getElementById('previewSpinner').style.display='block';
  document.getElementById('statCards').style.display='none';
  document.getElementById('chartsSection').style.display='none';
  document.getElementById('sqdSection').style.display='none';
  document.getElementById('demoSection').style.display='none';
  document.getElementById('commentsSection').style.display='none';
  document.getElementById('periodBadgeWrap').style.display='none';

  const btn=document.getElementById('generateBtn');
  btn.disabled=true;
  btn.innerHTML='<i class="bi bi-hourglass-split spin-anim"></i> Generating…';

  $.ajax({
    url: '../php/get/get_csmr_data.php',
    method: 'POST',
    dataType: 'json',
    data:{dept_id:DEPT_CODE,date_from:selectedFrom,date_to:selectedTo,incl_raw:0},
    success(res){
      if(!res.success){alert('Error: '+(res.message||'Failed.'));resetPreview();return;}
      lastReportData=res;
      renderPreview(res);
      document.getElementById('printBtn').classList.add('show');
      document.getElementById('topbarPrintBtn').style.display='flex';
    },
    error(xhr){console.error(xhr.responseText);resetPreview();},
    complete(){
      document.getElementById('previewSpinner').style.display='none';
      btn.disabled=false;
      btn.innerHTML='<i class="bi bi-eye"></i> Preview Report';
    }
  });
}

function resetPreview(){
  document.getElementById('previewSpinner').style.display='none';
  document.getElementById('previewEmpty').style.display='block';
}

function renderPreview(res){
  const s=res.summary;

  $('#statTotal').text(s.total_responses||0);
  $('#statSat').text((s.satisfaction_rate||0)+'%');
  $('#statAvg').text(parseFloat(s.avg_rating||0).toFixed(1));

  const sqdKeys=['avg_sqd0','avg_sqd1','avg_sqd2','avg_sqd3','avg_sqd4','avg_sqd5','avg_sqd6','avg_sqd7','avg_sqd8'];
  const sqdVals=sqdKeys.map(k=>parseFloat(s[k]||0)).filter(v=>v>0);
  $('#statSqd').text(sqdVals.length?(sqdVals.reduce((a,b)=>a+b,0)/sqdVals.length).toFixed(2):'—');

  const fmt=d=>new Date(d).toLocaleDateString('en-PH',{month:'short',day:'numeric',year:'numeric'});
  $('#periodBadgeText').text(`${fmt(selectedFrom)} – ${fmt(selectedTo)}`);
  $('#statCards').css('display','grid');
  $('#periodBadgeWrap').css('display','flex');

  if(document.getElementById('inclCharts').checked){
    renderRatingBars(s);
    $('#chartsSection').show();
  }

  // SQD is independent — shows even without charts
  if(document.getElementById('inclSQD').checked){
    renderSQDBars(s);
    $('#sqdSection').show();
  }

  if(document.getElementById('inclDemo').checked&&(res.by_type||res.by_age)){
    renderDemo(res);
    $('#demoSection').show();
  }

  if(document.getElementById('inclComments').checked&&res.recent_comments?.length){
    renderComments(res.recent_comments);
    $('#commentsSection').show();
  }
}

function renderRatingBars(s){
  const total=parseInt(s.total_responses)||1;
  const ratings=[
    {label:'Excellent (5★)',count:parseInt(s.cnt_5||0),color:'#1e7c3b'},
    {label:'Good (4★)',     count:parseInt(s.cnt_4||0),color:'#1a6fbf'},
    {label:'Average (3★)', count:parseInt(s.cnt_3||0),color:'#b06c10'},
    {label:'Poor (2★)',     count:parseInt(s.cnt_2||0),color:'#c0392b'},
    {label:'Very Poor (1★)',count:parseInt(s.cnt_1||0),color:'#922b21'},
  ];
  document.getElementById('ratingBars').innerHTML=ratings.map(r=>{
    const pct=Math.round(r.count/total*100);
    return `<div class="rating-bar-row">
      <div class="rbl">${r.label}</div>
      <div class="rbw"><div class="rbf" style="width:${pct}%;background:${r.color}"></div></div>
      <div class="rbc" style="color:${r.color}">${r.count} (${pct}%)</div>
    </div>`;
  }).join('');
}

function renderSQDBars(s){
  const sqds=[0,1,2,3,4,5,6,7,8].map(i=>({key:`avg_sqd${i}`,label:`SQD${i}`}));
  document.getElementById('sqdBars').innerHTML=sqds.map(q=>{
    const val=parseFloat(s[q.key]||0);
    const pct=Math.round(val/5*100);
    const col=val>=4?'#1e7c3b':val>=3?'#1a6fbf':val>=2?'#b06c10':'#c0392b';
    return `<div class="sqd-bar-row">
      <div class="sbl">${q.label}</div>
      <div class="sbw"><div class="sbf" style="width:${pct}%;background:${col}"></div></div>
      <div class="sbc" style="color:${col}">${val>0?val.toFixed(2):'—'}</div>
    </div>`;
  }).join('');
}

function renderDemo(res){
  const typeMap={citizen:'Citizen',employee:'Employee',business_owner:'Business Owner',other:'Other'};
  const ageMap={below_18:'Below 18','18_30':'18–30','31_45':'31–45','46_60':'46–60',above_60:'Above 60'};
  document.getElementById('demoType').innerHTML=(res.by_type||[]).map(t=>`
    <div class="demo-item"><span class="demo-lbl">${typeMap[t.respondent_type]||t.respondent_type}</span><span class="demo-val">${t.total}</span></div>`).join('')||'<div style="color:#bbb;font-size:12px">No data</div>';
  document.getElementById('demoAge').innerHTML=(res.by_age||[]).map(a=>`
    <div class="demo-item"><span class="demo-lbl">${ageMap[a.age_group]||a.age_group}</span><span class="demo-val">${a.total}</span></div>`).join('')||'<div style="color:#bbb;font-size:12px">No data</div>';
}

function renderComments(comments){
  const stars=r=>'★'.repeat(Math.round(r))+'☆'.repeat(5-Math.round(r));
  document.getElementById('commentsList').innerHTML=comments.slice(0,5).map(c=>`
    <div class="comment-item">
      <div class="comment-meta">
        <span style="color:#c8991a">${stars(c.rating)} ${c.rating}/5</span>
        <span style="text-transform:capitalize">${(c.respondent_type||'').replace('_',' ')}</span>
        <span>${c.submitted_at||''}</span>
      </div>
      <div class="comment-text">"${escHtml(c.comment)}"</div>
    </div>`).join('')||'<p style="color:#bbb;font-size:13px">No comments for this period.</p>';
}

function openPrint(){
  if(!lastReportData){alert('Please generate a report first.');return;}
  const params=new URLSearchParams({
    dept_id:       DEPT_CODE,
    dept_name:     DEPT_NAME,
    date_from:     selectedFrom,
    date_to:       selectedTo,
    title:         document.getElementById('reportTitle').value,
    incl_comments: document.getElementById('inclComments').checked?1:0,
    incl_charts:   document.getElementById('inclCharts').checked?1:0,
  });
  window.open('../admin/admin_csmr_generator_print.php?'+params.toString(),'_blank');
}

function escHtml(s){if(!s)return'';return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
</script>
</body>
</html>