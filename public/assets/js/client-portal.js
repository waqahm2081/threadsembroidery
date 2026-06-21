/* ============================================================
   CLIENT PORTAL  —  client-portal.js
   Tab switching · order filter + search · order tracking ·
   avatar preview · password toggle · save feedback
   ============================================================ */

(function () {
  'use strict';

  /* ── Tracking data ────────────────────────────────────────── */
  const ORDER_DATA = {
    'TE-2045': {
      desc:   'Classic Polo Shirt × 25',
      status: 'processing',
      eta:    'Tuesday, 27 May 2026',
      steps: [
        { event: 'Order Placed',          date: '18 May 2026, 10:32',  state: 'done' },
        { event: 'Artwork Approved',       date: '19 May 2026, 14:15',  state: 'done' },
        { event: 'In Production',          date: '20 May 2026, 09:00',  state: 'active' },
        { event: 'Quality Check',          date: 'Pending',             state: 'pending' },
        { event: 'Dispatched',             date: 'Pending',             state: 'pending' },
        { event: 'Delivered',             date: 'Pending',             state: 'pending' },
      ]
    },
    'TE-2031': {
      desc:   'Softshell Jacket × 10',
      status: 'shipped',
      eta:    'Thursday, 23 May 2026',
      steps: [
        { event: 'Order Placed',          date: '02 May 2026, 08:54',  state: 'done' },
        { event: 'Artwork Approved',       date: '03 May 2026, 11:20',  state: 'done' },
        { event: 'In Production',          date: '04 May 2026, 09:00',  state: 'done' },
        { event: 'Quality Check',          date: '06 May 2026, 16:00',  state: 'done' },
        { event: 'Dispatched',             date: '07 May 2026, 13:45',  state: 'active' },
        { event: 'Delivered',             date: 'Pending',             state: 'pending' },
      ]
    },
    'TE-2019': {
      desc:   'Fleece Hoodie × 30',
      status: 'delivered',
      eta:    'Delivered on 18 Apr 2026',
      steps: [
        { event: 'Order Placed',          date: '14 Apr 2026, 09:10',  state: 'done' },
        { event: 'Artwork Approved',       date: '14 Apr 2026, 15:30',  state: 'done' },
        { event: 'In Production',          date: '15 Apr 2026, 08:00',  state: 'done' },
        { event: 'Quality Check',          date: '16 Apr 2026, 14:00',  state: 'done' },
        { event: 'Dispatched',             date: '17 Apr 2026, 10:20',  state: 'done' },
        { event: 'Delivered',             date: '18 Apr 2026, 12:05',  state: 'done' },
      ]
    },
    'TE-2008': {
      desc:   'Hi-Vis Safety Vest × 50',
      status: 'delivered',
      eta:    'Delivered on 02 Apr 2026',
      steps: [
        { event: 'Order Placed',          date: '28 Mar 2026, 11:00',  state: 'done' },
        { event: 'Artwork Approved',       date: '29 Mar 2026, 09:40',  state: 'done' },
        { event: 'In Production',          date: '30 Mar 2026, 08:00',  state: 'done' },
        { event: 'Quality Check',          date: '31 Mar 2026, 16:00',  state: 'done' },
        { event: 'Dispatched',             date: '01 Apr 2026, 11:00',  state: 'done' },
        { event: 'Delivered',             date: '02 Apr 2026, 14:30',  state: 'done' },
      ]
    },
    'TE-1994': {
      desc:   'School PE Kit Set × 60',
      status: 'delivered',
      eta:    'Delivered on 20 Mar 2026',
      steps: [
        { event: 'Order Placed',          date: '10 Mar 2026, 10:00',  state: 'done' },
        { event: 'Artwork Approved',       date: '11 Mar 2026, 13:00',  state: 'done' },
        { event: 'In Production',          date: '12 Mar 2026, 08:00',  state: 'done' },
        { event: 'Quality Check',          date: '17 Mar 2026, 15:00',  state: 'done' },
        { event: 'Dispatched',             date: '18 Mar 2026, 09:30',  state: 'done' },
        { event: 'Delivered',             date: '20 Mar 2026, 11:15',  state: 'done' },
      ]
    },
    'TE-1982': {
      desc:   'Embroidered Cap × 40',
      status: 'processing',
      eta:    'Wednesday, 28 May 2026',
      steps: [
        { event: 'Order Placed',          date: '05 Mar 2026, 14:22',  state: 'done' },
        { event: 'Artwork Approved',       date: '06 Mar 2026, 10:00',  state: 'active' },
        { event: 'In Production',          date: 'Pending',             state: 'pending' },
        { event: 'Quality Check',          date: 'Pending',             state: 'pending' },
        { event: 'Dispatched',             date: 'Pending',             state: 'pending' },
        { event: 'Delivered',             date: 'Pending',             state: 'pending' },
      ]
    }
  };

  const ICON_MAP = {
    done:    'bi-check-lg',
    active:  'bi-arrow-repeat',
    pending: 'bi-clock'
  };

  /* ══════════════════════════════════════════════════════════
     1. TAB SWITCHING
  ══════════════════════════════════════════════════════════ */
  const navItems = document.querySelectorAll('.cp-nav-item');
  const panels   = document.querySelectorAll('.cp-tab-panel');

  function switchTab(tabId) {
    navItems.forEach(n => n.classList.toggle('active', n.dataset.tab === tabId));
    panels.forEach(p => {
      const match = p.id === `cp-tab-${tabId}`;
      p.classList.toggle('active', match);
    });
  }

  navItems.forEach(item => {
    item.addEventListener('click', () => switchTab(item.dataset.tab));
  });

  // "View All" link in summary table
  document.querySelectorAll('.cp-block-link[data-tab-link]').forEach(btn => {
    btn.addEventListener('click', () => switchTab(btn.dataset.tabLink));
  });

  // "Track" buttons in orders table
  document.querySelectorAll('.cp-row-track-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const orderNum = btn.dataset.order.replace('#', '');
      switchTab('track');
      const select = document.getElementById('cpTrackSelect');
      if (select) {
        select.value = orderNum;
        renderTracking(orderNum);
      }
    });
  });


  /* ══════════════════════════════════════════════════════════
     2. ORDER FILTERS + SEARCH
  ══════════════════════════════════════════════════════════ */
  const filterBtns  = document.querySelectorAll('.cp-filter-btn');
  const searchInput = document.getElementById('cpOrderSearch');
  const tableRows   = document.querySelectorAll('#cpOrdersTable tbody tr');

  let activeFilter = 'all';
  let searchQuery  = '';

  function applyFilters() {
    tableRows.forEach(row => {
      const status    = row.dataset.status || '';
      const text      = row.textContent.toLowerCase();
      const passFilter = activeFilter === 'all' || status === activeFilter;
      const passSearch = searchQuery === '' || text.includes(searchQuery);
      row.style.display = (passFilter && passSearch) ? '' : 'none';
    });
  }

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      activeFilter = btn.dataset.filter;
      applyFilters();
    });
  });

  if (searchInput) {
    searchInput.addEventListener('input', () => {
      searchQuery = searchInput.value.toLowerCase().trim();
      applyFilters();
    });
  }


  /* ══════════════════════════════════════════════════════════
     3. ORDER TRACKING
  ══════════════════════════════════════════════════════════ */
  const trackSelect  = document.getElementById('cpTrackSelect');
  const trackResult  = document.getElementById('cpTrackResult');
  const trackEmpty   = document.getElementById('cpTrackEmpty');
  const trackOrderNum  = document.getElementById('cpTrackOrderNum');
  const trackOrderDesc = document.getElementById('cpTrackOrderDesc');
  const trackBadge     = document.getElementById('cpTrackStatusBadge');
  const timeline       = document.getElementById('cpTimeline');
  const etaWrap        = document.getElementById('cpTrackEta');
  const etaDate        = document.getElementById('cpEtaDate');

  function renderTracking(orderKey) {
    const data = ORDER_DATA[orderKey];
    if (!data) return;

    // Order bar
    trackOrderNum.textContent  = `#${orderKey}`;
    trackOrderDesc.textContent = data.desc;
    trackBadge.className       = `cp-status cp-status--${data.status}`;
    trackBadge.textContent     = data.status.charAt(0).toUpperCase() + data.status.slice(1);

    // Timeline
    timeline.innerHTML = data.steps.map(step => `
      <div class="cp-timeline-item">
        <div class="cp-timeline-left">
          <div class="cp-timeline-dot cp-timeline-dot--${step.state}">
            <i class="bi ${ICON_MAP[step.state]}"></i>
          </div>
          <div class="cp-timeline-line"></div>
        </div>
        <div class="cp-timeline-content">
          <div class="cp-timeline-event${step.state === 'pending' ? ' cp-timeline-event--pending' : ''}">${step.event}</div>
          <div class="cp-timeline-date">${step.date}</div>
        </div>
      </div>
    `).join('');

    // ETA
    etaDate.textContent = data.eta;

    // Show result, hide empty
    trackResult.style.display = 'block';
    trackEmpty.style.display  = 'none';
  }

  if (trackSelect) {
    trackSelect.addEventListener('change', () => {
      const val = trackSelect.value;
      if (val) {
        renderTracking(val);
      } else {
        trackResult.style.display = 'none';
        trackEmpty.style.display  = 'flex';
      }
    });
  }


  /* ══════════════════════════════════════════════════════════
     4. AVATAR PREVIEW
  ══════════════════════════════════════════════════════════ */
  const avatarInput       = document.getElementById('cpAvatarInput');
  const profileAvatarImg  = document.getElementById('cpProfileAvatarImg');
  const headerAvatarImg   = document.getElementById('cpAvatarPreview');

  if (avatarInput) {
    avatarInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file || !file.type.startsWith('image/')) return;
      if (file.size > 2 * 1024 * 1024) {
        alert('Image must be under 2MB.');
        return;
      }
      const reader = new FileReader();
      reader.onload = function (e) {
        if (profileAvatarImg) profileAvatarImg.src = e.target.result;
        if (headerAvatarImg)  headerAvatarImg.src  = e.target.result;
      };
      reader.readAsDataURL(file);
    });
  }


  /* ══════════════════════════════════════════════════════════
     5. PASSWORD VISIBILITY TOGGLE
  ══════════════════════════════════════════════════════════ */
  document.querySelectorAll('.cp-pass-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      const isText = input.type === 'text';
      input.type = isText ? 'password' : 'text';
      btn.querySelector('i').className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
  });


  /* ══════════════════════════════════════════════════════════
     6. SAVE PROFILE FEEDBACK
  ══════════════════════════════════════════════════════════ */
  const saveBtn      = document.getElementById('cpSaveBtn');
  const saveFeedback = document.getElementById('cpSaveFeedback');

  if (saveBtn && saveFeedback) {
    saveBtn.addEventListener('click', () => {
      saveBtn.disabled    = true;
      saveBtn.innerHTML   = '<i class="bi bi-hourglass-split"></i> Saving…';

      setTimeout(() => {
        saveBtn.disabled    = false;
        saveBtn.innerHTML   = '<i class="bi bi-check2-circle"></i> Save Changes';
        saveFeedback.textContent = '✓ Changes saved successfully';
        saveFeedback.classList.add('show');

        setTimeout(() => saveFeedback.classList.remove('show'), 3500);
      }, 900);
    });
  }

})();