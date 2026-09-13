(function () {
  'use strict';

  const base = (window.HRMS && window.HRMS.baseUrl) || '';
  const csrf = (window.HRMS && window.HRMS.csrf) || '';

  async function post(path, data = {}) {
    const body = new URLSearchParams({ ...data, _csrf: csrf });
    const res = await fetch(base + path, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body
    });
    return res.json().catch(() => ({ ok: false, error: 'Unexpected response.' }));
  }

  async function get(path) {
    const res = await fetch(base + path, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    return res.json().catch(() => ({}));
  }

  function toast(message, type = 'primary') {
    const container = document.createElement('div');
    container.className = `toast align-items-center text-bg-${type} border-0 position-fixed`;
    container.style.cssText = 'top:1rem;right:1rem;z-index:1080;';
    container.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div>
      <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>`;
    document.body.appendChild(container);
    const t = new bootstrap.Toast(container, { delay: 3500 });
    t.show();
    container.addEventListener('hidden.bs.toast', () => container.remove());
  }

  // Attendance buttons
  const btnIn = document.getElementById('btnClockIn');
  const btnOut = document.getElementById('btnClockOut');
  if (btnIn) btnIn.addEventListener('click', async () => {
    const r = await post('/attendance/clock-in');
    toast(r.ok ? 'Clocked in.' : (r.error || 'Failed.'), r.ok ? 'success' : 'danger');
    if (r.ok) setTimeout(() => location.reload(), 800);
  });
  if (btnOut) btnOut.addEventListener('click', async () => {
    const r = await post('/attendance/clock-out');
    toast(r.ok ? 'Clocked out.' : (r.error || 'Failed.'), r.ok ? 'success' : 'danger');
    if (r.ok) setTimeout(() => location.reload(), 800);
  });

  // Leave approval
  document.querySelectorAll('.btn-approve').forEach(b => b.addEventListener('click', async () => {
    if (!confirm('Approve this request?')) return;
    const r = await post('/leave/approve', { id: b.dataset.id });
    toast(r.ok ? 'Approved.' : (r.error || 'Failed.'), r.ok ? 'success' : 'danger');
    if (r.ok) setTimeout(() => location.reload(), 800);
  }));
  document.querySelectorAll('.btn-reject').forEach(b => b.addEventListener('click', () => {
    const modalEl = document.getElementById('rejectModal');
    if (!modalEl) {
      const reason = prompt('Reason for rejection:');
      if (reason) post('/leave/reject', { id: b.dataset.id, remarks: reason }).then(r => {
        toast(r.ok ? 'Rejected.' : 'Failed.', r.ok ? 'success' : 'danger');
        if (r.ok) setTimeout(() => location.reload(), 800);
      });
      return;
    }
    document.getElementById('rejectId').value = b.dataset.id;
    new bootstrap.Modal(modalEl).show();
  }));

  // User toggle
  document.querySelectorAll('.btn-toggle').forEach(b => b.addEventListener('click', async () => {
    const r = await post('/users/toggle', { id: b.dataset.id });
    if (r.ok) location.reload();
    else toast(r.error || 'Failed.', 'danger');
  }));

  // Payroll actions
  const btnProcess = document.getElementById('btnProcess');
  const btnApprove = document.getElementById('btnApprove');
  const btnLock = document.getElementById('btnLock');
  const periodId = new URLSearchParams(location.search).get('id');

  if (btnProcess) btnProcess.addEventListener('click', async () => {
    btnProcess.disabled = true;
    const r = await post('/payroll/process', { id: periodId });
    toast(r.ok ? `Processed ${r.processed} employees.` : (r.error || 'Failed.'), r.ok ? 'success' : 'danger');
    if (r.ok) setTimeout(() => location.reload(), 900);
    else btnProcess.disabled = false;
  });
  if (btnApprove) btnApprove.addEventListener('click', async () => {
    if (!confirm('Approve this payroll?')) return;
    const r = await post('/payroll/approve', { id: periodId });
    toast(r.ok ? 'Approved.' : (r.error || 'Failed.'), r.ok ? 'success' : 'danger');
    if (r.ok) setTimeout(() => location.reload(), 800);
  });
  if (btnLock) btnLock.addEventListener('click', async () => {
    if (!confirm('Lock payroll? This cannot be undone easily.')) return;
    const r = await post('/payroll/lock', { id: periodId });
    toast(r.ok ? 'Locked and payslips generated.' : (r.error || 'Failed.'), r.ok ? 'success' : 'danger');
    if (r.ok) setTimeout(() => location.reload(), 800);
  });

  // Notifications
  async function loadNotifications() {
    const count = document.getElementById('notifCount');
    if (!count) return;
    const data = await get('/api/notifications');
    if (data.unread > 0) {
      count.textContent = data.unread;
      count.style.display = 'inline-block';
    } else {
      count.style.display = 'none';
    }
  }
  if (document.getElementById('notifCount')) {
    loadNotifications();
    setInterval(loadNotifications, 30000);
  }

  // Dashboard charts
  if (window.deptData && document.getElementById('deptChart')) {
    new Chart(document.getElementById('deptChart'), {
      type: 'bar',
      data: {
        labels: window.deptData.map(d => d.label),
        datasets: [{ label: 'Employees', data: window.deptData.map(d => d.total),
          backgroundColor: '#0d6efd' }]
      },
      options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
  }
  if (window.attData && document.getElementById('attChart')) {
    new Chart(document.getElementById('attChart'), {
      type: 'doughnut',
      data: {
        labels: Object.keys(window.attData),
        datasets: [{ data: Object.values(window.attData),
          backgroundColor: ['#198754','#ffc107','#dc3545','#0dcaf0','#6c757d','#0d6efd'] }]
      }
    });
  }
})();