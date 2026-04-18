<?php
require "../php/auth_check.php";
requireDeptUser();

require "../php/dbconnect.php";

$avatarLetter = strtoupper(substr(CURRENT_USER, 0, 1));
$dept_code    = CURRENT_DEPT;

// Get department info
$deptStmt = $conn->prepare("SELECT * FROM departments WHERE code = ? LIMIT 1");
$deptStmt->execute([$dept_code]);
$deptInfo = $deptStmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($deptInfo['name'] ?? 'Department') ?> | LGU-Connect</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="icon" href="../assets/img/logo.png" type="image/x-icon">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="../assets/css/bootstrap-icons.min.css"/>
<link rel="stylesheet" href="../assets/css/sidebar_header.css"/>
<link rel="stylesheet" href="../assets/css/admin_dashboard.css"/>

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

    <!-- Department badge -->
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
      <li><a href="dept_dashboard.php" >
        <span class="nav-icon"><i class="bi bi-speedometer2"></i></span> My Dashboard
      </a></li>
      <li><a href="dept_feedback.php">
        <span class="nav-icon"><i class="bi bi-clipboard-check"></i></span> Feedback Inbox
        <span class="nav-badge" id="sbFeedbackCount">0</span>
      </a></li>
      <li><a href="dept_qrcode.php" class="active">
        <span class="nav-icon"><i class="bi bi-qr-code"></i></span> My QR Code
      </a></li>
    </ul>

    <div class="sb-section">Reports</div>
    <ul class="sb-nav">
      <li><a href="dept_csmr.php">
        <span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span> Generate CSMR
      </a></li>
      <li><a href="dept_analytics.php">
        <span class="nav-icon"><i class="bi bi-bar-chart-line"></i></span> My Analytics
      </a></li>
      <li><a href="dept_export.php">
        <span class="nav-icon"><i class="bi bi-download"></i></span> Export Data
      </a></li>
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
        My Dashboard
        <span class="tb-subtitle"><?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></span>
      </div>
      <div class="topbar-actions">
        <button class="tb-btn" id="refreshBtn">
          <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="tb-btn primary" onclick="location.href='dept_csmr.php'">
          <i class="bi bi-file-earmark-text"></i> Generate CSMR
        </button>
        <div class="tb-avatar" id="topbarAvatar" onclick="toggleAvatarDropdown(event)">
          <?= $avatarLetter ?>
          <div class="avatar-dropdown" id="avatarDropdown">
            <div class="av-header">
              <div class="av-name"><?= htmlspecialchars(CURRENT_USER) ?></div>
              <div class="av-role">Department User</div>
              <div style="font-size:.68rem;color:#888;margin-top:1px">
                <?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?>
              </div>
            </div>
            <div class="av-menu">
              <div class="av-divider"></div>
              <a href="../php/logout.php" class="av-item danger"
                 onclick="return confirm('Are you sure you want to sign out?')">
                <i class="bi bi-box-arrow-right"></i> Sign Out
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PAGE CONTENT -->
    <div class="page-content">

      <div class="live-bar">
        <div class="live-dot"></div>
        <span class="live-text">
          Live &nbsp;&middot;&nbsp; Last updated: <span id="lastUpdated">just now</span>
        </span>
        <span class="live-date" id="todayDate"></span>
      </div>

     
     

     

          </div>
        </div>

      </div>

    </div><!-- /page-content -->
  </div><!-- /main-area -->
</div><!-- /app-shell -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/jquery-4.0.0.min.js"></script>
<script src="../assets/js/mobile_toggle.js"></script>



</body>
</html>