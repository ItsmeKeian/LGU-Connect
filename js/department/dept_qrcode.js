// ── Init ──
document.getElementById('todayDate').textContent =
  new Date().toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'});

document.getElementById('menuToggle')?.addEventListener('click',()=>
  document.getElementById('sidebar').classList.toggle('sb-open'));

function toggleAvatarDropdown(e){
  e.stopPropagation();
  document.getElementById('avatarDropdown').classList.toggle('show');
}
document.addEventListener('click',()=>document.getElementById('avatarDropdown')?.classList.remove('show'));

// Sidebar feedback count
$.get('../php/get/get_feedback.php',{dept:DEPT_CODE,per_page:1,page:1},function(res){
  if(res.success) $('#sbFeedbackCount').text(res.summary.total||0);
});

// ── Build QR code ──
function buildQR(size) {
  const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodeURIComponent(FEEDBACK_URL)}&color=000000&bgcolor=ffffff&margin=1`;
  const img = document.getElementById('qrImage');
  img.src = qrUrl;
  img.width  = size;
  img.height = size;

  // Update download button
  document.getElementById('downloadBtn').href     = qrUrl;
  document.getElementById('downloadBtn').download = `QR_${DEPT_CODE}_${size}.png`;
}

function changeSize(size, btn) {
  currentSize = size;
  document.querySelectorAll('.qr-size-tab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  buildQR(size);
}

// ── Copy link ──
function copyLink() {
  const btn = document.getElementById('copyBtn');
  navigator.clipboard.writeText(FEEDBACK_URL).then(() => {
    btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
    btn.classList.add('copied');
    setTimeout(() => {
      btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy';
      btn.classList.remove('copied');
    }, 2500);
  }).catch(() => {
    // Fallback for older browsers
    const el = document.createElement('textarea');
    el.value = FEEDBACK_URL;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    btn.innerHTML = '<i class="bi bi-check-lg"></i> Copied!';
    btn.classList.add('copied');
    setTimeout(() => {
      btn.innerHTML = '<i class="bi bi-clipboard"></i> Copy';
      btn.classList.remove('copied');
    }, 2500);
  });
}

// ── Initial load ──
buildQR(220);