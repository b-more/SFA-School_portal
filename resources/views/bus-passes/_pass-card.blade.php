<div class="bus-pass">
    {{-- Header: logo + school info + contact --}}
    <div class="pass-header">
        <div class="pass-logo">
            @if($settings && $settings->getLogoUrl())
                <img src="{{ $settings->getLogoUrl() }}" alt="Logo">
            @elseif(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            @else
                <span class="pass-logo-text">SFA</span>
            @endif
        </div>
        <div class="pass-header-text">
            <div class="pass-school-name">{{ $settings->school_name ?? 'St. Francis of Assisi Private School' }}</div>
            @if($settings->school_motto)
                <div class="pass-motto">"{{ $settings->school_motto }}"</div>
            @endif
            <div class="pass-contact-line">
                @php
                    $contactBits = array_filter([
                        ($settings->address ?? null) ? $settings->address : null,
                        ($settings->city ?? null) ? $settings->city : null,
                    ]);
                @endphp
                @if(!empty($contactBits))
                    <span>{{ implode(', ', $contactBits) }}@if($settings->postal_code) &middot; P.O. Box {{ $settings->postal_code }}@endif</span>
                @endif
            </div>
            <div class="pass-contact-line">
                @if($settings->phone)<span>Tel: {{ $settings->phone }}</span>@endif
                @if($settings->phone && $settings->email)<span class="pass-contact-sep">|</span>@endif
                @if($settings->email)<span>{{ $settings->email }}</span>@endif
            </div>
        </div>
    </div>

    {{-- Bus pass label --}}
    <div class="pass-label-bar">
        <span class="pass-label-icon">&#9679;</span>
        School Bus Pass
    </div>

    {{-- Body: photo + info --}}
    <div class="pass-body">
        <div class="pass-photo">
            @if($busPayment->student->profile_photo)
                <img src="{{ Storage::url($busPayment->student->profile_photo) }}" alt="{{ $busPayment->student->name }}">
            @else
                {{ strtoupper(substr($busPayment->student->name, 0, 1)) }}
            @endif
        </div>
        <div class="pass-info">
            <div class="pass-student-name">{{ $busPayment->student->name }}</div>
            <div class="pass-student-id">ID: {{ $busPayment->student->student_id_number ?? 'N/A' }}</div>
            <div class="pass-detail-grid">
                <div class="pass-detail-item">
                    <div class="pass-detail-label">Grade</div>
                    <div class="pass-detail-value">{{ $busPayment->student->grade->name ?? 'N/A' }}</div>
                </div>
                <div class="pass-detail-item">
                    <div class="pass-detail-label">Expires</div>
                    <div class="pass-detail-value">{{ $expiryDate->format('d M Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Route + validity strip --}}
    <div class="pass-strip">
        <div class="pass-route">
            <div class="pass-route-label">Route</div>
            <div class="pass-route-value">{{ $busPayment->busFareStructure->route_name }}</div>
        </div>
        <div class="pass-validity">
            <div class="pass-validity-label">Valid For</div>
            <div class="pass-validity-value">
                @if($busPayment->month)
                    {{ $busPayment->month }} {{ $busPayment->year }}
                @else
                    Full Term {{ $busPayment->year }}
                @endif
            </div>
        </div>
    </div>

    {{-- Footer: status + QR --}}
    <div class="pass-footer">
        <div class="pass-status-area">
            @if($busPayment->payment_status === 'paid')
                <div class="pass-status-badge pass-status-valid">&#10003; Valid</div>
            @elseif($busPayment->payment_status === 'partial')
                <div class="pass-status-badge pass-status-partial">Partial</div>
            @endif
            <div class="pass-verification">BUS-{{ str_pad($busPayment->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="pass-qr">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BUS-PASS-{{ $busPayment->id }}-{{ $busPayment->student->student_id_number }}" alt="QR">
        </div>
    </div>

    <div class="pass-bottom-note">
        Present this pass when boarding. Valid for {{ $busPayment->busFareStructure->route_name }} route only.
    </div>
</div>
