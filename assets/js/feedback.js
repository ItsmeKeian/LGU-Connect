
// ── Rating labels ──
const RATING_LABELS = {
    1: '😞 Very Poor',
    2: '😐 Poor',
    3: '🙂 Average',
    4: '😊 Good',
    5: '🤩 Excellent!'
  };
  
  const SQD_SCALE = '1 – Strongly Disagree &nbsp;&nbsp; 5 – Strongly Agree';
  
  // ── Get dept code from URL ──
  const urlParams = new URLSearchParams(window.location.search);
  const deptCode  = urlParams.get('dept') || '';
  
  // ── Init on page load ──
  $(document).ready(function () {
    loadFormData();
  });
  
  // ══════════════════════════════════════════
  // 1. Load all form data via AJAX
  // ══════════════════════════════════════════
  function loadFormData() {
    $.ajax({
      url:      'php/get/get_feedback_init.php',
      method:   'GET',
      data:     { dept: deptCode },
      dataType: 'json',
      success(res) {
        if (!res.success) {
          showError('Failed to load form. Please refresh the page.');
          return;
        }
  
        // Update header with LGU info
        $('#lguName').text(res.lgu.name);
        $('#lguSub').text('Client Satisfaction Feedback Form');
        $('#lguAddress').text(res.lgu.address + ' · Anti-Red Tape Authority (ARTA) Compliant');
  
        // Feedback closed?
        if (!res.is_open) {
          showClosedState();
          return;
        }
  
        // Show form
        buildForm(res);
        bindEvents();
      },
      error() {
        showError('Connection error. Please check your network and refresh.');
      }
    });
  }
  
  // ══════════════════════════════════════════
  // 2. Build the form HTML from JSON data
  // ══════════════════════════════════════════
  function buildForm(res) {
    const { department, all_depts, sqd_questions } = res;
  
    // ── Department banner ──
    if (department) {
      $('#deptBanner').html(`
        <div class="dept-icon">🏢</div>
        <div class="dept-info">
          <h3>${escHtml(department.name)}</h3>
          <p>You are submitting feedback for this office.
             ${department.head ? '<br>Officer-in-Charge: ' + escHtml(department.head) : ''}
          </p>
        </div>
      `).show();
      // Hidden dept_code input
      $('#deptCodeInput').val(department.code);
    } else {
      // Show department dropdown
      let opts = '<option value="">— Select the office you visited —</option>';
      all_depts.forEach(d => {
        opts += `<option value="${escAttr(d.code)}">${escHtml(d.name)}</option>`;
      });
      $('#deptSelectWrap').html(`
        <div class="form-group" id="deptGroup">
          <label class="form-label">Department / Office <span class="required">*</span></label>
          <select class="form-select" id="deptSelect" name="dept_code" required>
            ${opts}
          </select>
          <div class="field-error">Please select a department.</div>
        </div>
      `).show();
    }
  
    // ── SQD Questions ──
    let sqdHtml = '';
    sqd_questions.forEach((sqd, idx) => {
      sqdHtml += `
        <div class="sqd-item" id="${sqd.key}_group">
          <div class="sqd-question">
            <span class="sqd-num">SQD${idx}</span>
            ${escHtml(sqd.question)}
          </div>
          <div class="sqd-stars">
            ${buildStars(sqd.key, 28)}
          </div>
          <div class="sqd-scale-labels">
            <span>1 – Strongly Disagree</span>
            <span>5 – Strongly Agree</span>
          </div>
          <div class="field-error" id="${sqd.key}_error">Please rate this statement.</div>
        </div>`;
    });
    $('#sqdContainer').html(sqdHtml);
  
    // Show the form
    $('#feedbackFormWrap').show();
    $('#loadingWrap').hide();
  }
  
  // ── Build star radio inputs ──
  function buildStars(name, size = 44) {
    let html = '';
    for (let v = 5; v >= 1; v--) {
      html += `
        <input type="radio" name="${name}" id="${name}_${v}" value="${v}">
        <label for="${name}_${v}" style="font-size:${size}px" title="${v}">★</label>`;
    }
    return html;
  }
  
  // ══════════════════════════════════════════
  // 3. Bind all events
  // ══════════════════════════════════════════
  function bindEvents() {
  
    // Overall rating label update
    $(document).on('change', 'input[name="rating"]', function () {
      const lbl   = $('#overallLabel');
      const val   = parseInt(this.value);
      const color = val >= 4 ? '#1e7c3b' : val === 3 ? '#b06c10' : '#c0392b';
      lbl.text(RATING_LABELS[val] || 'Tap a star to rate').css('color', color);
      $('#ratingGroup').removeClass('has-error');
    });
  
    // Clear errors on interaction
    $(document).on('change', 'input[type="radio"]', function () {
      $(this).closest('.form-group, .sqd-item').removeClass('has-error');
    });
    $(document).on('change', 'select', function () {
      $(this).closest('.form-group').removeClass('has-error');
    });
  
    // Form submit
    $('#feedbackForm').on('submit', function (e) {
      e.preventDefault();
      if (validateForm()) submitForm();
    });
  }
  
  // ══════════════════════════════════════════
  // 4. Validate form
  // ══════════════════════════════════════════
  function validateForm() {
    let valid = true;
  
    // Department dropdown (if visible)
    const deptSel = $('#deptSelect');
    if (deptSel.length && !deptSel.val()) {
      $('#deptGroup').addClass('has-error');
      valid = false;
    }
  
    // Sex
    if (!$('input[name="sex"]:checked').length) {
      $('#sexGroup').addClass('has-error');
      valid = false;
    }
  
    // Age group
    if (!$('input[name="age_group"]:checked').length) {
      $('#ageGroup').addClass('has-error');
      valid = false;
    }
  
    // Overall rating
    if (!$('input[name="rating"]:checked').length) {
      $('#ratingGroup').addClass('has-error');
      valid = false;
    }
  
    // SQD questions
    for (let i = 0; i <= 8; i++) {
      const key = `sqd${i}`;
      if (!$(`input[name="${key}"]:checked`).length) {
        $(`#${key}_group`).addClass('has-error');
        valid = false;
      }
    }
  
    // Scroll to first error
    if (!valid) {
      const firstErr = $('.has-error').first();
      if (firstErr.length) {
        $('html, body').animate({
          scrollTop: firstErr.offset().top - 100
        }, 400);
      }
    }
  
    return valid;
  }
  
  // ══════════════════════════════════════════
  // 5. Submit form via AJAX
  // ══════════════════════════════════════════
  function submitForm() {
    const btn = $('#submitBtn');
    btn.prop('disabled', true).html('<span class="spinner-inline"></span> Submitting…');
  
    // Build payload
    const payload = {
      dept_code:      $('#deptCodeInput').val() || $('#deptSelect').val(),
      respondent_type: $('input[name="respondent_type"]:checked').val(),
      sex:            $('input[name="sex"]:checked').val(),
      age_group:      $('input[name="age_group"]:checked').val(),
      rating:         $('input[name="rating"]:checked').val(),
      comment:        $('#comment').val().trim(),
      suggestions:    $('#suggestions').val().trim(),
    };
  
    // SQD scores
    for (let i = 0; i <= 8; i++) {
      payload[`sqd${i}`] = $(`input[name="sqd${i}"]:checked`).val();
    }
  
    $.ajax({
      url:      'php/save/submit_feedback.php',
      method:   'POST',
      data:     payload,
      dataType: 'json',
      success(res) {
        if (res.success) {
          showSuccess(res.dept_name || payload.dept_code, payload.rating);
        } else {
          alert('Error: ' + (res.message || 'Please try again.'));
          btn.prop('disabled', false).html('✓ Submit Feedback');
        }
      },
      error() {
        alert('Network error. Please check your connection and try again.');
        btn.prop('disabled', false).html('✓ Submit Feedback');
      }
    });
  }
  
  // ══════════════════════════════════════════
  // 6. Success overlay
  // ══════════════════════════════════════════
  function showSuccess(deptName, rating) {
    const stars = '★'.repeat(rating) + '☆'.repeat(5 - rating);
    $('#successDept').text(deptName);
    $('#successRating').html(`${stars} (${rating}/5)`);
    $('#successOverlay').addClass('show');
  }
  
  function submitAnother() {
    $('#successOverlay').removeClass('show');
    $('#feedbackForm')[0].reset();
    $('#overallLabel').text('Tap a star to rate').css('color', '#555');
    $('#submitBtn').prop('disabled', false).html('✓ Submit Feedback');
    $('html, body').animate({ scrollTop: 0 }, 400);
  }
  
  // ══════════════════════════════════════════
  // 7. Special states
  // ══════════════════════════════════════════
  function showClosedState() {
    $('#loadingWrap').hide();
    $('#feedbackFormWrap').hide();
    $('#closedWrap').show();
  }
  
  function showError(msg) {
    $('#loadingWrap').hide();
    $('#errorWrap').text(msg).show();
  }
  
  // ══════════════════════════════════════════
  // 8. Helpers
  // ══════════════════════════════════════════
  function escHtml(s) {
    if (!s) return '';
    return String(s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
  function escAttr(s) {
    if (!s) return '';
    return String(s).replace(/'/g, "\\'").replace(/"/g, '&quot;');
  }