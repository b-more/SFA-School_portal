@php
    $schoolName = $settings->school_name ?? 'St. Francis of Assisi';
    $shortName  = 'St. Francis of Assisi';
    $motto      = $settings->school_motto ?? 'Faith · Family · Future';
    $phone      = $settings->phone        ?? '+260 977 000 000';
    $email      = $settings->email        ?? 'info@stfrancisofassisizm.com';
    $logoPath   = $settings && $settings->school_logo
                    ? asset('storage/' . ltrim($settings->school_logo, '/'))
                    : asset('images/logo.png');
    $year       = now()->year;
@endphp
