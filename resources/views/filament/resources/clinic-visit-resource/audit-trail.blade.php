@php
    $labels = [
        'clinic_visit.created' => ['Recorded',  '#0e2746'],
        'clinic_visit.updated' => ['Edited',    '#b08a3e'],
        'clinic_visit.deleted' => ['Deleted',   '#8b1a1a'],
    ];
    $prettyField = fn ($k) => ucfirst(str_replace('_', ' ', $k));
@endphp

<div style="font-family: -apple-system, system-ui, sans-serif;">
    @forelse ($entries as $e)
        @php
            [$eventLabel, $eventColor] = $labels[$e->event] ?? [ucfirst($e->event), '#6b7280'];
            $old = $e->old_values ? json_decode($e->old_values, true) : [];
            $new = $e->new_values ? json_decode($e->new_values, true) : [];
            $fields = array_unique(array_merge(array_keys($old), array_keys($new)));
        @endphp
        <div style="margin-bottom: 14px; padding: 12px 14px; background: #fff; border: 1px solid #e5e7eb; border-left: 3px solid {{ $eventColor }};">
            <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px;">
                <div>
                    <span style="font-size: 10px; letter-spacing: .18em; text-transform: uppercase; color: {{ $eventColor }}; font-weight: 600;">{{ $eventLabel }}</span>
                    <span style="margin-left: 8px; font-family: 'EB Garamond', Georgia, serif; font-size: 15px; color: #0e2746;">{{ $e->actor ?? 'system' }}</span>
                </div>
                <div style="font-size: 12px; color: #6b7280; font-family: 'EB Garamond', Georgia, serif; font-style: italic;">
                    {{ \Illuminate\Support\Carbon::parse($e->created_at)->format('D d M Y, H:i') }}
                    @if ($e->ip_address)
                        · <span style="font-family: ui-monospace, monospace;">{{ $e->ip_address }}</span>
                    @endif
                </div>
            </div>

            @if (empty($fields))
                <div style="color: #9ca3af; font-style: italic; font-size: 12px;">(no field-level detail recorded)</div>
            @else
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead style="background: #f9fafb;">
                        <tr>
                            <th style="text-align: left; padding: 6px 10px; font-size: 10px; letter-spacing: .14em; text-transform: uppercase; color: #6b7280; font-weight: 600;">Field</th>
                            <th style="text-align: left; padding: 6px 10px; font-size: 10px; letter-spacing: .14em; text-transform: uppercase; color: #6b7280; font-weight: 600;">From</th>
                            <th style="text-align: left; padding: 6px 10px; font-size: 10px; letter-spacing: .14em; text-transform: uppercase; color: #6b7280; font-weight: 600;">To</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fields as $f)
                            <tr style="border-top: 1px solid #f3f4f6;">
                                <td style="padding: 6px 10px; color: #4b5563;">{{ $prettyField($f) }}</td>
                                <td style="padding: 6px 10px; color: #b91c1c;"><s>{{ $old[$f] ?? '—' }}</s></td>
                                <td style="padding: 6px 10px; color: #059669;">{{ $new[$f] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @empty
        <div style="padding: 32px; text-align: center; color: #9ca3af; font-style: italic; font-family: 'EB Garamond', Georgia, serif;">
            No audit entries yet for this visit.
        </div>
    @endforelse
</div>
