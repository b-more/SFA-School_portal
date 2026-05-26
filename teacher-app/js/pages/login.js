export function renderLogin(container, settings, api) {
    const logo = settings?.logo
        ? `<img src="${settings.logo}" alt="">`
        : '<div style="font-size:1.5rem;font-weight:700;color:#fff">SF</div>';

    container.innerHTML = `
        <div class="login-page">
            <div class="login-header">
                <div class="login-logo">${logo}</div>
                <div class="login-title">${settings?.name || 'St. Francis of Assisi'}</div>
                <div class="login-subtitle">Teacher Portal</div>
            </div>
            <div class="login-form-wrap">
                <div class="login-welcome">Welcome, Teacher</div>
                <div class="login-welcome-sub">Sign in to manage your classes</div>
                <div id="login-error" class="form-msg" style="display:none"></div>
                <div id="login-form">
                    <div class="form-group">
                        <label class="form-label">Email or Username</label>
                        <input type="text" id="login-email" class="form-input" placeholder="teacher@school.com" autocomplete="email">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="form-input-wrap">
                            <input type="password" id="login-password" class="form-input" placeholder="Enter password" autocomplete="current-password">
                            <button class="password-toggle" type="button" id="pw-toggle">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="checkbox-wrap"><input type="checkbox" id="remember" ${localStorage.getItem('teacher_remember') === 'true' ? 'checked' : ''}><span>Remember me</span></label>
                        <a href="#" class="forgot-link" id="forgot-link">Forgot password?</a>
                    </div>
                    <button class="btn btn-primary" id="login-btn">Sign In</button>
                </div>
                <div id="forgot-form" style="display:none">
                    <div class="form-group"><label class="form-label">Email Address</label><input type="email" id="forgot-email" class="form-input" placeholder="your@email.com"></div>
                    <div id="forgot-msg" class="form-msg success" style="display:none"></div>
                    <button class="btn btn-primary" id="forgot-btn">Send Reset Link</button>
                    <button class="btn btn-outline" id="back-to-login">Back to Login</button>
                </div>
            </div>
        </div>
    `;

    // Pre-fill saved credentials
    const savedEmail = localStorage.getItem('teacher_saved_email');
    if (savedEmail) {
        document.getElementById('login-email').value = savedEmail;
    }

    const errEl = document.getElementById('login-error');
    document.getElementById('pw-toggle').addEventListener('click', () => {
        const inp = document.getElementById('login-password');
        inp.type = inp.type === 'password' ? 'text' : 'password';
    });

    document.getElementById('login-btn').addEventListener('click', async () => {
        const email = document.getElementById('login-email').value.trim();
        const password = document.getElementById('login-password').value;
        const btn = document.getElementById('login-btn');
        if (!email || !password) { errEl.textContent = 'Please enter email and password.'; errEl.style.display = ''; return; }
        errEl.style.display = 'none';
        btn.disabled = true; btn.innerHTML = '<div class="btn-spinner"></div> Signing in...';
        try {
            const data = await api.login(email, password);
            api.setToken(data.token);
            localStorage.setItem('teacher_data', JSON.stringify(data.user));
            const remember = document.getElementById('remember').checked;
            localStorage.setItem('teacher_remember', remember);
            if (remember) {
                localStorage.setItem('teacher_saved_email', email);
                localStorage.setItem('teacher_saved_pass', password);
            } else {
                localStorage.removeItem('teacher_saved_email');
                localStorage.removeItem('teacher_saved_pass');
            }
            window.location.hash = '#/dashboard';
        } catch (err) { errEl.textContent = err.message; errEl.style.display = ''; btn.disabled = false; btn.innerHTML = 'Sign In'; }
    });

    document.getElementById('login-password').addEventListener('keydown', (e) => {
        if (e.key === 'Enter') document.getElementById('login-btn').click();
    });

    document.getElementById('forgot-link').addEventListener('click', (e) => {
        e.preventDefault();
        document.getElementById('login-form').style.display = 'none';
        document.getElementById('forgot-form').style.display = '';
    });
    document.getElementById('back-to-login').addEventListener('click', () => {
        document.getElementById('forgot-form').style.display = 'none';
        document.getElementById('login-form').style.display = '';
    });
    document.getElementById('forgot-btn').addEventListener('click', async () => {
        const email = document.getElementById('forgot-email').value.trim();
        const btn = document.getElementById('forgot-btn');
        const msg = document.getElementById('forgot-msg');
        if (!email) return;
        btn.disabled = true; btn.textContent = 'Sending...';
        try { await api.forgotPassword(email); msg.textContent = 'Reset link sent to your email.'; msg.style.display = ''; }
        catch (err) { msg.textContent = err.message; msg.className = 'form-msg'; msg.style.display = ''; }
        btn.disabled = false; btn.textContent = 'Send Reset Link';
    });
}
