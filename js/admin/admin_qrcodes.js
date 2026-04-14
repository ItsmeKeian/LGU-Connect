// ── Date ──
document.getElementById('todayDate').textContent =
  new Date().toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});

// ── Size chips ──
document.querySelectorAll('.size-chip').forEach(chip => {
  chip.addEventListener('click', () => {
    document.querySelectorAll('.size-chip').forEach(c => c.classList.remove('active'));
    chip.classList.add('active');
    qrSize = parseInt(chip.dataset.size);
    renderAllQRs();
  });
});

// ── Load departments + feedback counts ──
$.ajax({
  url: '../php/get/get_departments.php',
  method: 'GET',
  success(res) {
    allDepts = Array.isArray(res) ? res : (res.data || res.departments || []);
    document.getElementById('deptCountLabel').textContent =
      allDepts.length + ' department' + (allDepts.length !== 1 ? 's' : '') + ' found';

    // Load feedback counts per dept
    $.ajax({
      url: '../php/get/get_analytics_data.php',
      method: 'POST',
      dataType: 'json',
      data: { period: 'this_year', dept_id: '' },
      success(ares) {
        if (ares.success && ares.by_dept) {
          ares.by_dept.forEach(d => {
            deptStats[d.department_code] = {
              total: d.total,
              sat:   d.satisfaction_rate,
              avg:   d.avg_rating
            };
          });
        }
        renderAllQRs();
      },
      error() { renderAllQRs(); }
    });
  },
  error() {
    document.getElementById('qrGrid').innerHTML =
      '<div class="qr-loading"><i class="bi bi-exclamation-triangle" style="font-size:30px;color:#e74c3c;display:block;margin-bottom:12px"></i><p style="color:#e74c3c">Failed to load departments. Check your connection.</p></div>';
  }
});

// ── Render all QR cards ──
function renderAllQRs() {
  const filter = document.getElementById('statusFilter').value;
  const grid   = document.getElementById('qrGrid');
  grid.innerHTML = '';

  const filtered = allDepts.filter(d => {
    if (filter === 'active')   return d.status === 'active';
    if (filter === 'inactive') return d.status === 'inactive';
    return true;
  });

  if (filtered.length === 0) {
    grid.innerHTML = '<div class="qr-loading"><i class="bi bi-inbox" style="font-size:32px;color:#ddd;display:block;margin-bottom:12px"></i><p>No departments found.</p></div>';
    return;
  }

  filtered.forEach(dept => {
    const card = buildQRCard(dept);
    grid.appendChild(card);
    // Generate QR inside the card's canvas wrap
    generateQR(dept, card.querySelector('.qr-canvas-wrap'));
  });
}

// ── Build one QR card DOM element ──
function buildQRCard(dept) {
  const feedbackUrl = BASE_URL + '?dept=' + encodeURIComponent(dept.code);
  const stats       = deptStats[dept.code] || { total: 0, sat: '—', avg: '—' };
  const isActive    = dept.status === 'active';

  const div = document.createElement('div');
  div.className   = 'qr-card';
  div.dataset.code   = dept.code;
  div.dataset.status = dept.status;

  div.innerHTML = `
    <div class="qr-card-header" style="${isActive ? '' : 'background:#888'}">
      <div class="qr-dept-icon"><i class="bi bi-building"></i></div>
      <div>
        <div class="qr-dept-name">${escHtml(dept.name)}</div>
        <div class="qr-dept-code">
          ${escHtml(dept.code)}
          ${isActive
            ? '<span style="background:rgba(255,255,255,.2);padding:1px 7px;border-radius:8px;margin-left:6px;font-size:9px">Active</span>'
            : '<span style="background:rgba(0,0,0,.2);padding:1px 7px;border-radius:8px;margin-left:6px;font-size:9px">Inactive</span>'}
        </div>
      </div>
    </div>
    <div class="qr-body">
      <div class="qr-canvas-wrap" id="qr_${escAttr(dept.code)}">
        <!-- QR generated here -->
      </div>
      <div class="qr-url">${escHtml(feedbackUrl)}</div>
      <div class="qr-divider"></div>
      <div class="qr-stats">
        <div class="qr-stat">
          <div class="sv">${stats.total || 0}</div>
          <div class="sl">Responses</div>
        </div>
        <div class="qr-stat">
          <div class="sv" style="color:${getSatColor(stats.sat)}">${stats.sat && stats.sat !== '—' ? stats.sat + '%' : '—'}</div>
          <div class="sl">Satisfied</div>
        </div>
        <div class="qr-stat">
          <div class="sv">${stats.avg && !isNaN(parseFloat(stats.avg)) ? parseFloat(stats.avg).toFixed(1) : '—'}</div>
          <div class="sl">Avg Rating</div>
        </div>
      </div>
      ${dept.head ? `<div style="font-size:11px;color:#aaa;text-align:center"><i class="bi bi-person" style="margin-right:3px"></i>${escHtml(dept.head)}</div>` : ''}
    </div>
    <div class="qr-actions">
      <button class="qr-btn download" onclick="downloadQR('${escAttr(dept.code)}', '${escAttr(dept.name)}')">
        <i class="bi bi-download"></i> Download
      </button>
      <button class="qr-btn print" onclick="printSingleQR('${escAttr(dept.code)}', '${escAttr(dept.name)}', '${escAttr(feedbackUrl)}')">
        <i class="bi bi-printer"></i> Print
      </button>
      <button class="qr-btn copy" onclick="copyLink('${escAttr(feedbackUrl)}')" title="Copy feedback URL">
        <i class="bi bi-link-45deg"></i>
      </button>
    </div>`;
  return div;
}

