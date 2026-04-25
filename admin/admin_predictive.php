<?php
require "../php/auth_check.php";
requireSuperAdmin();
require "../php/dbconnect.php";

$avatarLetter = strtoupper(substr(CURRENT_USER, 0, 1));

// Get all departments for filter
$depts = $conn->query("SELECT code, name FROM departments WHERE status='active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Predictive Analytics | LGU-Connect</title>
<link rel="icon" href="../assets/img/logo.png" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="../assets/css/bootstrap-icons.min.css"/>
<link rel="stylesheet" href="../assets/css/sidebar_header.css"/>
<link rel="stylesheet" href="../assets/css/admin_predictive.css"/>
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
      <li><a href="admin_predictive.php" class="active"><span class="nav-icon"><i class="bi bi-graph-up-arrow"></i></span> Predictive Analytics</a></li>
      <li><a href="admin_exportdata.php"><span class="nav-icon"><i class="bi bi-download"></i></span> Export Data</a></li>
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

  <!-- ══ MAIN AREA ══ -->
  <div class="main-area">
    <div class="topbar">
      <button class="menu-toggle" id="menuToggle">&#9776;</button>
      <div class="topbar-title">
        Predictive Analytics
        <span class="tb-subtitle">Statistical Forecasting & Risk Detection</span>
      </div>
      <div class="topbar-actions">
        <button class="tb-btn" onclick="loadPredictions()"><i class="bi bi-arrow-clockwise"></i> Refresh</button>
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
        <span class="live-text">Live &nbsp;·&nbsp; Predictive Analytics — Statistical Trend Analysis &amp; Forecasting</span>
        <span class="live-date" id="todayDate"></span>
      </div>

      <div class="pa-content">

        <!-- Health Score (hidden until loaded) -->
        <div id="healthCard" style="display:none">
          <div class="health-card">
            <div class="health-left">
              <h2><i class="bi bi-activity me-2"></i> Overall Service Health</h2>
              <h1 id="healthLabel">—</h1>
              <p id="healthRec">Loading recommendation…</p>
            </div>
            <div class="health-badge">
              <div class="hb-val" id="healthScore">—</div>
              <div class="hb-label">Health Score</div>
            </div>
          </div>
        </div>

        <!-- Filter bar -->
        <div class="filter-bar">
          <label><i class="bi bi-funnel me-1"></i> Filter:</label>
          <select id="filterDept">
            <option value="">All Departments (System-Wide)</option>
            <?php foreach ($depts as $d): ?>
            <option value="<?= htmlspecialchars($d['code']) ?>"><?= htmlspecialchars($d['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn-load-pa" id="loadBtn" onclick="loadPredictions()">
            <i class="bi bi-graph-up-arrow"></i> Load Predictions
          </button>
          <span style="font-size:11px;color:#aaa;margin-left:auto">
            <i class="bi bi-info-circle me-1"></i>
            Uses last 6 months of data · Weighted Moving Average method
          </span>
        </div>

        <!-- Empty state -->
        <div class="pa-card" id="emptyState">
          <div class="pa-empty">
            <i class="bi bi-graph-up-arrow"></i>
            <h4>No Predictions Loaded</h4>
            <p>Click <strong>Load Predictions</strong> to generate analytics</p>
          </div>
        </div>

        <!-- Main content (hidden until loaded) -->
        <div id="paContent" style="display:none">

          <!-- 3 Prediction Cards -->
          <div class="grid-3" id="predCards"></div>

          <!-- Trend Chart + Risk Alerts -->
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
                <h4><i class="bi bi-exclamation-triangle-fill"></i> Department Risk Alerts</h4>
                <span class="method-badge"><i class="bi bi-calculator"></i> Decline Detection</span>
              </div>
              <div class="pa-card-body" id="riskAlertsList">
                <div class="pa-empty" style="padding:20px">
                  <i class="bi bi-shield-check" style="font-size:32px;opacity:.4;display:block;margin-bottom:8px"></i>
                  <p style="font-size:13px;color:#bbb">No risk alerts detected</p>
                </div>
              </div>
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
          <div class="grid-full">
            <div class="recommendation-box" id="recommendationBox">
              <i class="bi bi-lightbulb-fill"></i>
              <p id="recommendationText">—</p>
            </div>
          </div>

        </div><!-- /paContent -->
      </div>
    </div>
  </div>
</div>

<div class="spinner-overlay" id="spinnerOverlay">
  <div class="spinner-box">
    <div class="spin-circle"></div>
    <p>Running predictions…</p>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/jquery-4.0.0.min.js"></script>
<script src="../assets/js/admin_sidebarcount.js"></script>
<script src="../js/admin/admin_predictive.js"></script>
</body>
</html>