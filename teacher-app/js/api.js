const API_BASE = '/teacher-api';
export const SERVER_BASE = '';

class Api {
    constructor() { this.token = localStorage.getItem('teacher_token'); }
    setToken(token) { this.token = token; if (token) localStorage.setItem('teacher_token', token); else localStorage.removeItem('teacher_token'); }
    isAuthenticated() { return !!this.token; }

    async request(path, options = {}) {
        const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', ...options.headers };
        if (this.token) headers['Authorization'] = `Bearer ${this.token}`;
        try {
            const res = await fetch(`${API_BASE}${path}`, { ...options, headers });
            if (res.status === 401 && path !== '/login') { this.setToken(null); window.location.hash = '#/login'; throw new Error('Session expired.'); }
            const data = await res.json();
            if (!res.ok) { if (data.errors) { const f = Object.values(data.errors)[0]; throw new Error(Array.isArray(f) ? f[0] : f); } throw new Error(data.message || 'Something went wrong.'); }
            return data;
        } catch (err) { if (err instanceof TypeError) throw new Error('Unable to connect. Check your internet.'); throw err; }
    }

    get(path) { return this.request(path); }
    post(path, body) { return this.request(path, { method: 'POST', body: JSON.stringify(body) }); }

    async uploadForm(path, formData) {
        const headers = { 'Accept': 'application/json' };
        if (this.token) headers['Authorization'] = `Bearer ${this.token}`;
        try {
            const res = await fetch(`${API_BASE}${path}`, { method: 'POST', headers, body: formData });
            if (res.status === 401) { this.setToken(null); window.location.hash = '#/login'; throw new Error('Session expired.'); }
            const data = await res.json();
            if (!res.ok) { if (data.errors) { const f = Object.values(data.errors)[0]; throw new Error(Array.isArray(f) ? f[0] : f); } throw new Error(data.message || 'Something went wrong.'); }
            return data;
        } catch (err) { if (err instanceof TypeError) throw new Error('Unable to connect.'); throw err; }
    }

    downloadUrl(path) { const sep = path.includes('?') ? '&' : '?'; return `${path}${sep}token=${this.token}`; }

    // Auth
    login(login, password) { return this.post('/login', { login, password }); }
    logout() { return this.post('/logout', {}); }
    forgotPassword(email) { return this.post('/forgot-password', { email }); }
    getUser() { return this.get('/user'); }
    getSchoolSettings() { return this.get('/school-settings'); }

    // Dashboard
    getDashboard() { return this.get('/dashboard'); }
    getHeadDashboard() { return this.get('/head-dashboard'); }

    // Classes
    getMyClasses() { return this.get('/my-classes'); }
    getClassStudents(classSectionId) { return this.get(`/class/${classSectionId}/students`); }

    // Attendance
    markAttendance(data) { return this.post('/attendance/mark', data); }
    getAttendance(classSectionId, date) { return this.get(`/attendance/${classSectionId}?date=${date || new Date().toISOString().split('T')[0]}`); }

    // Homework
    getMyHomework() { return this.get('/homework'); }
    createHomework(data) {
        if (data.file) {
            const fd = new FormData();
            Object.entries(data).forEach(([k, v]) => { if (v !== null && v !== undefined) fd.append(k, v); });
            return this.uploadForm('/homework/create', fd);
        }
        return this.post('/homework/create', data);
    }
    getHomeworkSubmissions(homeworkId) { return this.get(`/homework/${homeworkId}/submissions`); }
    gradeSubmission(submissionId, data) { return this.post(`/homework/submission/${submissionId}/grade`, data); }

    // Results
    enterResults(data) { return this.post('/results/enter', data); }
    getResults(classSectionId, subjectId) { return this.get(`/results/${classSectionId}/${subjectId}`); }

    // Timetable
    getMyTimetable() { return this.get('/timetable'); }

    // Notices
    getNotices() { return this.get('/notices'); }

    // Profile & Employment
    getProfile() { return this.get('/profile'); }
    getPayslips() { return this.get('/payslips'); }
    getMyStudents() { return this.get('/my-students'); }