// ── Generate QR code into container ──
function generateQR(dept, container) {
  container.innerHTML = '';
  const url = BASE_URL + '?dept=' + encodeURIComponent(dept.code);
  new QRCode(container, {
    text:          url,
    width:         qrSize,
    height:        qrSize,
    colorDark:     '#1a1a1a',
    colorLight:    '#ffffff',
    correctLevel:  QRCode.CorrectLevel.H
  });
}

// ── Download QR as PNG ──
function downloadQR(code, name) {
  const wrap   = document.getElementById('qr_' + code);
  const canvas = wrap ? wrap.querySelector('canvas') : null;
  if (!canvas) { alert('QR not ready yet.'); return; }

  // Create a new canvas with padding + label
  const pad    = 20;
  const c2     = document.createElement('canvas');
  c2.width     = canvas.width  + pad * 2;
  c2.height    = canvas.height + pad * 2 + 32;
  const ctx    = c2.getContext('2d');

  ctx.fillStyle = '#ffffff';
  ctx.fillRect(0, 0, c2.width, c2.height);
  ctx.drawImage(canvas, pad, pad);

  // Label below QR
  ctx.fillStyle    = '#1a1a1a';
  ctx.font         = 'bold 13px Inter, sans-serif';
  ctx.textAlign    = 'center';
  ctx.fillText(name, c2.width / 2, canvas.height + pad + 20);

  const link  = document.createElement('a');
  link.href   = c2.toDataURL('image/png');
  link.download = 'qr_' + code.toLowerCase() + '_feedback.png';
  link.click();
}

// ── Print single QR ──
function printSingleQR(code, name, url) {
  const wrap   = document.getElementById('qr_' + code);
  const canvas = wrap ? wrap.querySelector('canvas') : null;
  if (!canvas) { alert('QR not ready.'); return; }

  const imgSrc = canvas.toDataURL('image/png');
  const win    = window.open('', '_blank');
  win.document.write(`
    <!DOCTYPE html>
    <html>
    <head>
      <title>QR Code – ${escHtml(name)}</title>
      <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Inter', Arial, sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; background:#fff; }
        .wrap { text-align:center; padding:40px; }
        .lgu-name { font-size:13pt; font-weight:700; color:#8B1A1A; margin-bottom:4px; letter-spacing:.04em; }
        .lgu-sub  { font-size:9pt; color:#888; margin-bottom:6px; }
        .dept-name { font-size:15pt; font-weight:700; color:#1a1a1a; margin-bottom:4px; }
        .dept-sub  { font-size:9pt; color:#aaa; margin-bottom:20px; }
        img { display:block; margin:0 auto; border:1px solid #efefef; border-radius:4px; padding:8px; }
        .url  { font-size:8pt; color:#aaa; margin-top:14px; font-family:monospace; word-break:break-all; max-width:300px; margin-left:auto; margin-right:auto; }
        .inst { font-size:9pt; color:#555; margin-top:16px; }
        @media print {
          body { min-height:auto; }
          .no-print { display:none; }
        }
      </style>
    </head>
    <body onload="window.print();window.close()">
      <div class="wrap">
        <div class="lgu-name">Municipality of San Julian</div>
        <div class="lgu-sub">San Julian, Eastern Samar</div>
        <div class="dept-name">${escHtml(name)}</div>
        <div class="dept-sub">Client Satisfaction Feedback</div>
        <img src="${imgSrc}" width="220" height="220" alt="QR Code"/>
        <div class="url">${escHtml(url)}</div>
        <div class="inst">Scan this QR code to submit your feedback</div>
      </div>
    </body>
    </html>
  `);
  win.document.close();
}

// ── Copy feedback link ──
function copyLink(url) {
  navigator.clipboard.writeText(url).then(() => {
    // Brief visual feedback
    event.currentTarget.innerHTML = '<i class="bi bi-check2"></i>';
    setTimeout(() => { event.currentTarget.innerHTML = '<i class="bi bi-link-45deg"></i>'; }, 1500);
  }).catch(() => {
    prompt('Copy this link:', url);
  });
}

// ── Filter cards ──
function filterCards() { renderAllQRs(); }

// ── Helpers ──
function getSatColor(sat) {
  if (sat === '—' || sat === null) return '#aaa';
  const v = parseFloat(sat);
  return v >= 80 ? '#1e7c3b' : v >= 60 ? '#1a6fbf' : v >= 40 ? '#b06c10' : '#c0392b';
}
function escHtml(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escAttr(s) {
  if (!s) return '';
  return String(s).replace(/'/g,"\\'").replace(/"/g,'&quot;');
}

// ── Avatar dropdown ──
function toggleAvatarDropdown(e) {
  e.stopPropagation();
  document.getElementById('avatarDropdown').classList.toggle('show');
}
document.addEventListener('click', () => {
  document.getElementById('avatarDropdown').classList.remove('show');
});