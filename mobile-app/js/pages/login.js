export function renderLogin(container, settings, api) {
    const logoHtml = settings?.logo
        ? `<img src="${settings.logo}" alt="Logo">`
        : `<div style="font-size:1.8rem;font-weight:700;color:#fff">SF</div>`;

    const remembered = localStorage.getItem('remember_login') === 'true';
    const savedLogin = remembered ? (localStorage.getItem('saved_login') || '') : '';

    container.innerHTML = `
        <div class="login-page">
            <div class="login-header">
                <div class="login-logo">${logoHtml}</div>
                <div class="login-title">${settings?.name || 'St. Francis of Assisi'}</div>
                <div class="login-subtitle">Parent Portal</div>
            </div>
            <div class="login-form-wrap">
                <div class="login-welcome">Welcome Back</div>
                <div class="login-welcome-sub">Sign in to view your children's progress</div>

                <div id="login-msg"></div>
                <div id="forgot-form" style="display:none">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" id="forgot-email" placeholder="Enter your email">
                    </div>
                    <button class="btn btn-primary" id="forgot-btn">Send Reset Link</button>
                    <button class="btn btn-outline" id="back-to-login">Back to Login</button>
                </div>
                <form id="login-form">
                    <div class="form-group">
                        <label class="form-label">Email or Username</label>
                        <input type="text" class="form-input" id="login-input" placeholder="Enter your email or username" value="${savedLogin}" autocomplete="username" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="form-input-wrap">
                            <input type="password" class="form-input" id="password-input" placeholder="Enter your password" autocomplete="current-password" required>
                            <button type="button" class="password-toggle" id="pw-toggle">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="checkbox-wrap">
                            <input type="checkbox" id="remember-me" ${remembered ? 'checked' : ''}>
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="forgot-link" id="forgot-link">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary" id="login-btn">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    `;

    // Password toggle
    const pwInput = document.getElementById('password-input');
    const pwToggle = document.getElementById('pw-toggle');
    pwToggle.addEventListener('click', () => {
        const isPassword = pwInput.type === 'password';
        pwInput.type = isPassword ? 'text' : 'password';
        pwToggle.innerHTML = isPassword
            ? '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>'
            : '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
    });

    // Forgot password toggle
    const forgotLink = document.getElementById('forgot-link');
    const forgotForm = document.getElementById('forgot-form');
    const loginForm = document.getElementById('login-form');
    const backBtn = document.getElementById('back-to-login');

    forgotLink.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display = 'none';
        forgotForm.style.display = 'block';
    });
    backBtn.addEventListener('click', () => {
        forgotForm.style.display = 'none';
        loginForm.style.display = 'block';
        document.getElementById('login-msg').innerHTML = '';
    });

    // Forgot password submit
    document.getElementById('forgot-btn').addEventListener('click', async () => {
        const email = document.getElementById('forgot-email').value;
        if (!email) return;
        const btn = document.getElementById('forgot-btn');
        btn.disabled = true; btn.textContent = 'Sending...';
        try {
            await api.forgotPassword(email);
            document.getElementById('login-msg').innerHTML = '<div class="form-msg success">Reset link sent to your email.</div>';
        } catch (err) {
            document.getElementById('login-msg').innerHTML = `<div class="form-msg">${err.message}</div>`;
        }
        btn.disabled = false; btn.textContent = 'Send Reset Link';
    });

    // Login submit
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const login = document.getElementById('login-input').value.trim();
        const password = pwInput.value;
        const remember = document.getElementById('remember-me').checked;
        const btn = document.getElementById('login-btn');
        const msgEl = document.getElementById('login-msg');

        if (!login || !password) { msgEl.innerHTML = '<div class="form-msg">Please fill in all fields.</div>'; return; }

        btn.disabled = true;
        btn.innerHTML = '<span class="btn-spinner"></span> Signing in...';
        msgEl.innerHTML = '';

        try {
            const data = await api.login(login, password, remember);
            api.setToken(data.token);

            if (remember) {
                localStorage.setItem('remember_login', 'true');
                localStorage.setItem('saved_login', login);
            } else {
                localStorage.removeItem('remember_login');
                localStorage.removeItem('saved_login');
            }

            // Store user data
            localStorage.setItem('user_data', JSON.stringify(data.user));
            localStorage.setItem('children_data', JSON.stringify(data.children));

            window.location.hash = '#/dashboard';
        } catch (err) {
            msgEl.innerHTML = `<div class="form-msg">${err.message}</div>`;
            btn.disabled = false;
            btn.innerHTML = 'Sign In';
        }
    });
}
