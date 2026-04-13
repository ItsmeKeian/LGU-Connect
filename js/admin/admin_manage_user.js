// ── Modals ──
const userModal   = new bootstrap.Modal(document.getElementById('userModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

// ── State ──
let currentPage    = 1;
let currentFilters = {};

// ── Init ──
document.getElementById('todayDate').textContent =
  new Date().toLocaleDateString('en-PH', {weekday:'long',year:'numeric',month:'long',day:'numeric'});

document.getElementById('menuToggle').addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('sb-open');
});

function toggleAvatarDropdown(e) {
  e.stopPropagation();
  document.getElementById('avatarDropdown').classList.toggle('show');
}
document.addEventListener('click', () => document.getElementById('avatarDropdown').classList.remove('show'));
document.getElementById('avatarDropdown').addEventListener('click', e => e.stopPropagation());

document.getElementById('refreshBtn').addEventListener('click', () => loadUsers());

// ── Load Departments into modal dropdown ──
function loadDeptDropdown() {
  $.get('../php/get/get_departments.php', function(res) {
    if (!res.success) return;
    let opts = '<option value="">— Select Department —</option>';
    res.data.forEach(d => {
      opts += `<option value="${d.code}">${d.code} — ${d.name}</option>`;
    });
    $('#inputDept').html(opts);
  });
}

// ── Load Users ──
function loadUsers(page = 1) {
  currentPage = page;
  const search = $('#searchInput').val().trim();
  const role   = $('#filterRole').val();
  currentFilters = { search, role };

  $('#usersTableBody').html(`
    <tr><td colspan="8" class="text-center py-4" style="color:#6b6864;">
      <div class="spinner-border spinner-border-sm text-danger me-2"></div> Loading...
    </td></tr>`);

  $.get('../php/get/get_users.php', { page, per_page: 10, ...currentFilters }, function(res) {
    if (!res.success) { showToast(res.message || 'Failed to load users.', 'danger'); return; }

    $('#sumTotal').text(res.summary.total ?? 0);
    $('#sumActive').text(res.summary.active ?? 0);
    $('#sumAdmins').text(res.summary.superadmins ?? 0);
    $('#sumDeptUsers').text(res.summary.dept_users ?? 0);

    if (!res.data.length) {
      $('#usersTableBody').html(`
        <tr><td colspan="8" class="text-center py-4" style="color:#9a9390;">
          <i class="bi bi-people" style="font-size:28px;display:block;margin-bottom:8px;opacity:0.3;"></i>
          No users found.
        </td></tr>`);
      $('#recordCount').text('0 users');
      $('#paginationInfo').text('');
      $('#paginationLinks').html('');
      return;
    }

    let rows = '';
    res.data.forEach((u, i) => {
      const num       = (page - 1) * 10 + i + 1;
      const letter    = u.full_name.charAt(0).toUpperCase();
      const roleClass = u.role === 'superadmin' ? 'superadmin' : 'dept_user';
      const roleLabel = u.role === 'superadmin'
        ? '<i class="bi bi-shield-fill-check"></i> Super Admin'
        : '<i class="bi bi-person-badge"></i> Dept User';
      const deptHtml  = u.department
        ? `<span class="dept-pill">${u.department}</span>`
        : `<span class="dept-pill none">—</span>`;
      const statusClass = (u.status ?? 'active') === 'active' ? 'active' : 'inactive';
      const created   = new Date(u.created_at).toLocaleDateString('en-PH', {month:'short',day:'numeric',year:'numeric'});

      rows += `
      <tr>
        <td style="color:#9a9390;font-size:0.72rem;">${num}</td>
        <td>
          <div class="user-cell">
            <div class="user-avatar">${letter}</div>
            <div>
              <div class="user-name">${htmlEsc(u.full_name)}</div>
              <div class="user-email">${htmlEsc(u.email ?? '')}</div>
            </div>
          </div>
        </td>
        <td style="font-size:0.8rem;color:var(--text-muted);">@${htmlEsc(u.username)}</td>
        <td><span class="role-badge ${roleClass}">${roleLabel}</span></td>
        <td>${deptHtml}</td>
        <td>
          <span class="status-badge ${statusClass}">
            <span class="status-dot ${statusClass}"></span>
            ${statusClass.charAt(0).toUpperCase() + statusClass.slice(1)}
          </span>
        </td>
        <td style="font-size:0.75rem;color:var(--text-muted);">${created}</td>
        <td>
          <div class="action-wrap">
            <button class="btn-edit" onclick="openEditModal(${JSON.stringify(u).replace(/"/g,'&quot;')})">
              <i class="bi bi-pencil"></i> Edit
            </button>
            <button class="btn-delete" onclick="openDeleteModal(${u.id}, '${htmlEsc(u.full_name)}')">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </td>
      </tr>`;
    });

    $('#usersTableBody').html(rows);
    $('#recordCount').text(`${res.total} user${res.total !== 1 ? 's' : ''} found`);
    renderPagination(res.total, 10, page);

  }).fail(() => showToast('Server error loading users.', 'danger'));
}

