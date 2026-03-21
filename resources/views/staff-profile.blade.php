<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $employee->name }} - {{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #1e3a5f;
            --navy-light: #2a4d7a;
            --red: #dc2626;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --green-600: #16a34a;
            --green-50: #f0fdf4;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--slate-100);
            color: var(--slate-700);
            min-height: 100vh;
        }

        /* Hero Header */
        .hero {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            padding: 0;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: rgba(255,255,255,0.03);
            border-radius: 50%;
        }
        .hero-inner {
            max-width: 640px;
            margin: 0 auto;
            padding: 40px 24px 32px;
            text-align: center;
            position: relative;
        }

        /* School branding */
        .school-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        .school-logo {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: white;
            padding: 3px;
            object-fit: contain;
        }
        .school-logo-placeholder {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            color: var(--navy);
        }
        .school-info {
            text-align: left;
        }
        .school-name {
            color: white;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-motto {
            color: rgba(255,255,255,0.6);
            font-size: 11px;
            font-style: italic;
        }

        /* Avatar */
        .avatar-wrap {
            margin-bottom: 20px;
        }
        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.3);
            object-fit: cover;
            background: var(--slate-200);
        }
        .avatar-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            font-weight: 700;
            color: rgba(255,255,255,0.8);
        }

        .employee-name {
            color: white;
            font-size: 28px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 6px;
        }
        .employee-position {
            color: rgba(255,255,255,0.7);
            font-size: 15px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }
        .employee-department {
            color: rgba(255,255,255,0.5);
            font-size: 13px;
        }

        .status-badge {
            display: inline-block;
            margin-top: 12px;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-active {
            background: rgba(34, 197, 94, 0.2);
            color: #86efac;
        }

        /* Red accent */
        .accent-bar {
            height: 4px;
            background: var(--red);
        }

        /* Content */
        .content {
            max-width: 640px;
            margin: 0 auto;
            padding: 24px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .card-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 480px) {
            .info-grid { grid-template-columns: 1fr; }
        }
        .info-item-full {
            grid-column: 1 / -1;
        }
        .info-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }
        .info-value {
            font-size: 15px;
            font-weight: 500;
            color: var(--slate-800);
        }

        /* Contact actions */
        .contact-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .contact-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .contact-btn-phone {
            background: #f0fdf4;
            color: #15803d;
        }
        .contact-btn-phone:hover { background: #dcfce7; }
        .contact-btn-email {
            background: #eff6ff;
            color: #1d4ed8;
        }
        .contact-btn-email:hover { background: #dbeafe; }

        .contact-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .contact-icon-phone { background: #dcfce7; }
        .contact-icon-email { background: #dbeafe; }

        .contact-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            opacity: 0.7;
        }
        .contact-value {
            font-size: 15px;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 24px;
            color: var(--slate-400);
            font-size: 12px;
        }
        .footer a {
            color: var(--navy);
            text-decoration: none;
            font-weight: 600;
        }

        /* Verified badge */
        .verified {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            color: var(--green-600);
            font-weight: 600;
            margin-top: 8px;
        }
        .verified svg {
            width: 16px;
            height: 16px;
        }
    </style>
</head>
<body>
    {{-- Hero --}}
    <div class="hero">
        <div class="hero-inner">
            {{-- School branding --}}
            <div class="school-bar">
                @if($settings && $settings->school_logo)
                    <img src="{{ asset('storage/' . $settings->school_logo) }}" alt="Logo" class="school-logo">
                @elseif(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="school-logo">
                @else
                    <div class="school-logo-placeholder">S</div>
                @endif
                <div class="school-info">
                    <div class="school-name">{{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                    @if($settings && $settings->school_motto)
                        <div class="school-motto">"{{ $settings->school_motto }}"</div>
                    @endif
                </div>
            </div>

            {{-- Avatar --}}
            <div class="avatar-wrap">
                @if($employee->profile_photo)
                    <img src="{{ asset('storage/' . $employee->profile_photo) }}" alt="{{ $employee->name }}" class="avatar">
                @else
                    <div class="avatar-placeholder">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                @endif
            </div>

            <div class="employee-name">{{ $employee->name }}</div>
            <div class="employee-position">{{ $employee->position ?? 'Staff' }}</div>
            @if($employee->department)
                <div class="employee-department">{{ ucfirst(str_replace('_', ' ', $employee->department)) }}</div>
            @endif

            @if($employee->status === 'active')
                <span class="status-badge status-active">Verified Staff</span>
            @endif
        </div>
    </div>
    <div class="accent-bar"></div>

    {{-- Content --}}
    <div class="content">
        {{-- Contact Actions --}}
        <div class="card">
            <div class="card-title">Contact</div>
            <div class="contact-actions">
                @if($employee->phone)
                <a href="tel:{{ $employee->phone }}" class="contact-btn contact-btn-phone">
                    <div class="contact-icon contact-icon-phone">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                    </div>
                    <div>
                        <div class="contact-label">Phone</div>
                        <div class="contact-value">{{ $employee->phone }}</div>
                    </div>
                </a>
                @endif

                @if($employee->email)
                <a href="mailto:{{ $employee->email }}" class="contact-btn contact-btn-email">
                    <div class="contact-icon contact-icon-email">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                    </div>
                    <div>
                        <div class="contact-label">Email</div>
                        <div class="contact-value">{{ $employee->email }}</div>
                    </div>
                </a>
                @endif
            </div>
        </div>

        {{-- Employment Details --}}
        <div class="card">
            <div class="card-title">Employment Details</div>
            <div class="info-grid">
                @if($employee->employee_id || $employee->employee_number)
                <div>
                    <div class="info-label">Employee ID</div>
                    <div class="info-value">{{ $employee->employee_id ?: $employee->employee_number }}</div>
                </div>
                @endif

                <div>
                    <div class="info-label">Position</div>
                    <div class="info-value">{{ $employee->position ?? 'Staff' }}</div>
                </div>

                @if($employee->department)
                <div>
                    <div class="info-label">Department</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $employee->department)) }}</div>
                </div>
                @endif

                @if($employee->employment_type)
                <div>
                    <div class="info-label">Employment Type</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $employee->employment_type)) }}</div>
                </div>
                @endif

                @if($employee->joining_date)
                <div>
                    <div class="info-label">Joined</div>
                    <div class="info-value">{{ $employee->joining_date->format('F Y') }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- School Info --}}
        <div class="card">
            <div class="card-title">School</div>
            <div class="info-grid">
                <div class="info-item-full">
                    <div class="info-label">Institution</div>
                    <div class="info-value">{{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
                </div>

                @if($settings && $settings->address)
                <div class="info-item-full">
                    <div class="info-label">Address</div>
                    <div class="info-value">
                        {{ $settings->address }}@if($settings->city), {{ $settings->city }}@endif
                    </div>
                </div>
                @endif

                @if($settings && $settings->phone)
                <div>
                    <div class="info-label">School Phone</div>
                    <div class="info-value">{{ $settings->phone }}</div>
                </div>
                @endif

                @if($settings && $settings->email)
                <div>
                    <div class="info-label">School Email</div>
                    <div class="info-value">{{ $settings->email }}</div>
                </div>
                @endif

                @if($settings && $settings->website)
                <div class="info-item-full">
                    <div class="info-label">Website</div>
                    <div class="info-value">{{ $settings->website }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</p>
            @if($settings && $settings->website)
                <p style="margin-top: 4px;"><a href="{{ $settings->website }}">{{ $settings->website }}</a></p>
            @endif
        </div>
    </div>
</body>
</html>
