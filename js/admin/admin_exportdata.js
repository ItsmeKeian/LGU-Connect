
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