    // Analytics
    getAttendanceAnalytics(classSectionId) { return this.get(`/attendance-analytics/${classSectionId}`); }
    getHomeworkAnalytics() { return this.get('/homework-analytics'); }
    getStudentPerformance(studentId) { return this.get(`/student-performance/${studentId}`); }

    // Communication
    sendClassNotice(data) { return this.post('/send-notice', data); }

    // Leave
    getLeaveBalances() { return this.get('/leave-balances'); }
    getLeaveApplications() { return this.get('/leave-applications'); }
    applyLeave(data) { return this.post('/leave/apply', data); }

    // Report Card Comments
    getReportCardStudents() { return this.get('/report-card-students'); }
    saveReportCardComment(data) { return this.post('/report-card-comment', data); }

    // Directories
    getParentContacts() { return this.get('/parent-contacts'); }
    getStaffDirectory() { return this.get('/staff-directory'); }

    // Calendar
    getSchoolCalendar() { return this.get('/school-calendar'); }

    // CPD
    getCpdDashboard() { return this.get('/cpd/dashboard'); }
    getCpdActivities() { return this.get('/cpd/activities'); }
    createCpdActivity(data) {
        if (data.certificate) { const fd = new FormData(); Object.entries(data).forEach(([k,v]) => { if (v != null) fd.append(k,v); }); return this.uploadForm('/cpd/activities', fd); }
        return this.post('/cpd/activities', data);
    }
    getCpdGoals() { return this.get('/cpd/goals'); }
    createCpdGoal(data) { return this.post('/cpd/goals', data); }
    updateCpdGoal(goalId, data) { return this.post(`/cpd/goals/${goalId}/update`, data); }
    getCpdResources() { return this.get('/cpd/resources'); }
    shareCpdResource(data) {
        if (data.file) { const fd = new FormData(); Object.entries(data).forEach(([k,v]) => { if (v != null) fd.append(k,v); }); return this.uploadForm('/cpd/resources', fd); }
        return this.post('/cpd/resources', data);
    }
    getCpdObservations() { return this.get('/cpd/observations'); }
    saveObservationReflection(obsId, reflection) { return this.post(`/cpd/observations/${obsId}/reflection`, { reflection }); }
    getCpdTermBreakdown() { return this.get('/cpd/term-breakdown'); }
    getCpdTemplates() { return this.get('/cpd/templates'); }
    quickLogCpd(templateId, date) { return this.post('/cpd/quick-log', { template_id: templateId, date }); }
    approveCpdActivity(id, status, remarks) { return this.post(`/cpd/activities/${id}/approve`, { status, remarks }); }
    getPendingApprovals() { return this.get('/cpd/pending-approvals'); }
    linkActivityToGoal(activityId, goalId) { return this.post(`/cpd/activities/${activityId}/link-goal`, { goal_id: goalId }); }
    linkActivityToObservation(activityId, obsId) { return this.post(`/cpd/activities/${activityId}/link-observation`, { observation_id: obsId }); }
    getCpdCertificates() { return this.get('/cpd/certificates'); }
    getSchoolWideCpdReport() { return this.get('/cpd/school-report'); }
    updateCpdActivity(id, data) { return this.post(`/cpd/activities/${id}/update`, data); }
    deleteCpdActivity(id) { return this.request(`/cpd/activities/${id}`, { method: 'DELETE' }); }
    deleteCpdGoal(id) { return this.request(`/cpd/goals/${id}`, { method: 'DELETE' }); }
    createObservation(data) { return this.post('/cpd/observations/create', data); }
    getCpdExport() { return this.get('/cpd/export'); }
    changePassword(data) { return this.post('/change-password', data); }
    updateProfilePhoto(file) { const fd = new FormData(); fd.append('photo', file); return this.uploadForm('/profile-photo', fd); }

    // Messaging
    getConversations() { return this.get('/messages'); }
    getChatMessages(partnerId) { return this.get(`/messages/${partnerId}`); }
    sendMessage(recipientId, message, file) {
        const fd = new FormData();
        fd.append('recipient_id', recipientId);
        if (message) fd.append('message', message);
        if (file) fd.append('file', file);
        return this.uploadForm('/messages/send', fd);
    }
}

export const api = new Api();
