// API calls go to /api on the same origin — nginx proxies to the Laravel backend
const API_BASE = '/api';

// Server base for resolving asset URLs (logos, files) — same origin via /storage proxy
export const SERVER_BASE = '';

class Api {
    constructor() {
        this.token = localStorage.getItem('auth_token');
    }

    setToken(token) {
        this.token = token;
        if (token) localStorage.setItem('auth_token', token);
        else localStorage.removeItem('auth_token');
    }

    isAuthenticated() {
        return !!this.token;
    }

    async request(path, options = {}) {
        const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json', ...options.headers };
        if (this.token) headers['Authorization'] = `Bearer ${this.token}`;

        try {
            const res = await fetch(`${API_BASE}${path}`, { ...options, headers });

            // Auth token expired — redirect to login (but not for the login endpoint itself)
            if (res.status === 401 && path !== '/login') {
                this.setToken(null);
                window.location.hash = '#/login';
                throw new Error('Session expired. Please sign in again.');
            }

            const data = await res.json();

            if (!res.ok) {
                // Laravel validation errors (422)
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0];
                    throw new Error(Array.isArray(firstError) ? firstError[0] : firstError);
                }
                throw new Error(data.message || 'Something went wrong.');
            }

            return data;
        } catch (err) {
            if (err instanceof TypeError) throw new Error('Unable to connect. Check your internet connection.');
            throw err;
        }
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

    // Auth
    login(login, password, remember) { return this.post('/login', { login, password, remember }); }
    logout() { return this.post('/logout', {}); }
    forgotPassword(email) { return this.post('/forgot-password', { email }); }
    getUser() { return this.get('/user'); }
    getSchoolSettings() { return this.get('/school-settings'); }

    // Append token to download URLs so the Laravel web routes can authenticate
    downloadUrl(path) {
        const sep = path.includes('?') ? '&' : '?';
        return `${path}${sep}token=${this.token}`;
    }

    // Data
    getDashboard() { return this.get('/dashboard'); }
    getChildren() { return this.get('/children'); }
    getAttendance(id) { return this.get(`/children/${id}/attendance`); }
    getFees(id) { return this.get(`/children/${id}/fees`); }
    getResults(id) { return this.get(`/children/${id}/results`); }
    getHomework(id) { return this.get(`/children/${id}/homework`); }
    getReportCards(id) { return this.get(`/children/${id}/report-cards`); }
    getTimetable(id) { return this.get(`/children/${id}/timetable`); }
    getBookLoans(id) { return this.get(`/children/${id}/book-loans`); }
    getBusPayments(id) { return this.get(`/children/${id}/bus-payments`); }
    getEvents() { return this.get('/events'); }
    getPayments() { return this.get('/payments'); }
    getNotices() { return this.get('/notices'); }
    getNews() { return this.get('/news'); }
    getComplaints() { return this.get('/complaints'); }
    createComplaint(studentId, data) { return this.post(`/children/${studentId}/complaints`, data); }
    getSchoolCalendar() { return this.get('/school-calendar'); }
    // Quizzes
    getQuizzes(studentId) { return this.get(`/children/${studentId}/quizzes`); }
    getQuiz(studentId, quizId) { return this.get(`/children/${studentId}/quizzes/${quizId}`); }
    startQuiz(studentId, quizId) { return this.post(`/children/${studentId}/quizzes/${quizId}/start`, {}); }
    submitQuiz(studentId, quizId, attemptId, answers) { return this.post(`/children/${studentId}/quizzes/${quizId}/submit`, { attempt_id: attemptId, answers }); }
    // CBC assessments
    getAssessments(studentId) { return this.get(`/children/${studentId}/assessments`); }
    getAssessment(studentId, id) { return this.get(`/children/${studentId}/assessments/${id}`); }
    submitAssessment(studentId, id, answers) { return this.post(`/children/${studentId}/assessments/${id}/submit`, { answers }); }
    submitHomework(studentId, homeworkId, content, file) {
        if (file) {
            const formData = new FormData();
            formData.append('content', content || '');
            formData.append('file', file);
            return this.uploadForm(`/children/${studentId}/homework/${homeworkId}/submit`, formData);
        }
        return this.post(`/children/${studentId}/homework/${homeworkId}/submit`, { content });
    }

    // Payments
    initiatePayment(studentId, amount, mobileNumber) { return this.post(`/children/${studentId}/pay`, { amount, mobile_number: mobileNumber }); }
    checkPaymentStatus(paymentId) { return this.post('/payment-status', { payment_id: paymentId }); }

    // Push notifications
    getVapidKey() { return this.get('/push/vapid-key'); }
    pushSubscribe(subscription) { return this.post('/push/subscribe', subscription); }
    pushUnsubscribe(endpoint) { return this.post('/push/unsubscribe', { endpoint }); }
}

export const api = new Api();
