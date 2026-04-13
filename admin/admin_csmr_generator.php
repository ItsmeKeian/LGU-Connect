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
<title>All Feedback | LGU-Connect</title>
<link rel="icon" href="../assets/img/logo.png" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap-icons.min.css"/>
<link rel="stylesheet" href="../assets/css/sidebar_header.css"/>


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
      <li><a href="admin_dashboard.php">
        <span class="nav-icon"><i class="bi bi-speedometer2"></i></span> Dashboard
      </a></li>
      <li><a href="admin_departments.php">
        <span class="nav-icon"><i class="bi bi-building"></i></span> Departments
      </a></li>
      <li><a href="admin_allfeedback.php">
        <span class="nav-icon"><i class="bi bi-clipboard-check"></i></span> All Feedback
        <span class="nav-badge" id="sbFeedbackCount">0</span>
      </a></li>
    </ul>
    <div class="sb-section">Reports</div>
    <ul class="sb-nav">
      <li><a href="admin_csmr_generator.php" class="active">
        <span class="nav-icon"><i class="bi bi-file-earmark-text"></i></span> CSMR Generator
      </a></li>
      <li><a href="admin_analytics.php">
        <span class="nav-icon"><i class="bi bi-bar-chart-line"></i></span> Analytics
      </a></li>
      <li><a href="admin_exportdata.php">
        <span class="nav-icon"><i class="bi bi-download"></i></span> Export Data
      </a></li>
    </ul>
    <div class="sb-section">System</div>
    <ul class="sb-nav">
      <li><a href="admin_manage_users.php">
        <span class="nav-icon"><i class="bi bi-people"></i></span> Manage Users
      </a></li>
      <li><a href="admin_qrcodes.php">
        <span class="nav-icon"><i class="bi bi-qr-code"></i></span> QR Codes
      </a></li>
      <li><a href="admin_settings.php">
        <span class="nav-icon"><i class="bi bi-gear"></i></span> Settings
      </a></li>
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
        All Feedback
        <span class="tb-subtitle">System-Wide Records</span>
      </div>
      <div class="topbar-actions">
        <button class="tb-btn" id="refreshBtn">
          <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
        <button class="tb-btn primary" onclick="location.href='csmr_generator.php'">
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
              <a href="settings.php" class="av-item"><i class="bi bi-person-circle"></i> My Profile</a>
              <a href="settings.php" class="av-item"><i class="bi bi-gear"></i> Settings</a>
              <div class="av-divider"></div>
              <a href="../php/logout.php" class="av-item danger"
                 onclick="return confirm('Sign out?')">
                <i class="bi bi-box-arrow-right"></i> Sign Out
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════ PAGE CONTENT ══════════ -->
    <div class="page-content">

      <!-- Live bar -->
      <div class="live-bar">
        <div class="live-dot"></div>
        <span class="live-text">Live &nbsp;&middot;&nbsp; All submitted feedback across departments</span>
        <span class="live-date" id="todayDate"></span>
      </div>

    



    </div>
    <!-- /page-content -->
  </div>
</div>


<!-- Scripts -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/jquery-4.0.0.min.js"></script>
<script src="../js/admin/admin_sidebarcount.js"></script>


</body>
</html>