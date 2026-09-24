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

                <!-- Forgot password: SMS-based reset (default for parents with no email) -->
                <div id="forgot-form" style="display:none">
                    <div id="forgot-step-phone">
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-input" id="forgot-phone" placeholder="e.g. 0977123456 or +260977123456" inputmode="tel">
                            <div class="text-xs text-gray" style="margin-top:4px">We'll SMS a 6-digit reset code to this number.</div>
                        </div>
                        <button class="btn btn-primary" id="forgot-send-otp-btn">Send SMS Code</button>
                        <button class="btn btn-outline" id="back-to-login">Back to Login</button>
                        <div style="text-align:center;margin-top:10px;font-size:0.8rem">
                            <a href="#" class="forgot-link" id="use-email-reset">Use email reset instead</a>
                        </div>
                    </div>
                    <div id="forgot-step-otp" style="display:none">
                        <div class="form-group">
                            <label class="form-label">SMS Code</label>
                            <input type="text" class="form-input" id="forgot-otp" placeholder="6-digit code" inputmode="numeric" maxlength="6">
                        </div>
                        <div class="form-group">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-input" id="forgot-new-password" placeholder="At least 8 characters" autocomplete="new-password">
                        </div>
                        <button class="btn btn-primary" id="forgot-reset-btn">Set New Password</button>
                        <button class="btn btn-outline" id="back-to-login-2">Back to Login</button>
                    </div>
                    <div id="forgot-step-email" style="display:none">
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-input" id="forgot-email" placeholder="Enter your email">
                        </div>
                        <button class="btn btn-primary" id="forgot-btn">Send Reset Link</button>
                        <button class="btn btn-outline" id="back-to-login-3">Back to Login</button>
                    </div>
                </div>

                <form id="login-form">
                    <div class="form-group">
                        <label class="form-label">Phone, Email, or Username</label>
                        <input type="text" class="form-input" id="login-input" placeholder="e.g. 0977123456 or your email" value="${savedLogin}" autocomplete="username" required>
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

    // Forgot password toggle (SMS-first; email is a fallback)
    const forgotLink = document.getElementById('forgot-link');
    const forgotForm = document.getElementById('forgot-form');
    const loginForm = document.getElementById('login-form');
    const stepPhone = document.getElementById('forgot-step-phone');
    const stepOtp   = document.getElementById('forgot-step-otp');
    const stepEmail = document.getElementById('forgot-step-email');
    const msgEl     = document.getElementById('login-msg');

    const showStep = (which) => {
        stepPhone.style.display = which === 'phone' ? 'block' : 'none';
        stepOtp.style.display   = which === 'otp' ? 'block' : 'none';
        stepEmail.style.display = which === 'email' ? 'block' : 'none';
    };

    forgotLink.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display = 'none';
        forgotForm.style.display = 'block';
        showStep('phone');
        msgEl.innerHTML = '';
    });

    const backHandler = () => {
        forgotForm.style.display = 'none';
        loginForm.style.display = 'block';
        msgEl.innerHTML = '';
    };
    document.getElementById('back-to-login').addEventListener('click', backHandler);
    document.getElementById('back-to-login-2').addEventListener('click', backHandler);
    document.getElementById('back-to-login-3').addEventListener('click', backHandler);

    document.getElementById('use-email-reset').addEventListener('click', (e) => {
        e.preventDefault();
        showStep('email');
    });

    // Step 1 — request OTP via SMS
    let resetPhone = '';
    document.getElementById('forgot-send-otp-btn').addEventListener('click', async () => {
        const phone = document.getElementById('forgot-phone').value.trim();
        if (!phone) return;
        const btn = document.getElementById('forgot-send-otp-btn');
        btn.disabled = true; btn.textContent = 'Sending...';
        try {
            await api.forgotPasswordSms(phone);
            resetPhone = phone;
            msgEl.innerHTML = '<div class="form-msg success">If the phone is on file, an SMS code is on its way. Check your messages.</div>';
            showStep('otp');
        } catch (err) {
            msgEl.innerHTML = `<div class="form-msg">${err.message}</div>`;
        }
        btn.disabled = false; btn.textContent = 'Send SMS Code';
    });

    // Step 2 — verify OTP + set new password
    document.getElementById('forgot-reset-btn').addEventListener('click', async () => {
        const otp = document.getElementById('forgot-otp').value.trim();
        const newPassword = document.getElementById('forgot-new-password').value;
        if (!otp || !newPassword || newPassword.length < 8) {
            msgEl.innerHTML = '<div class="form-msg">Enter the 6-digit code and a new password of at least 8 characters.</div>';
            return;
        }
        const btn = document.getElementById('forgot-reset-btn');
        btn.disabled = true; btn.textContent = 'Updating...';
        try {
            await api.resetPasswordSms(resetPhone, otp, newPassword);
            msgEl.innerHTML = '<div class="form-msg success">Password updated. Sign in with your new password.</div>';
            backHandler();
        } catch (err) {
            msgEl.innerHTML = `<div class="form-msg">${err.message}</div>`;
        }
        btn.disabled = false; btn.textContent = 'Set New Password';
    });

    // Email-based reset (fallback)
    document.getElementById('forgot-btn').addEventListener('click', async () => {
        const email = document.getElementById('forgot-email').value;
        if (!email) return;
        const btn = document.getElementById('forgot-btn');
        btn.disabled = true; btn.textContent = 'Sending...';
        try {
            await api.forgotPassword(email);
            msgEl.innerHTML = '<div class="form-msg success">Reset link sent to your email.</div>';
        } catch (err) {
            msgEl.innerHTML = `<div class="form-msg">${err.message}</div>`;
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
                localStorage.setItem('saved_pass', password);
            } else {
                localStorage.removeItem('remember_login');
                localStorage.removeItem('saved_login');
                localStorage.removeItem('saved_pass');
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
