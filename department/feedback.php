
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>LGU-Connect | Municipality of San Julian</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="icon" href="../assets/img/logo.png" type="image/x-icon">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/dashboard.css"/>
</head>
<body>
<div class="app-shell">

  <!-- ══════════════ SIDEBAR ══════════════ -->
  <aside class="sidebar" id="sidebar">

    <div class="sb-brand">
      <img src="../assets/img/san_julian_logo.png" class="sb-logo-img" alt="Logo"
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
        <div class="role-name" id="sidebarUserName"></div>
        <div class="role-sub">Department Administrator</div>
      </div>
    </div>

    <div class="sb-section">My Department</div>
    <ul class="sb-nav">
      <li><a href="dept_dashboard.php" >
        <span class="nav-icon">&#9962;</span> My Dashboard
      </a></li>
      <li><a href="feedback_inbox.php" class="active">
        <span class="nav-icon">&#128203;</span> Feedback Inbox
        <span class="nav-badge" id="sbFeedbackCount">0</span>
      </a></li>
      <li><a href="qrcode.php">
        <span class="nav-icon">&#9636;</span> My QR Code
      </a></li>
    </ul>

    <div class="sb-section">Reports</div>
    <ul class="sb-nav">
      <li><a href="my_csmr.php">
        <span class="nav-icon">&#128196;</span> Generate CSMR
      </a></li>
      <li><a href="analytics.php">
        <span class="nav-icon">&#128200;</span> My Analytics
      </a></li>
      <li><a href="export.php">
        <span class="nav-icon">&#128228;</span> Export Data
      </a></li>
    </ul>

    <div class="sb-section">Account</div>
    <ul class="sb-nav">
      <li><a href="profile.php">
        <span class="nav-icon">&#128100;</span> My Profile
      </a></li>
      <li><a href="settings.php">
        <span class="nav-icon">&#9881;</span> Settings
      </a></li>
    </ul>

    <div class="sb-footer">
      <a href="../logout.php">
        <span class="nav-icon">&#10548;</span> Sign Out
      </a>
    </div>

  </aside>
  <!-- /SIDEBAR -->

  <!-- ══════════════ MAIN AREA ══════════════ -->
  <div class="main-area">

    <!-- Topbar -->
    <div class="topbar">
      <button class="menu-toggle" id="menuToggle">&#9776;</button>
      <div class="topbar-title" id="topbarTitle">
        
        <span class="tb-subtitle">Department Dashboard</span>
      </div>
      <div class="topbar-actions">
        <div class="search-wrap">
          <span class="search-icon">&#128269;</span>
          <input type="text" class="tb-search" id="fbSearch" placeholder="Search feedback..."/>
        </div>
        <button class="tb-btn" id="refreshBtn">&#8635; Refresh</button>
        <button class="tb-btn primary" onclick="location.href='my_csmr.php'">
          &#128196; Generate CSMR
        </button>
        <div class="tb-avatar" id="topbarAvatar"></div>
      </div>
    </div>

    <!-- Page content -->
    <div class="page-content">

      <!-- Live bar -->
      <div class="live-bar">
        <div class="live-dot" id="liveDot"></div>
        <span class="live-text">
          Live &nbsp;&middot;&nbsp; Last updated: <span id="lastUpdated">just now</span>
        </span>
        <span class="live-date" id="todayDate"></span>
      </div>

      
      
      

    
      <!-- /bottom grid -->

    </div>
    <!-- /page-content -->

  </div>
  <!-- /main-area -->

</div>
<!-- /app-shell -->

<!-- Hidden: pass PHP session data to JS -->
<script>
  const DEPT_ID   = <?= (int)$deptId ?>;
  const DEPT_NAME = <?= json_encode($userName) ?>;
</script>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script src="../js/dept_dashboard.js"></script>

</body>
</html>