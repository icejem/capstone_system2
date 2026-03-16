
    const sidebar = document.getElementById('sidebar');
    const menuBtn = document.getElementById('menuBtn');
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationPanel = document.getElementById('notificationPanel');
    const overviewSection = document.getElementById('overviewSection');
    const studentsSection = document.getElementById('studentsSection');
    const instructorsSection = document.getElementById('instructorsSection');
    const consultationsSection = document.getElementById('consultationsSection');
    const overviewLink = document.getElementById('overviewLink');
    const studentsLink = document.getElementById('studentsLink');
    const instructorsLink = document.getElementById('instructorsLink');
    const consultationsLink = document.getElementById('consultationsLink');
    const overviewTab = document.getElementById('overviewTab');
    const studentsTab = document.getElementById('studentsTab');
    const instructorsTab = document.getElementById('instructorsTab');
    const consultationsTab = document.getElementById('consultationsTab');
    const studentSearch = document.getElementById('studentSearch');
    const studentStatusFilter = document.getElementById('studentStatusFilter');
    const studentTableBody = document.getElementById('studentTableBody');
    const instructorSearch = document.getElementById('instructorSearch');
    const instructorStatusFilter = document.getElementById('instructorStatusFilter');
    const instructorTableBody = document.getElementById('instructorTableBody');
    const consultationSearch = document.getElementById('consultationSearch');
    const consultationStatusFilter = document.getElementById('consultationStatusFilter');
    const consultationTableBody = document.getElementById('consultationTableBody');
    const consultationDetailsModal = document.getElementById('consultationDetailsModal');
    const consultationViewButtons = document.querySelectorAll('.consultation-view-btn');
    const closeConsultationDetailsModal = document.getElementById('closeConsultationDetailsModal');
    const detailsId = document.getElementById('detailsId');
    const detailsStatus = document.getElementById('detailsStatus');
    const detailsStudent = document.getElementById('detailsStudent');
    const detailsInstructor = document.getElementById('detailsInstructor');
    const detailsDateTime = document.getElementById('detailsDateTime');
    const detailsMode = document.getElementById('detailsMode');
    const detailsType = document.getElementById('detailsType');
    const detailsSummaryText = document.getElementById('detailsSummaryText');
    const detailsTranscriptBtn = document.getElementById('detailsTranscriptBtn');
    const manageUserModal = document.getElementById('manageUserModal');
    const manageUserButtons = document.querySelectorAll('.manage-user-btn');
    const closeManageUserModal = document.getElementById('closeManageUserModal');
    const manageAvatar = document.getElementById('manageAvatar');
    const manageName = document.getElementById('manageName');
    const manageEmail = document.getElementById('manageEmail');
    const manageMeta = document.getElementById('manageMeta');
    const manageRole = document.getElementById('manageRole');
    const manageJoined = document.getElementById('manageJoined');
    const manageConsultations = document.getElementById('manageConsultations');
    const manageCurrentStatus = document.getElementById('manageCurrentStatus');
    const manageStatusButtons = document.querySelectorAll('.manage-status-btn');
    const openAddInstructor = document.getElementById('openAddInstructor');
    const addInstructorModal = document.getElementById('addInstructorModal');
    const closeAddInstructor = document.getElementById('closeAddInstructor');
    const cancelAddInstructor = document.getElementById('cancelAddInstructor');
    let activeManageRow = null;

    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    if (notificationBtn && notificationPanel) {
        notificationBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            notificationPanel.classList.toggle('active');
        });

        document.addEventListener('click', (event) => {
            if (!notificationPanel.contains(event.target) && !notificationBtn.contains(event.target)) {
                notificationPanel.classList.remove('active');
            }
        });
    }

    function setActiveSidebar(linkId) {
        document.querySelectorAll('.sidebar-menu-link').forEach((link) => {
            link.classList.remove('active');
        });
        const activeLink = document.getElementById(linkId);
        if (activeLink) activeLink.classList.add('active');
    }

    function showOverview() {
        if (overviewSection) overviewSection.classList.remove('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.add('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('overviewLink');
    }

    function showStudents() {
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.remove('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.add('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('studentsLink');
    }

    function showInstructors() {
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.remove('is-hidden');
        if (consultationsSection) consultationsSection.classList.add('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.add('active');
        if (consultationsTab) consultationsTab.classList.remove('active');
        setActiveSidebar('instructorsLink');
    }

    function showConsultations() {
        if (overviewSection) overviewSection.classList.add('is-hidden');
        if (studentsSection) studentsSection.classList.add('is-hidden');
        if (instructorsSection) instructorsSection.classList.add('is-hidden');
        if (consultationsSection) consultationsSection.classList.remove('is-hidden');
        if (overviewTab) overviewTab.classList.remove('active');
        if (studentsTab) studentsTab.classList.remove('active');
        if (instructorsTab) instructorsTab.classList.remove('active');
        if (consultationsTab) consultationsTab.classList.add('active');
        setActiveSidebar('consultationsLink');
    }

    if (overviewLink) {
        overviewLink.addEventListener('click', (event) => {
            event.preventDefault();
            showOverview();
            if (sidebar && sidebar.classList.contains('open')) sidebar.classList.remove('open');
        });
    }

    if (studentsLink) {
        studentsLink.addEventListener('click', (event) => {
            event.preventDefault();
            showStudents();
            if (sidebar && sidebar.classList.contains('open')) sidebar.classList.remove('open');
        });
    }

    if (instructorsLink) {
        instructorsLink.addEventListener('click', (event) => {
            event.preventDefault();
            showInstructors();
            if (sidebar && sidebar.classList.contains('open')) sidebar.classList.remove('open');
        });
    }

    if (consultationsLink) {
        consultationsLink.addEventListener('click', (event) => {
            event.preventDefault();
            showConsultations();
            if (sidebar && sidebar.classList.contains('open')) sidebar.classList.remove('open');
        });
    }

    if (overviewTab) {
        overviewTab.addEventListener('click', (event) => {
            event.preventDefault();
            showOverview();
        });
    }

    if (studentsTab) {
        studentsTab.addEventListener('click', (event) => {
            event.preventDefault();
            showStudents();
        });
    }

    if (instructorsTab) {
        instructorsTab.addEventListener('click', (event) => {
            event.preventDefault();
            showInstructors();
        });
    }

    if (consultationsTab) {
        consultationsTab.addEventListener('click', (event) => {
            event.preventDefault();
            showConsultations();
        });
    }

    function filterStudentsTable() {
        if (!studentTableBody) return;
        const searchValue = (studentSearch?.value || '').toLowerCase().trim();
        const selectedStatus = (studentStatusFilter?.value || '').toLowerCase().trim();

        studentTableBody.querySelectorAll('tr[data-status]').forEach((row) => {
            const rowSearch = row.dataset.search || '';
            const rowStatus = (row.dataset.status || '').toLowerCase();
            const matchSearch = !searchValue || rowSearch.includes(searchValue);
            const matchStatus = !selectedStatus || rowStatus === selectedStatus;
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });
    }

    if (studentSearch) {
        studentSearch.addEventListener('input', filterStudentsTable);
    }

    if (studentStatusFilter) {
        studentStatusFilter.addEventListener('change', filterStudentsTable);
    }

    function filterInstructorsTable() {
        if (!instructorTableBody) return;
        const searchValue = (instructorSearch?.value || '').toLowerCase().trim();
        const selectedStatus = (instructorStatusFilter?.value || '').toLowerCase().trim();

        instructorTableBody.querySelectorAll('tr[data-status]').forEach((row) => {
            const rowSearch = row.dataset.search || '';
            const rowStatus = (row.dataset.status || '').toLowerCase();
            const matchSearch = !searchValue || rowSearch.includes(searchValue);
            const matchStatus = !selectedStatus || rowStatus === selectedStatus;
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });
    }

    if (instructorSearch) {
        instructorSearch.addEventListener('input', filterInstructorsTable);
    }

    if (instructorStatusFilter) {
        instructorStatusFilter.addEventListener('change', filterInstructorsTable);
    }

    function filterConsultationsTable() {
        if (!consultationTableBody) return;
        const searchValue = (consultationSearch?.value || '').toLowerCase().trim();
        const selectedStatus = (consultationStatusFilter?.value || '').toLowerCase().trim();

        consultationTableBody.querySelectorAll('tr[data-status]').forEach((row) => {
            const rowSearch = row.dataset.search || '';
            const rowStatus = (row.dataset.status || '').toLowerCase();
            const matchSearch = !searchValue || rowSearch.includes(searchValue);
            const matchStatus = !selectedStatus || rowStatus === selectedStatus;
            row.style.display = (matchSearch && matchStatus) ? '' : 'none';
        });
    }

    if (consultationSearch) {
        consultationSearch.addEventListener('input', filterConsultationsTable);
    }

    if (consultationStatusFilter) {
        consultationStatusFilter.addEventListener('change', filterConsultationsTable);
    }

    function openConsultationDetails(data) {
        if (!consultationDetailsModal) return;
        if (detailsId) detailsId.textContent = data.id || '—';
        if (detailsStatus) {
            const normalizedStatus = String(data.status || '').toLowerCase();
            const statusClass = normalizedStatus === 'pending'
                ? 'detail-status-pending'
                : (normalizedStatus === 'approved'
                    ? 'detail-status-approved'
                    : (normalizedStatus === 'completed'
                        ? 'detail-status-completed'
                        : 'detail-status-default'));
            detailsStatus.className = `detail-status-pill ${statusClass}`;
            detailsStatus.textContent = data.status || '—';
        }
        if (detailsStudent) detailsStudent.textContent = data.student || '—';
        if (detailsInstructor) detailsInstructor.textContent = data.instructor || '—';
        if (detailsDateTime) detailsDateTime.textContent = `${data.date || '—'} ${data.time || ''}`.trim();
        if (detailsMode) detailsMode.textContent = data.mode || '—';
        if (detailsType) detailsType.textContent = data.type || '—';
        if (detailsSummaryText) detailsSummaryText.textContent = data.summary || 'Summary not yet available.';
        if (detailsTranscriptBtn) {
            detailsTranscriptBtn.style.display = data.isOnline ? 'inline-flex' : 'none';
        }
        consultationDetailsModal.classList.add('open');
        consultationDetailsModal.setAttribute('aria-hidden', 'false');
    }

    function closeConsultationDetails() {
        if (!consultationDetailsModal) return;
        consultationDetailsModal.classList.remove('open');
        consultationDetailsModal.setAttribute('aria-hidden', 'true');
    }

    if (consultationViewButtons.length) {
        consultationViewButtons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                openConsultationDetails({
                    id: btn.dataset.id || '—',
                    status: btn.dataset.status || '—',
                    student: btn.dataset.student || '—',
                    instructor: btn.dataset.instructor || '—',
                    date: btn.dataset.date || '—',
                    time: btn.dataset.time || '',
                    mode: btn.dataset.mode || '—',
                    type: btn.dataset.type || '—',
                    summary: btn.dataset.summary || '',
                    isOnline: btn.dataset.online === '1',
                });
            });
        });
    }

    if (closeConsultationDetailsModal) {
        closeConsultationDetailsModal.addEventListener('click', closeConsultationDetails);
    }

    if (consultationDetailsModal) {
        consultationDetailsModal.addEventListener('click', (event) => {
            if (event.target === consultationDetailsModal) {
                closeConsultationDetails();
            }
        });
    }

    function applyStatusPill(el, status) {
        if (!el) return;
        const normalized = String(status || '').toLowerCase();
        const pillClass = normalized === 'active'
            ? 'status-active'
            : (normalized === 'inactive'
                ? 'status-inactive'
                : (normalized === 'suspended' ? 'status-suspended' : 'status-inactive'));
        el.className = `status-tag ${pillClass}`;
        el.textContent = normalized || 'inactive';
    }

    function openManageModal(data, row) {
        if (!manageUserModal) return;
        activeManageRow = row || null;
        if (manageAvatar) manageAvatar.textContent = (data.name || 'U').charAt(0).toUpperCase();
        if (manageName) manageName.textContent = data.name || '—';
        if (manageEmail) manageEmail.textContent = data.email || '—';
        if (manageMeta) manageMeta.textContent = data.meta || '—';
        if (manageRole) manageRole.textContent = data.role || '—';
        if (manageJoined) manageJoined.textContent = data.joined || '—';
        if (manageConsultations) manageConsultations.textContent = data.consultations || '0';
        applyStatusPill(manageCurrentStatus, data.status || 'inactive');
        manageUserModal.classList.add('open');
        manageUserModal.setAttribute('aria-hidden', 'false');
    }

    function closeManageModal() {
        if (!manageUserModal) return;
        manageUserModal.classList.remove('open');
        manageUserModal.setAttribute('aria-hidden', 'true');
        activeManageRow = null;
    }

    function openAddInstructorModal() {
        if (!addInstructorModal) return;
        addInstructorModal.classList.add('open');
        addInstructorModal.setAttribute('aria-hidden', 'false');
    }

    function closeAddInstructorModal() {
        if (!addInstructorModal) return;
        addInstructorModal.classList.remove('open');
        addInstructorModal.setAttribute('aria-hidden', 'true');
    }

    if (openAddInstructor) {
        openAddInstructor.addEventListener('click', openAddInstructorModal);
    }

    if (closeAddInstructor) {
        closeAddInstructor.addEventListener('click', closeAddInstructorModal);
    }

    if (cancelAddInstructor) {
        cancelAddInstructor.addEventListener('click', closeAddInstructorModal);
    }

    if (addInstructorModal) {
        addInstructorModal.addEventListener('click', (event) => {
            if (event.target === addInstructorModal) {
                closeAddInstructorModal();
            }
        });
    }

    const hasAddInstructorErrors = null);
    if (hasAddInstructorErrors) {
        openAddInstructorModal();
    }

    if (manageUserButtons.length) {
        manageUserButtons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                const row = btn.closest('tr');
                openManageModal({
                    role: btn.dataset.role || '—',
                    name: btn.dataset.name || '—',
                    email: btn.dataset.email || '—',
                    meta: btn.dataset.meta || '—',
                    joined: btn.dataset.joined || '—',
                    consultations: btn.dataset.consultations || '0',
                    status: btn.dataset.status || 'inactive',
                }, row);
            });
        });
    }

    if (manageStatusButtons.length) {
        manageStatusButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const nextStatus = btn.dataset.statusValue || 'inactive';
                applyStatusPill(manageCurrentStatus, nextStatus);

                if (activeManageRow) {
                    activeManageRow.dataset.status = nextStatus;
                    const rowPill = activeManageRow.querySelector('.status-tag');
                    applyStatusPill(rowPill, nextStatus);
                }
            });
        });
    }

    if (closeManageUserModal) {
        closeManageUserModal.addEventListener('click', closeManageModal);
    }

    if (manageUserModal) {
        manageUserModal.addEventListener('click', (event) => {
            if (event.target === manageUserModal) {
                closeManageModal();
            }
        });
    }
