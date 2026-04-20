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

// Feedback URL
$feedback_url = "http://{$_SERVER['HTTP_HOST']}/lgu-connect/feedback.php?dept={$dept_code}";

// Quick stats
$statsStmt = $conn->prepare("
    SELECT
        COUNT(*) AS total,
        ROUND(AVG(rating), 2) AS avg_rating,
        ROUND(SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(*),0), 1) AS satisfaction_rate,
        SUM(CASE WHEN DATE(submitted_at) = CURDATE() THEN 1 ELSE 0 END) AS today
    FROM feedback WHERE department_code = ?
");
$statsStmt->execute([$dept_code]);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>My QR Code | <?= htmlspecialchars($deptInfo['name'] ?? 'Department') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="icon" href="../assets/img/logo.png" type="image/x-icon">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="../assets/css/bootstrap-icons.min.css"/>
<link rel="stylesheet" href="../assets/css/sidebar_header.css"/>
<link rel="stylesheet" href="../assets/css/admin_dashboard.css"/>
<link rel="stylesheet" href="../assets/css/dept_qrcode.css"/>
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
        My QR Code
        <span class="tb-subtitle"><?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></span>
      </div>
      <div class="topbar-actions">
        <button class="tb-btn" onclick="window.print()">
          <i class="bi bi-printer"></i> Print QR
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
        <span class="live-text">Live &nbsp;&middot;&nbsp; Your department's citizen feedback QR code</span>
        <span class="live-date" id="todayDate"></span>
      </div>

      <div class="qr-content">

        <!-- ── LEFT: QR Code Card ── -->
        <div class="qr-main-card">
          <div class="qr-card-top">
            <h2><?= htmlspecialchars($deptInfo['name'] ?? $dept_code) ?></h2>
            <p>Citizen Feedback QR Code</p>
            <div class="qr-dept-code-badge"><?= htmlspecialchars($dept_code) ?></div>
          </div>

          <div class="qr-image-wrap">

            <!-- Size selector -->
            <div class="qr-size-tabs">
              <button class="qr-size-tab" onclick="changeSize(150, this)">Small</button>
              <button class="qr-size-tab active" onclick="changeSize(220, this)">Medium</button>
              <button class="qr-size-tab" onclick="changeSize(300, this)">Large</button>
            </div>

            <!-- QR Image -->
            <div class="qr-image-box">
              <img id="qrImage" src="" alt="QR Code" width="220" height="220"/>
            </div>

            <!-- Feedback URL -->
            <div class="qr-link-box">
              <i class="bi bi-link-45deg" style="color:#8B1A1A;font-size:18px;flex-shrink:0"></i>
              <div class="link-text" id="feedbackUrlText"><?= htmlspecialchars($feedback_url) ?></div>
              <button class="copy-btn" id="copyBtn" onclick="copyLink()">
                <i class="bi bi-clipboard"></i> Copy
              </button>
            </div>

            <!-- Action buttons -->
            <div class="qr-actions">
              <a id="downloadBtn" href="#" download="QR_<?= htmlspecialchars($dept_code) ?>.png"
                 class="qr-action-btn download">
                <i class="bi bi-download"></i> Download QR
              </a>
              <button onclick="window.print()" class="qr-action-btn print">
                <i class="bi bi-printer"></i> Print QR
              </button>
            </div>

            <div style="font-size:11px;color:#aaa;text-align:center;line-height:1.5">
              Citizens scan this QR code with their phone<br>to access the feedback form for your office.
            </div>

          </div>
        </div>

        <!-- ── RIGHT: Stats + Info ── -->
        <div class="qr-right">

          <!-- Stats -->
          <div>
            <div style="font-size:11px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px">
              Feedback Stats
            </div>
            <div class="qr-stats-grid">
              <div class="qr-stat-card">
                <div class="qr-stat-icon red"><i class="bi bi-clipboard-data"></i></div>
                <div>
                  <div class="qr-stat-val"><?= number_format($stats['total'] ?? 0) ?></div>
                  <div class="qr-stat-label">Total Responses</div>
                </div>
              </div>
              <div class="qr-stat-card">
                <div class="qr-stat-icon gold"><i class="bi bi-star-fill"></i></div>
                <div>
                  <div class="qr-stat-val"><?= $stats['avg_rating'] ? number_format($stats['avg_rating'],1) : '—' ?></div>
                  <div class="qr-stat-label">Avg Rating</div>
                </div>
              </div>
              <div class="qr-stat-card">
                <div class="qr-stat-icon green"><i class="bi bi-emoji-smile"></i></div>
                <div>
                  <div class="qr-stat-val"><?= ($stats['satisfaction_rate'] ?? 0) > 0 ? $stats['satisfaction_rate'].'%' : '—' ?></div>
                  <div class="qr-stat-label">Satisfaction Rate</div>
                </div>
              </div>
              <div class="qr-stat-card">
                <div class="qr-stat-icon blue"><i class="bi bi-calendar-check"></i></div>
                <div>
                  <div class="qr-stat-val"><?= number_format($stats['today'] ?? 0) ?></div>
                  <div class="qr-stat-label">Today's Responses</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Department Info -->
          <div class="dept-info-card">
            <div class="dept-info-header">
              <i class="bi bi-building-fill"></i> Department Information
            </div>
            <div class="dept-info-body">
              <div class="dept-info-row">
                <span class="dil">Office Name</span>
                <span class="div"><?= htmlspecialchars($deptInfo['name'] ?? '—') ?></span>
              </div>
              <div class="dept-info-row">
                <span class="dil">Department Code</span>
                <span class="div"><?= htmlspecialchars($dept_code) ?></span>
              </div>
              <div class="dept-info-row">
                <span class="dil">Officer-in-Charge</span>
                <span class="div"><?= htmlspecialchars($deptInfo['head'] ?? '—') ?></span>
              </div>
              <div class="dept-info-row">
                <span class="dil">Status</span>
                <span class="div">
                  <span style="background:#eef8f0;color:#1e7c3b;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:700">
                    <?= ucfirst($deptInfo['status'] ?? 'Active') ?>
                  </span>
                </span>
              </div>
              <div class="dept-info-row" style="flex-direction:column;gap:6px">
                <span class="dil">Feedback Form URL</span>
                <span style="font-size:11px;color:#8B1A1A;word-break:break-all;font-weight:500">
                  <?= htmlspecialchars($feedback_url) ?>
                </span>
              </div>
            </div>
          </div>

          <!-- How to use -->
          <div class="how-card">
            <div class="how-card-header">
              <i class="bi bi-question-circle-fill"></i> How to Use This QR Code
            </div>
            <div class="how-card-body">
              <div class="how-step">
                <div class="how-step-num">1</div>
                <div class="how-step-text">
                  <h4>Download or Print</h4>
                  <p>Download the QR code image or print it directly from this page.</p>
                </div>
              </div>
              <div class="how-step">
                <div class="how-step-num">2</div>
                <div class="how-step-text">
                  <h4>Display in Your Office</h4>
                  <p>Post the QR code at your service counter, waiting area, or reception desk where citizens can easily see it.</p>
                </div>
              </div>
              <div class="how-step">
                <div class="how-step-num">3</div>
                <div class="how-step-text">
                  <h4>Citizens Scan & Rate</h4>
                  <p>Citizens scan the QR code with their phone camera and are directed straight to the feedback form for your office.</p>
                </div>
              </div>
              <div class="how-step">
                <div class="how-step-num">4</div>
                <div class="how-step-text">
                  <h4>View Responses</h4>
                  <p>All submitted feedback appears instantly in your Feedback Inbox and Dashboard.</p>
                </div>
              </div>
            </div>
          </div>

        </div><!-- /qr-right -->
      </div><!-- /qr-content -->
    </div><!-- /page-content -->
  </div><!-- /main-area -->
</div><!-- /app-shell -->

<script src="../assets/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/jquery-4.0.0.min.js"></script>
<script src="../assets/js/mobile_toggle.js"></script>
<script>
const DEPT_CODE    = <?= json_encode($dept_code) ?>;
const FEEDBACK_URL = <?= json_encode($feedback_url) ?>;

let currentSize = 220;


</script>
<script src="../js/department/dept_qrcode.js"></script>
</body>
</html>