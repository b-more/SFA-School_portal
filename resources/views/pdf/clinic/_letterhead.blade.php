@php
    $ss = $schoolSettings ?? \App\Models\SchoolSettings::getInstance();
    $logoPath = null;
    if ($ss && $ss->school_logo && file_exists(public_path('storage/' . $ss->school_logo))) {
        $logoPath = public_path('storage/' . $ss->school_logo);
    } elseif (file_exists(public_path('images/logo.png'))) {
        $logoPath = public_path('images/logo.png');
    }
    $addressLine = trim(implode(', ', array_filter([$ss->address ?? null, $ss->city ?? null, $ss->country ?? null])));
    if ($ss && ! empty($ss->postal_code)) $addressLine .= ' · P.O. Box ' . $ss->postal_code;
    $contactLine = implode('  ·  ', array_filter([
        ($ss->phone ?? null) ? 'Tel ' . $ss->phone : null,
        ($ss->email ?? null) ? $ss->email : null,
    ]));
@endphp

<table class="letterhead">
    <tr>
        <td class="logo-cell">@if($logoPath)<img src="{{ $logoPath }}" class="logo" alt="">@endif</td>
        <td>
            <div class="school-name">{{ $ss->school_name ?? 'St. Francis of Assisi Private School' }}</div>
            <div class="school-motto">&ldquo;{{ $ss->school_motto ?? 'For God and Country' }}&rdquo;</div>
            <div class="school-meta">
                @if($addressLine) <strong>{{ $addressLine }}</strong><br>@endif
                @if($contactLine) {{ $contactLine }} @endif
            </div>
        </td>
    </tr>
</table>
<div class="rule"></div>

<div class="doc-title">School Clinic · {{ $subtitle }}</div>
<div class="doc-meta">
    Generated {{ now()->format('d M Y H:i') }} · Ref {{ $refCode }}
</div>
