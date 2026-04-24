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
<title>Predictive Analytics | <?= htmlspecialchars($deptInfo['name'] ?? 'Department') ?></title>
<link rel="icon" href="../assets/img/logo.png" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="../assets/css/bootstrap-icons.min.css"/>
<link rel="stylesheet" href="../assets/css/sidebar_header.css"/>
<link rel="stylesheet" href="../assets/css/dept_predictive.css"/>
</head>
<body>
<div class="app-shell">

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
      <li><a href="dept_csmr.php"><span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span> Generate CSMR</a></li>
      <li><a href="dept_predictive.php" class="active"><span class="nav-icon"><i class="bi bi-graph-up-arrow"></i></span> Predictive Analytics</a></li>
      <li><a href="dept_export.php"><span class="nav-icon"><i class="bi bi-download"></i></span> Export Data</a></li>
    </ul>
    <div class="sb-footer">
      <a href="../php/logout.php" onclick="return confirm('Sign out?')">
        <span class="nav-icon"><i class="bi bi-box-arrow-right"></i></span> Sign Out
      </a>
    </div>
  </aside>

  <div class="main-area">
    <div class="topbar">
      <button class="menu-toggle" id="menuToggle">&#9776;</button>
      <div class="topbar-title">
        Predictive Analytics
        <span class="tb-subtitle"><?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></span>
      </div>
      <div class="topbar-actions">
        <button class="tb-btn" onclick="loadPredictions()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
        <button class="tb-btn primary" onclick="location.href='dept_csmr.php'">
          <i class="bi bi-file-earmark-text"></i> Generate CSMR
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
        <span class="live-text">Live &nbsp;·&nbsp; Predictive Analytics for <strong><?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></strong> only</span>
        <span class="live-date" id="todayDate"></span>
      </div>

      <div class="pa-content">

        <!-- Health card -->
        <div id="healthCard" style="display:none">
          <div class="health-card">
            <div class="health-left">
              <h2><i class="bi bi-activity me-2"></i> Department Service Health</h2>
              <h1 id="healthLabel">—</h1>
              <p id="healthRec">Loading…</p>
              <div class="dept-locked">
                <i class="bi bi-lock-fill"></i>
                Showing predictions for <?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?> only
              </div>
            </div>
            <div class="health-badge">
              <div class="hb-val" id="healthScore">—</div>
              <div class="hb-label">Health Score</div>
            </div>
          </div>
        </div>

        <!-- Loading state -->
        <div class="pa-card" id="loadingCard">
          <div class="pa-empty">
            <i class="bi bi-graph-up-arrow"></i>
            <h4>Loading Predictions…</h4>
            <p>Please wait while we analyze your department's data.</p>
          </div>
        </div>

        <div id="paContent" style="display:none">

          <!-- 3 Prediction Cards -->
          <div class="grid-3" id="predCards"></div>

          <!-- Trend Chart + Risk Status -->
          <div class="grid-2">
            <div class="pa-card">
              <div class="pa-card-header">
                <h4><i class="bi bi-graph-up-arrow"></i> Satisfaction Trend Forecast</h4>
                <span class="method-badge"><i class="bi bi-calculator"></i> WMA</span>
              </div>
              <div class="pa-card-body">
                <div class="chart-wrap"><canvas id="chartTrend"></canvas></div>
              </div>
            </div>
            <div class="pa-card">
              <div class="pa-card-header">
                <h4><i class="bi bi-exclamation-triangle-fill"></i> Department Risk Status</h4>
                <span class="method-badge"><i class="bi bi-calculator"></i> Decline Detection</span>
              </div>
              <div class="pa-card-body" id="riskStatusBox"></div>
            </div>
          </div>

          <!-- SQD Analysis -->
          <div class="grid-full">
            <div class="pa-card">
              <div class="pa-card-header">
                <h4><i class="bi bi-clipboard-data-fill"></i> SQD Weak Point Detector</h4>
                <span class="method-badge"><i class="bi bi-calculator"></i> Threshold Analysis</span>
              </div>
              <div class="pa-card-body" id="sqdAnalysisList"></div>
            </div>
          </div>

          <!-- Recommendation -->
          <div class="recommendation-box">
            <i class="bi bi-lightbulb-fill"></i>
            <p id="recommendationText">—</p>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<div class="spinner-overlay" id="spinnerOverlay">
  <div class="spinner-box">
    <div class="spin-circle"></div>
    <p>Running predictions for your department…</p>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/jquery-4.0.0.min.js"></script>
<script>
const DEPT_CODE = <?= json_encode($dept_code) ?>;
let chartTrend = null;


</script>
<script src="../js/department/dept_predictive.js"></script>
</body>
</html>