// ── Pagination ──
function renderPagination(total, perPage, current) {
  const totalPages = Math.ceil(total / perPage);
  const from = (current - 1) * perPage + 1;
  const to   = Math.min(current * perPage, total);
  $('#paginationInfo').text(total ? `Showing ${from}–${to} of ${total}` : '');
  if (totalPages <= 1) { $('#paginationLinks').html(''); return; }

  let links = '';
  links += `<li class="page-item ${current===1?'disabled':''}">
    <a class="page-link" href="#" onclick="loadUsers(${current-1});return false;">‹</a></li>`;
  for (let p = Math.max(1,current-2); p <= Math.min(totalPages,current+2); p++) {
    links += `<li class="page-item ${p===current?'active':''}">
      <a class="page-link" href="#" onclick="loadUsers(${p});return false;">${p}</a></li>`;
  }
  links += `<li class="page-item ${current===totalPages?'disabled':''}">
    <a class="page-link" href="#" onclick="loadUsers(${current+1});return false;">›</a></li>`;
  $('#paginationLinks').html(links);
}

// ── Role change: show/hide dept field ──
function handleRoleChange() {
  const role = $('#inputRole').val();
  if (role === 'superadmin') {
    $('#deptGroup').hide();
    $('#inputDept').val(''); // ✅ clear dept value when superadmin
  } else {
    $('#deptGroup').show();
  }
}

// ── Open Add Modal ──
function openAddModal() {
  document.getElementById('modalTitle').textContent = 'Add New User';
  document.getElementById('saveBtnText').textContent = 'Save User';
  document.getElementById('userId').value = '0';
  document.getElementById('inputFullName').value = '';
  document.getElementById('inputUsername').value = '';
  document.getElementById('inputEmail').value = '';
  document.getElementById('inputPassword').value = '';
  document.getElementById('inputRole').value = 'dept_user';
  document.getElementById('inputDept').value = '';
  document.getElementById('pwRequired').style.display = 'inline';
  document.getElementById('pwHint').style.display = 'none';
  handleRoleChange(); // ✅ trigger show/hide based on default role
  userModal.show();
}

// ── Open Edit Modal ──
function openEditModal(u) {
  document.getElementById('modalTitle').textContent = 'Edit User';
  document.getElementById('saveBtnText').textContent = 'Update User';
  document.getElementById('userId').value = u.id;
  document.getElementById('inputFullName').value = u.full_name;
  document.getElementById('inputUsername').value = u.username;
  document.getElementById('inputEmail').value = u.email ?? '';
  document.getElementById('inputPassword').value = '';
  document.getElementById('inputRole').value = u.role;
  document.getElementById('inputDept').value = u.department ?? '';
  document.getElementById('pwRequired').style.display = 'none';
  document.getElementById('pwHint').style.display = 'inline';
  handleRoleChange(); // ✅ show/hide dept based on role
  userModal.show();
}

// ── Save User ──
function saveUser() {
  const id       = parseInt($('#userId').val()) || 0;
  const fullName = $('#inputFullName').val().trim();
  const username = $('#inputUsername').val().trim();
  const email    = $('#inputEmail').val().trim();
  const password = $('#inputPassword').val().trim();
  const role     = $('#inputRole').val();
  const dept     = $('#inputDept').val();

  // ── Validation ──
  if (!fullName || !username || !email) {
    showToast('Name, username, and email are required.', 'danger'); return;
  }
  if (!id && !password) {
    showToast('Password is required for new users.', 'danger'); return;
  }
  if (role === 'dept_user' && !dept) {
    showToast('Please select a department.', 'danger'); return;
  }

  // ── Disable button to prevent double submit ──
  const saveBtn = document.querySelector('.btn-save');
  saveBtn.disabled = true;

  const data = { id, full_name: fullName, username, email, password, role, department: dept };

  $.post('../php/save/save_user.php', data, function(res) {
    saveBtn.disabled = false;
    if (res.success) {
      userModal.hide();
      showToast(res.message, 'success');
      loadUsers(currentPage);
    } else {
      showToast(res.message || 'Failed to save user.', 'danger');
    }
  }, 'json').fail(() => {
    saveBtn.disabled = false;
    showToast('Server error.', 'danger');
  });
}

// ── Delete ──
function openDeleteModal(id, name) {
  document.getElementById('deleteUserId').value = id;
  document.getElementById('deleteUserName').textContent = name;
  deleteModal.show();
}

function confirmDelete() {
  const id = $('#deleteUserId').val();
  $.post('../php/delete/delete_user.php', { id }, function(res) {
    deleteModal.hide();
    if (res.success) {
      showToast(res.message, 'success');
      loadUsers(currentPage);
    } else {
      showToast(res.message || 'Failed to delete user.', 'danger');
    }
  }).fail(() => showToast('Server error.', 'danger'));
}

// ── Password toggle ──
function togglePw() {
  const input = document.getElementById('inputPassword');
  const icon  = document.getElementById('pwEyeIcon');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'bi bi-eye';
  }
}

// ── Toast ──
function showToast(msg, type = 'success') {
  const el = document.getElementById('toastMsg');
  const tx = document.getElementById('toastText');
  el.className = `toast align-items-center border-0 text-white bg-${type === 'success' ? 'success' : 'danger'}`;
  tx.textContent = msg;
  new bootstrap.Toast(el, { delay: 3000 }).show();
}

// ── HTML escape helper ──
function htmlEsc(str) {
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Initial Load ──
loadDeptDropdown();
loadUsers(1);